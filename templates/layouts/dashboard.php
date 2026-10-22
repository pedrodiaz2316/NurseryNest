<?php

use NurseryNest\Auth\NurseryAuth;
use NurseryNest\Auth\Policy;
use NurseryNest\Core\Database;
use NurseryNest\Repository\NurseryRepository;

/** @var string $content */
$user = NurseryAuth::user();
$nursery = (new NurseryRepository(Database::connection()))->findById($user['nursery_id']);
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
        <a class="brand" href="/dashboard">
            <?php if (!empty($nursery['logo_path'])): ?>
                <img class="brand-logo" src="<?= e($nursery['logo_path']) ?>" alt="<?= e($nursery['name']) ?>">
            <?php else: ?>
                <span class="brand-mark"><?= e(mb_strtoupper(mb_substr($nursery['name'], 0, 1))) ?></span>
                <?= e($nursery['name']) ?>
            <?php endif; ?>
        </a>
        <nav class="site-nav" aria-label="Account">
            <a href="/n/<?= e(rawurlencode($nursery['slug'])) ?>" target="_blank" rel="noopener">View public page</a>
            <form method="post" action="/nursery/logout"><?= csrf_field() ?><button class="linklike" type="submit">Log out</button></form>
        </nav>
    </div>
</header>

<div class="dash">
    <aside class="dash-side">
        <nav aria-label="Dashboard">
            <a href="/dashboard"<?= current_attr('/dashboard') ?>>Overview</a>
            <?php if (Policy::can($user['role'], Policy::VIEW_INVENTORY)): ?>
                <a href="/dashboard/inventory"<?= current_attr('/dashboard/inventory', true) ?>>Inventory</a>
            <?php endif; ?>
        </nav>
        <p class="who">
            Signed in as<br><strong><?= e($user['email']) ?></strong><br>
            <?= e(Policy::ROLE_LABELS[$user['role']] ?? $user['role']) ?>
        </p>
    </aside>
    <main class="dash-main">
        <?php require __DIR__ . '/../partials/flashes.php'; ?>
        <?= $content ?>
    </main>
</div>
</body>
</html>
