<?php

use NurseryNest\Auth\ClientAuth;

/** @var array<string, string> $old */
?>
<div class="container">
    <div class="card form-card">
        <h1>Create your account</h1>
        <p class="muted">Browse every nursery together, keep one cart and see all your orders in one place.</p>
        <form method="post" action="/signup" novalidate>
            <?= csrf_field() ?>
            <div class="field">
                <label for="name">Full name</label>
                <input id="name" type="text" name="name" value="<?= e($old['name'] ?? '') ?>" autocomplete="name" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email" required>
            </div>
            <div class="field">
                <label for="phone">Phone (optional)</label>
                <input id="phone" type="tel" name="phone" value="<?= e($old['phone'] ?? '') ?>" autocomplete="tel">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" autocomplete="new-password" minlength="<?= ClientAuth::MIN_PASSWORD_LENGTH ?>" required>
                <p class="small muted">At least <?= ClientAuth::MIN_PASSWORD_LENGTH ?> characters.</p>
            </div>
            <div class="field">
                <label for="password_confirm">Confirm password</label>
                <input id="password_confirm" type="password" name="password_confirm" autocomplete="new-password" required>
            </div>
            <div class="form-actions">
                <button class="btn" type="submit">Create account</button>
                <a href="/login">I already have an account</a>
            </div>
        </form>
    </div>
</div>
