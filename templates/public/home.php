<?php
/** @var int $nurseryCount */
/** @var list<array<string, mixed>> $featured */
?>
<section class="hero">
    <div class="container">
        <div>
            <span class="eyebrow">Florida's nursery platform</span>
            <h1>Every great Florida garden starts at a local nursery.</h1>
            <p class="lead">NurseryNest puts Florida's independent plant nurseries online. Browse their plants, order from any of them with one account, and pick up, get delivery or have it shipped.</p>
            <div class="actions">
                <a class="btn" href="/nurseries">Find a nursery</a>
                <a class="btn btn-outline" href="/signup">Create a free account</a>
            </div>
            <div class="stat-row">
                <div class="stat"><strong><?= e($nurseryCount) ?></strong><span class="muted">partner nurseries</span></div>
                <div class="stat"><strong>67</strong><span class="muted">Florida counties served</span></div>
                <div class="stat"><strong>1</strong><span class="muted">account for all of them</span></div>
            </div>
        </div>
        <?php require __DIR__ . '/../partials/hero-art.php'; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <span class="eyebrow">Why NurseryNest</span>
        <h2>The best way to shop Florida nurseries</h2>
        <div class="grid grid-3">
            <div class="card">
                <div class="icon-badge"><?php require __DIR__ . '/../partials/leaf.php'; ?></div>
                <h3>Grown for Florida</h3>
                <p class="muted">Every plant comes from a Florida nursery that knows our heat, humidity, sandy soil and hurricane season.</p>
            </div>
            <div class="card">
                <div class="icon-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l3 12h11l2-8H6.5"/></svg></div>
                <h3>One cart, many nurseries</h3>
                <p class="muted">Fill one cart from several nurseries. Each nursery is paid directly, and you check out once.</p>
            </div>
            <div class="card">
                <div class="icon-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7h11v10H3zM14 10h4l3 3v4h-7"/><circle cx="7" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/></svg></div>
                <h3>Pickup, delivery or shipping</h3>
                <p class="muted">Choose how each order reaches you from the options your nursery offers.</p>
            </div>
        </div>
    </div>
</section>

<?php if ($featured): ?>
<section class="section section-alt">
    <div class="container">
        <span class="eyebrow">Partner nurseries</span>
        <h2>Meet a few of our nurseries</h2>
        <div class="grid grid-3">
            <?php foreach ($featured as $n): ?>
                <a class="card card-link nursery-card" href="/n/<?= e(rawurlencode($n['slug'])) ?>">
                    <div class="swatch"></div>
                    <h3><?= e($n['name']) ?></h3>
                    <p class="small muted"><?= e($n['city']) ?>, <?= e($n['county']) ?> County</p>
                    <p class="small"><?= e($n['description']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
        <p><a href="/nurseries">See all partner nurseries →</a></p>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container card">
        <h2>Run a nursery in Florida?</h2>
        <p class="muted">Get your own branded storefront, inventory tools, online payments to your own account, and sales tax tracking, all in one place.</p>
        <div class="form-actions">
            <a class="btn" href="/contact">Talk to us</a>
            <a class="btn btn-outline" href="/nursery/login">Nursery log in</a>
        </div>
    </div>
</section>
