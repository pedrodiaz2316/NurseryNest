<?php

declare(strict_types=1);

namespace NurseryNest\Repository;

use PDO;

/**
 * Public, read-only nursery data: profiles, themes and storefront listings (WEB-6, CLI-3).
 * Only active items with a price are shown publicly.
 */
final class NurseryRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /** @return list<array<string, mixed>> */
    public function allActive(): array
    {
        return $this->db->query(
            'SELECT n.id, n.slug, n.name, n.city, n.county, n.description,
                    t.primary_color, t.secondary_color
             FROM nurseries n
             LEFT JOIN nursery_themes t ON t.nursery_id = n.id
             WHERE n.is_active = 1
             ORDER BY n.name'
        )->fetchAll();
    }

    public function countActive(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM nurseries WHERE is_active = 1')->fetchColumn();
    }

    /** @return array<string, mixed>|null */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT n.*, t.primary_color, t.secondary_color, t.logo_path, t.banner_path, th.layout
             FROM nurseries n
             LEFT JOIN nursery_themes t ON t.nursery_id = n.id
             LEFT JOIN themes th ON th.id = t.theme_id
             WHERE n.slug = ? AND n.is_active = 1'
        );
        $stmt->execute([$slug]);

        return $stmt->fetch() ?: null;
    }

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT n.*, t.primary_color, t.secondary_color, t.logo_path, t.banner_path
             FROM nurseries n
             LEFT JOIN nursery_themes t ON t.nursery_id = n.id
             WHERE n.id = ?'
        );
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function publicItems(int $nurseryId, string $search = '', string $category = ''): array
    {
        $sql = 'SELECT id, name, category, genus, species, description, quantity, price, florida_only
                FROM inventory_items
                WHERE nursery_id = ? AND is_active = 1 AND price > 0';
        $args = [$nurseryId];

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

    /** @return list<string> */
    public function publicCategories(int $nurseryId): array
    {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT category FROM inventory_items
             WHERE nursery_id = ? AND is_active = 1 AND price > 0 ORDER BY category'
        );
        $stmt->execute([$nurseryId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /** @return array<string, mixed>|null */
    public function publicItem(int $nurseryId, int $itemId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, category, genus, species, description, quantity, price, florida_only,
                    DATEDIFF(CURRENT_DATE, date_received) AS days_at_nursery
             FROM inventory_items
             WHERE id = ? AND nursery_id = ? AND is_active = 1 AND price > 0'
        );
        $stmt->execute([$itemId, $nurseryId]);
        $item = $stmt->fetch();
        if (!$item) {
            return null;
        }

        $attrs = $this->db->prepare(
            'SELECT attribute_name, attribute_value FROM item_attributes WHERE item_id = ? ORDER BY attribute_name'
        );
        $attrs->execute([$itemId]);
        $item['attributes'] = $attrs->fetchAll();

        return $item;
    }

    /**
     * Combined search across every nursery (CLI-3). Only for logged-in clients (WEB-7).
     *
     * @return list<array<string, mixed>>
     */
    public function searchAllNurseries(string $search, string $category = '', int $limit = 60): array
    {
        $sql = 'SELECT i.id, i.name, i.category, i.genus, i.species, i.price, i.quantity, i.florida_only,
                       n.name AS nursery_name, n.slug AS nursery_slug, n.city AS nursery_city
                FROM inventory_items i
                JOIN nurseries n ON n.id = i.nursery_id
                WHERE n.is_active = 1 AND i.is_active = 1 AND i.price > 0';
        $args = [];

        if ($search !== '') {
            $sql .= ' AND (i.name LIKE ? OR i.genus LIKE ? OR i.species LIKE ? OR i.category LIKE ?)';
            $like = '%' . $search . '%';
            array_push($args, $like, $like, $like, $like);
        }
        if ($category !== '') {
            $sql .= ' AND i.category = ?';
            $args[] = $category;
        }

        $stmt = $this->db->prepare($sql . ' ORDER BY i.name, n.name LIMIT ' . max(1, $limit));
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    /** @return list<string> */
    public function allCategories(): array
    {
        return $this->db->query(
            'SELECT DISTINCT i.category FROM inventory_items i
             JOIN nurseries n ON n.id = i.nursery_id
             WHERE n.is_active = 1 AND i.is_active = 1 AND i.price > 0
             ORDER BY i.category'
        )->fetchAll(PDO::FETCH_COLUMN);
    }
}
