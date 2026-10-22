<?php

use NurseryNest\Auth\ClientAuth;

/** @var string $content */
$client = ClientAuth::user();
?>
<!doctype html>
<html lang="en">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body>
<header class="site-header">
    <div class="container">
        <a class="brand" href="/">
            <span class="brand-mark"><?php require __DIR__ . '/../partials/leaf.php'; ?></span>
            NurseryNest
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button>
        <nav class="site-nav" id="site-nav" aria-label="Main">
            <a href="/about"<?= current_attr('/about') ?>>About</a>
            <a href="/specialty"<?= current_attr('/specialty') ?>>Our specialty</a>
            <a href="/features"<?= current_attr('/features') ?>>Features</a>
            <a href="/nurseries"<?= current_attr('/nurseries') ?>>Nurseries</a>
            <a href="/contact"<?= current_attr('/contact') ?>>Contact</a>
            <?php if ($client): ?>
                <a href="/browse"<?= current_attr('/browse') ?>>Browse plants</a>
                <a href="/account"<?= current_attr('/account') ?>>My account</a>
                <form method="post" action="/logout"><?= csrf_field() ?><button class="linklike site-nav-btn" type="submit">Log out</button></form>
            <?php else: ?>
                <a href="/login"<?= current_attr('/login') ?>>Log in</a>
                <a class="btn btn-small" href="/signup">Sign up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>
    <?php if (!isset($fullBleed)): ?><div class="container"><?php require __DIR__ . '/../partials/flashes.php'; ?></div><?php endif; ?>
    <?= $content ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
