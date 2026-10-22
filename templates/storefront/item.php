<?php
/** @var array<string, mixed> $nursery */
/** @var array<string, mixed> $item */
$base = '/n/' . rawurlencode($nursery['slug']);
?>
<section class="section">
    <div class="container">
        <p><a href="<?= e($base) ?>">← Back to <?= e($nursery['name']) ?></a></p>
        <div class="grid" id="item-detail">
            <div class="card plant-card">
                <div class="plant-art"><?php require __DIR__ . '/../partials/plant-art.php'; ?></div>
            </div>
            <div class="stack">
                <span class="eyebrow"><?= e($item['category']) ?></span>
                <h1><?= e($item['name']) ?></h1>
                <?php if ($item['genus']): ?>
                    <p class="botanical"><?= e($item['genus'] . ' ' . $item['species']) ?></p>
                <?php endif; ?>
                <p class="price"><strong><?= money($item['price']) ?></strong></p>
                <p><?= nl2br(e($item['description'])) ?></p>

                <dl class="facts">
                    <dt>Availability</dt>
                    <dd><?= (int) $item['quantity'] > 0 ? (int) $item['quantity'] . ' in stock' : 'Out of stock' ?></dd>
                    <?php if ($item['days_at_nursery'] !== null): ?>
                        <dt>At the nursery</dt>
                        <dd><?= (int) $item['days_at_nursery'] ?> days</dd>
                    <?php endif; ?>
                    <?php foreach ($item['attributes'] as $attr): ?>
                        <dt><?= e($attr['attribute_name']) ?></dt>
                        <dd><?= e($attr['attribute_value']) ?></dd>
                    <?php endforeach; ?>
                    <?php if ($item['florida_only']): ?>
                        <dt>Shipping</dt>
                        <dd><span class="tag tag-warn">Florida only</span></dd>
                    <?php endif; ?>
                </dl>

                <p class="small muted">Online ordering is coming soon. Call <?= e($nursery['phone'] ?? 'the nursery') ?> to reserve this item.</p>
            </div>
        </div>
    </div>
</section>
