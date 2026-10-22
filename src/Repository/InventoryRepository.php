<?php

declare(strict_types=1);

namespace NurseryNest\Repository;

/**
 * One nursery's inventory (INV-1 to INV-6). Every query is scoped by nursery_id.
 */
final class InventoryRepository extends TenantRepository
{
    private const COLUMNS = 'id, nursery_id, name, category, genus, species, description, quantity, price,
                             date_received, florida_only, is_active, updated_at,
                             DATEDIFF(CURRENT_DATE, date_received) AS days_at_nursery';

    /** @return list<array<string, mixed>> */
    public function all(string $search = '', string $category = ''): array
    {
        $sql = 'SELECT ' . self::COLUMNS . ' FROM inventory_items WHERE nursery_id = ? AND is_active = 1';
        $args = [$this->nurseryId];

        if ($search !== '') {
            $sql .= ' AND (name LIKE ? OR genus LIKE ? OR species LIKE ?)';
            $like = '%' . $search . '%';
            array_push($args, $like, $like, $like);
        }
        if ($category !== '') {
            $sql .= ' AND category = ?';
            $args[] = $category;
        }

        $stmt = $this->db->prepare($sql . ' ORDER BY category, name');
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ' . self::COLUMNS . ' FROM inventory_items WHERE id = ? AND nursery_id = ? AND is_active = 1'
        );
        $stmt->execute([$id, $this->nurseryId]);

        return $stmt->fetch() ?: null;
    }

    /** @return list<string> */
    public function categories(): array
    {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT category FROM inventory_items WHERE nursery_id = ? AND is_active = 1 ORDER BY category'
        );
        $stmt->execute([$this->nurseryId]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /** @param array<string, mixed> $data */
    public function create(array $data, float $price): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO inventory_items
                (nursery_id, name, category, genus, species, description, quantity, price, date_received, florida_only)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $this->nurseryId,
            $data['name'],
            $data['category'],
            $data['genus'],
            $data['species'],
            $data['description'],
            $data['quantity'],
            $price,
            $data['date_received'],
            $data['florida_only'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Updates everything except the price; prices change through updatePrice() (AUTH-9).
     *
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE inventory_items
             SET name = ?, category = ?, genus = ?, species = ?, description = ?, quantity = ?,
                 date_received = ?, florida_only = ?
             WHERE id = ? AND nursery_id = ? AND is_active = 1'
        );
        $stmt->execute([
            $data['name'],
            $data['category'],
            $data['genus'],
            $data['species'],
            $data['description'],
            $data['quantity'],
            $data['date_received'],
            $data['florida_only'],
            $id,
            $this->nurseryId,
        ]);

        return $stmt->rowCount() > 0 || $this->find($id) !== null;
    }

    public function updatePrice(int $id, float $price): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE inventory_items SET price = ? WHERE id = ? AND nursery_id = ? AND is_active = 1'
        );
        $stmt->execute([$price, $id, $this->nurseryId]);

        return $stmt->rowCount() > 0 || $this->find($id) !== null;
    }

    /** Removal is a soft delete so past orders keep their link. */
    public function remove(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE inventory_items SET is_active = 0 WHERE id = ? AND nursery_id = ?');
        $stmt->execute([$id, $this->nurseryId]);

        return $stmt->rowCount() > 0;
    }

    /** @return list<array{attribute_name: string, attribute_value: string}> */
    public function attributes(int $itemId): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.attribute_name, a.attribute_value
             FROM item_attributes a
             JOIN inventory_items i ON i.id = a.item_id
             WHERE a.item_id = ? AND i.nursery_id = ?
             ORDER BY a.attribute_name'
        );
        $stmt->execute([$itemId, $this->nurseryId]);

        return $stmt->fetchAll();
    }

    /**
     * Replaces an item's extra fields (INV-5).
     *
     * @param array<string, string> $attributes name => value
     */
    public function replaceAttributes(int $itemId, array $attributes): void
    {
        if ($this->find($itemId) === null) {
            return;
        }

        $this->db->beginTransaction();
        try {
            $this->db->prepare('DELETE FROM item_attributes WHERE item_id = ?')->execute([$itemId]);
            $insert = $this->db->prepare(
                'INSERT INTO item_attributes (item_id, attribute_name, attribute_value) VALUES (?, ?, ?)'
            );
            foreach ($attributes as $name => $value) {
                $insert->execute([$itemId, $name, $value]);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** @return array{items: int, units: int, low_stock: int} */
    public function summary(int $lowStockThreshold = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) AS items, COALESCE(SUM(quantity), 0) AS units,
                    COALESCE(SUM(quantity < ?), 0) AS low_stock
             FROM inventory_items WHERE nursery_id = ? AND is_active = 1'
        );
        $stmt->execute([$lowStockThreshold, $this->nurseryId]);
        $row = $stmt->fetch();

        return ['items' => (int) $row['items'], 'units' => (int) $row['units'], 'low_stock' => (int) $row['low_stock']];
    }
}
