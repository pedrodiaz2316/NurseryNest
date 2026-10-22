<?php
/** @var array<string, mixed> $profile */
/** @var list<array<string, mixed>> $addresses */
/** @var list<array<string, mixed>> $orders */
?>
<section class="section">
    <div class="container stack">
        <h1>Hello, <?= e($profile['name']) ?></h1>

        <div class="grid grid-3">
            <div class="card">
                <h3>Profile</h3>
                <dl class="facts">
                    <dt>Email</dt><dd><?= e($profile['email']) ?></dd>
                    <dt>Phone</dt><dd><?= e($profile['phone'] ?? '—') ?></dd>
                    <dt>Member since</dt><dd><?= e(date('F Y', strtotime($profile['created_at']))) ?></dd>
                </dl>
            </div>
            <div class="card">
                <h3>Saved addresses</h3>
                <?php if (!$addresses): ?>
                    <p class="muted">No saved addresses yet.</p>
                <?php endif; ?>
                <?php foreach ($addresses as $a): ?>
                    <p><strong><?= e($a['label'] ?? 'Address') ?></strong><br>
                        <?= e($a['address']) ?><br><?= e($a['city']) ?>, <?= e($a['state']) ?> <?= e($a['zip']) ?></p>
                <?php endforeach; ?>
            </div>
            <div class="card">
                <h3>Shop</h3>
                <p class="muted">Search plants across every partner nursery.</p>
                <a class="btn" href="/browse">Browse all nurseries</a>
            </div>
        </div>

        <h2>Order history</h2>
        <?php if (!$orders): ?>
            <p class="muted">You haven't placed any orders yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Order</th><th>Nursery</th><th>Date</th><th>Status</th><th class="num">Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>#<?= (int) $o['id'] ?></td>
                            <td><?= e($o['nursery_name']) ?></td>
                            <td><?= e(date('M j, Y', strtotime($o['created_at']))) ?></td>
                            <td><?= e(ucwords(str_replace('_', ' ', $o['status']))) ?></td>
                            <td class="num"><?= money($o['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
