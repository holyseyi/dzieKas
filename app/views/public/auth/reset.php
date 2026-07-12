<?php /** @var string $csrf_token @var string $token */ ?>
<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-card__title">Set a new password</h1>
        <form action="/reset-password/<?= e($token) ?>" method="post" class="form">
            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
            <label class="field">
                <span>New Password</span>
                <input type="password" name="password" minlength="6" required autofocus>
            </label>
            <button type="submit" class="btn btn--primary btn--block">Reset password</button>
        </form>
        <p class="auth-card__foot"><a href="/login">Back to login</a></p>
    </div>
</div>
