<?php
/** @var array<string, mixed> $nursery */
/** @var list<array<string, mixed>> $items */
/** @var list<string> $categories */
/** @var string $search */
/** @var string $category */
$base = '/n/' . rawurlencode($nursery['slug']);
$hours = json_decode((string) ($nursery['hours'] ?? ''), true) ?: [];
?>
<section class="store-banner">
    <div class="container">
        <h1><?= e($nursery['name']) ?></h1>
        <p><?= e($nursery['description']) ?></p>
        <div class="store-meta">
            <span><?= e($nursery['address']) ?>, <?= e($nursery['city']) ?>, FL <?= e($nursery['zip']) ?></span>
            <?php if ($nursery['phone']): ?><span><?= e($nursery['phone']) ?></span><?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require __DIR__ . '/../partials/flashes.php'; ?>
        <div class="dash-head">
            <h2>Plants and products</h2>
            <span class="muted small"><?= count($items) ?> item<?= count($items) === 1 ? '' : 's' ?></span>
        </div>

        <form class="filters" method="get" action="<?= e($base) ?>" role="search">
            <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search by name or botanical name" aria-label="Search">
            <select name="category" aria-label="Category" data-autosubmit>
                <option value="">All categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= e($c) ?>"<?= $c === $category ? ' selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn" type="submit">Search</button>
        </form>

        <?php if (!$items): ?>
            <p class="empty">No items match your search.</p>
        <?php else: ?>
            <div class="grid grid-4">
                <?php foreach ($items as $item): ?>
                    <a class="card card-link plant-card" href="<?= e($base) ?>/item/<?= (int) $item['id'] ?>">
                        <div class="plant-art"><?php require __DIR__ . '/../partials/plant-art.php'; ?></div>
                        <div class="body">
                            <span class="small muted"><?= e($item['category']) ?></span>
                            <h3><?= e($item['name']) ?></h3>
                            <?php if ($item['genus']): ?>
                                <span class="botanical"><?= e($item['genus'] . ' ' . $item['species']) ?></span>
                            <?php endif; ?>
                            <?php if ($item['florida_only']): ?><span><span class="tag tag-warn">Florida only</span></span><?php endif; ?>
                            <span class="price"><?= money($item['price']) ?></span>
                            <span class="small muted"><?= (int) $item['quantity'] > 0 ? (int) $item['quantity'] . ' in stock' : 'Out of stock' ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($hours): ?>
            <div class="card" id="hours">
                <h3>Opening hours</h3>
                <ul class="hours-list">
                    <?php foreach ($hours as $days => $time): ?>
                        <li><span><?= e($days) ?></span><span><?= e($time) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>
