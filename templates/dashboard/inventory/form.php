<?php

use NurseryNest\Auth\NurseryAuth;
use NurseryNest\Auth\Policy;

/** @var array<string, mixed>|null $item */
/** @var array<string, mixed> $old */
/** @var string $attributes */
/** @var array<string, string> $errors */
$user = NurseryAuth::user();
$canPrice = Policy::can($user['role'], Policy::CHANGE_PRICES);
$errors = $errors ?? [];
$isNew = $item === null;
$action = $isNew ? '/dashboard/inventory' : '/dashboard/inventory/' . (int) $item['id'];
$val = fn (string $key) => e($old[$key] ?? '');
$err = fn (string $key) => isset($errors[$key]) ? '<p class="field-error">' . e($errors[$key]) . '</p>' : '';
?>
<div class="dash-head">
    <h1><?= $isNew ? 'Add item' : 'Edit item' ?></h1>
    <a href="/dashboard/inventory">← Back to inventory</a>
</div>

<?php if ($errors): ?>
    <div class="flash flash-error" role="alert">Please fix the highlighted fields.</div>
<?php endif; ?>

<form class="card" method="post" action="<?= e($action) ?>" novalidate>
    <?= csrf_field() ?>
    <div class="form-grid">
        <div class="field">
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="<?= $val('name') ?>" maxlength="150" required>
            <?= $err('name') ?>
        </div>
        <div class="field">
            <label for="category">Category</label>
            <input id="category" type="text" name="category" value="<?= $val('category') ?>" maxlength="80" placeholder="Palms, Natives, Supplies…" required>
            <?= $err('category') ?>
        </div>
        <div class="field">
            <label for="genus">Genus</label>
            <input id="genus" type="text" name="genus" value="<?= $val('genus') ?>" maxlength="80" placeholder="For plants, e.g. Citrus">
            <?= $err('genus') ?>
        </div>
        <div class="field">
            <label for="species">Species</label>
            <input id="species" type="text" name="species" value="<?= $val('species') ?>" maxlength="80" placeholder="e.g. meyeri">
        </div>
        <div class="field">
            <label for="quantity">Quantity in stock</label>
            <input id="quantity" type="number" name="quantity" min="0" step="1" value="<?= $val('quantity') ?: '0' ?>" required>
            <?= $err('quantity') ?>
        </div>
        <div class="field">
            <label for="date_received">Date received</label>
            <input id="date_received" type="date" name="date_received" value="<?= $val('date_received') ?>">
            <?= $err('date_received') ?>
        </div>
        <?php if ($isNew): ?>
            <div class="field">
                <label for="price">Price (USD)</label>
                <?php if ($canPrice): ?>
                    <input id="price" type="number" name="price" min="0.01" step="0.01" value="<?= $val('price') ?>" required>
                    <?= $err('price') ?>
                <?php else: ?>
                    <input id="price" type="text" value="Set by a Nursery Manager" disabled>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= $val('description') ?></textarea>
    </div>

    <div class="field">
        <label for="attributes">Extra fields</label>
        <textarea id="attributes" name="attributes" placeholder="One per line, for example&#10;Mature height: 20-30 ft&#10;Light: Full sun"><?= e($attributes) ?></textarea>
        <p class="small muted">One field per line as <em>name: value</em>.</p>
    </div>

    <div class="field">
        <label class="checkbox">
            <input type="checkbox" name="florida_only" value="1"<?= !empty($old['florida_only']) ? ' checked' : '' ?>>
            Florida only (never ship this item outside Florida)
        </label>
    </div>

    <div class="form-actions">
        <button class="btn" type="submit"><?= $isNew ? 'Add item' : 'Save changes' ?></button>
        <a href="/dashboard/inventory">Cancel</a>
    </div>
</form>

<?php if (!$isNew): ?>
    <div class="grid grid-3" id="item-actions">
        <div class="card">
            <h3>Price</h3>
            <?php if ($canPrice): ?>
                <form method="post" action="/dashboard/inventory/<?= (int) $item['id'] ?>/price" class="form-actions">
                    <?= csrf_field() ?>
                    <label for="new-price" class="visually-hidden">New price</label>
                    <input id="new-price" type="number" name="price" min="0.01" step="0.01" value="<?= e((float) $item['price'] > 0 ? $item['price'] : '') ?>" required>
                    <button class="btn btn-small" type="submit">Update price</button>
                </form>
            <?php else: ?>
                <p><strong><?= (float) $item['price'] > 0 ? money($item['price']) : 'Not set' ?></strong></p>
                <p class="small muted">Only a Nursery Manager can change prices.</p>
            <?php endif; ?>
        </div>
        <div class="card">
            <h3>Remove item</h3>
            <p class="small muted">Removes it from your inventory and public page. Past orders keep their record.</p>
            <form method="post" action="/dashboard/inventory/<?= (int) $item['id'] ?>/remove" data-confirm="Remove <?= e($item['name']) ?>?">
                <?= csrf_field() ?>
                <button class="btn btn-danger btn-small" type="submit">Remove</button>
            </form>
        </div>
    </div>
<?php endif; ?>
