<?php

use NurseryNest\Auth\ClientAuth;

/** @var string $content */
/** @var array<string, mixed> $nursery */
$client = ClientAuth::user();
$base = '/n/' . rawurlencode($nursery['slug']);
?>
<!doctype html>
<html lang="en">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
<link rel="stylesheet" href="/theme/<?= e(rawurlencode($nursery['slug'])) ?>.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a class="brand" href="<?= e($base) ?>">
            <?php if (!empty($nursery['logo_path'])): ?>
                <img class="brand-logo" src="<?= e($nursery['logo_path']) ?>" alt="<?= e($nursery['name']) ?>">
            <?php else: ?>
                <span class="brand-mark"><?= e(mb_strtoupper(mb_substr($nursery['name'], 0, 1))) ?></span>
                <?= e($nursery['name']) ?>
            <?php endif; ?>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button>
        <nav class="site-nav" id="site-nav" aria-label="Main">
            <a href="<?= e($base) ?>">Shop</a>
            <a href="/nurseries">All nurseries</a>
            <?php if ($client): ?>
                <a href="/account">My account</a>
            <?php else: ?>
                <a href="/login">Log in</a>
            <?php endif; ?>
            <a class="small muted" href="/">Powered by NurseryNest</a>
        </nav>
    </div>
</header>

<main>
    <?= $content ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
