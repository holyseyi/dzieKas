<?php /** @var string $csrf_token */ ?>
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Welcome back</h1>
        <p class="auth-card__sub">Login to continue to <?= e($config['name']) ?></p>
        <form action="/login" method="post" class="form">
            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
            <label class="field">
                <span>Email or Username</span>
                <input type="text" name="identifier" required autofocus>
            </label>
            <label class="field">
                <span>Password</span>
                <input type="password" name="password" required>
            </label>
            <div class="form__row">
                <a href="/forgot-password" class="link">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn--primary btn--block">Login</button>
        </form>
        <p class="auth-card__foot">No account? <a href="/register">Create one</a></p>
    </div>
</div>
