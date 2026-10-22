<?php /** @var string $email */ ?>
<div class="container">
    <div class="card form-card">
        <h1>Log in</h1>
        <p class="muted">One NurseryNest account works at every nursery.</p>
        <form method="post" action="/login" novalidate>
            <?= csrf_field() ?>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" autocomplete="current-password" required>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Log in</button>
                <a href="/signup">Create an account</a>
            </div>
        </form>
        <p class="small muted">Work at a nursery? <a href="/nursery/login">Use the nursery login.</a></p>
    </div>
</div>
