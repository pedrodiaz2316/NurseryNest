<?php

use NurseryNest\Auth\NurseryAuth;
use NurseryNest\Auth\Policy;

/** @var array{items: int, units: int, low_stock: int} $summary */
$user = NurseryAuth::user();
?>
<div class="dash-head">
    <h1>Overview</h1>
    <?php if (Policy::can($user['role'], Policy::MANAGE_INVENTORY)): ?>
        <a class="btn" href="/dashboard/inventory/new">Add item</a>
    <?php endif; ?>
</div>

<div class="grid grid-3">
    <div class="card stat"><span class="muted">Items listed</span><strong><?= e($summary['items']) ?></strong></div>
    <div class="card stat"><span class="muted">Units in stock</span><strong><?= e($summary['units']) ?></strong></div>
    <div class="card stat"><span class="muted">Low stock (under 10)</span><strong><?= e($summary['low_stock']) ?></strong></div>
</div>

<div class="card" id="next-up">
    <h3>Coming next</h3>
    <p class="muted">Orders, clients, staff, users, settings and tax reports will appear here as they are built.</p>
</div>
