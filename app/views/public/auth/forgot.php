<?php /** @var string $csrf_token */ ?>
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Reset password</h1>
        <p class="auth-card__sub">Enter your email and we'll send a reset link.</p>
        <form action="/forgot-password" method="post" class="form">
            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
            <label class="field">
                <span>Email</span>
                <input type="email" name="email" required autofocus>
            </label>
            <button type="submit" class="btn btn--primary btn--block">Send reset link</button>
        </form>
        <p class="auth-card__foot"><a href="/login">Back to login</a></p>
    </div>
</div>
