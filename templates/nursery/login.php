<?php /** @var string $email */ ?>
<div class="container">
    <div class="card form-card">
        <span class="eyebrow">For nursery staff</span>
        <h1>Nursery login</h1>
        <form method="post" action="/nursery/login" novalidate>
            <?= csrf_field() ?>
            <div class="field">
                <label for="email">Work email</label>
                <input id="email" type="email" name="email" value="<?= e($email) ?>" autocomplete="username" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" autocomplete="current-password" required>
            </div>
            <button class="btn" type="submit">Log in</button>
        </form>
        <p class="small muted">Shopping for plants? <a href="/login">Use the customer login.</a></p>
    </div>
</div>
