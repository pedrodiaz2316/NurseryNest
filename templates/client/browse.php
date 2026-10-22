<?php
/** @var list<array<string, mixed>> $items */
/** @var list<string> $categories */
/** @var string $search */
/** @var string $category */
?>
<section class="section">
    <div class="container">
        <span class="eyebrow">All nurseries</span>
        <h1>Browse plants across Florida</h1>

        <form class="filters" method="get" action="/browse" role="search">
            <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search plants, genus or category" aria-label="Search">
            <select name="category" aria-label="Category" data-autosubmit>
                <option value="">All categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= e($c) ?>"<?= $c === $category ? ' selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn" type="submit">Search</button>
        </form>

        <?php if (!$items): ?>
            <p class="empty">Nothing matches that search yet.</p>
        <?php else: ?>
            <div class="grid grid-4">
                <?php foreach ($items as $item): ?>
                    <a class="card card-link plant-card" href="/n/<?= e(rawurlencode($item['nursery_slug'])) ?>/item/<?= (int) $item['id'] ?>">
                        <div class="plant-art"><?php require __DIR__ . '/../partials/plant-art.php'; ?></div>
                        <div class="body">
                            <span class="small muted"><?= e($item['nursery_name']) ?> · <?= e($item['nursery_city']) ?></span>
                            <h3><?= e($item['name']) ?></h3>
                            <?php if ($item['genus']): ?>
                                <span class="botanical"><?= e($item['genus'] . ' ' . $item['species']) ?></span>
                            <?php endif; ?>
                            <span class="price"><?= money($item['price']) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
