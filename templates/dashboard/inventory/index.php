<?php

use NurseryNest\Auth\NurseryAuth;
use NurseryNest\Auth\Policy;

/** @var list<array<string, mixed>> $items */
/** @var list<string> $categories */
/** @var string $search */
/** @var string $category */
$user = NurseryAuth::user();
$canManage = Policy::can($user['role'], Policy::MANAGE_INVENTORY);
?>
<div class="dash-head">
    <h1>Inventory</h1>
    <?php if ($canManage): ?><a class="btn" href="/dashboard/inventory/new">Add item</a><?php endif; ?>
</div>

<form class="filters" method="get" action="/dashboard/inventory" role="search">
    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search name, genus or species" aria-label="Search">
    <select name="category" aria-label="Category" data-autosubmit>
        <option value="">All categories</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= e($c) ?>"<?= $c === $category ? ' selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-outline" type="submit">Filter</button>
</form>

<?php if (!$items): ?>
    <p class="empty">No items yet<?= $canManage ? '. Add your first plant or product.' : '.' ?></p>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Item</th>
                <th>Category</th>
                <th class="num">Stock</th>
                <th class="num">Price</th>
                <th class="num">Days here</th>
                <?php if ($canManage): ?><th><span class="visually-hidden">Actions</span></th><?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <strong><?= e($item['name']) ?></strong>
                        <?php if ($item['genus']): ?><br><span class="botanical"><?= e($item['genus'] . ' ' . $item['species']) ?></span><?php endif; ?>
                        <?php if ($item['florida_only']): ?> <span class="tag tag-warn">Florida only</span><?php endif; ?>
                    </td>
                    <td><?= e($item['category']) ?></td>
                    <td class="num"><?= (int) $item['quantity'] ?></td>
                    <td class="num"><?= (float) $item['price'] > 0 ? money($item['price']) : '<span class="tag tag-warn">No price</span>' ?></td>
                    <td class="num"><?= $item['days_at_nursery'] !== null ? (int) $item['days_at_nursery'] : '—' ?></td>
                    <?php if ($canManage): ?>
                        <td class="num"><a class="btn btn-outline btn-small" href="/dashboard/inventory/<?= (int) $item['id'] ?>/edit">Edit</a></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
