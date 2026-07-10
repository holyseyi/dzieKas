<?php /** @var string $csrf_token */ ?>
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Create your account</h1>
        <p class="auth-card__sub">Join <?= e($config['name']) ?> today</p>
        <form action="/register" method="post" class="form">
            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
            <label class="field">
                <span>Username</span>
                <input type="text" name="username" minlength="3" maxlength="50" required autofocus>
            </label>
            <label class="field">
                <span>Email</span>
                <input type="email" name="email" required>
            </label>
            <label class="field">
                <span>Password</span>
                <input type="password" name="password" minlength="6" required>
            </label>
            <label class="field">
                <span>Confirm Password</span>
                <input type="password" name="password_confirm" minlength="6" required>
            </label>
            <button type="submit" class="btn btn--primary btn--block">Register</button>
        </form>
        <p class="auth-card__foot">Already have an account? <a href="/login">Login</a></p>
    </div>
</div>
