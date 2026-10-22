<?php /** @var list<array<string, mixed>> $nurseries */ ?>
<section class="section">
    <div class="container">
        <span class="eyebrow">Partner nurseries</span>
        <h1>Find a nursery near you</h1>
        <p class="muted">Visit any nursery's page to see its plants. <a href="/browse">Log in to search every nursery at once.</a></p>

        <?php if (!$nurseries): ?>
            <p class="empty">No nurseries yet.</p>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($nurseries as $n): ?>
                    <a class="card card-link nursery-card" href="/n/<?= e(rawurlencode($n['slug'])) ?>">
                        <div class="swatch"></div>
                        <h3><?= e($n['name']) ?></h3>
                        <p class="small muted"><?= e($n['city']) ?>, <?= e($n['county']) ?> County</p>
                        <p class="small"><?= e($n['description']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
