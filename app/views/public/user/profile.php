<?php
/**
 * @var array<string, mixed> $profile
 * @var string $csrf_token
 */
$p = $profile ?? [];
?>
<div class="container page">
    <h1 class="page__title">My Profile</h1>
    <div class="page__split">
        <form action="/profile" method="post" enctype="multipart/form-data" class="form panel">
            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
            <div class="profile-avatar">
                <img src="<?= e(!empty($p['avatar']) ? img($p['avatar']) : '/assets/images/avatar.svg') ?>"
                     onerror="this.src='/assets/images/avatar.svg'" alt="Avatar">
                <label class="field"><span>Change avatar</span><input type="file" name="avatar" accept="image/*"></label>
            </div>
            <label class="field"><span>Display Name</span>
                <input type="text" name="display_name" value="<?= e($p['display_name'] ?? '') ?>"></label>
            <label class="field"><span>Bio</span>
                <textarea name="bio" rows="4"><?= e($p['bio'] ?? '') ?></textarea></label>
            <button type="submit" class="btn btn--primary">Save Changes</button>
        </form>

        <div class="panel">
            <h3 class="panel__title">Account</h3>
            <dl class="facts">
                <dt>Username</dt><dd><?= e($p['username'] ?? '') ?></dd>
                <dt>Email</dt><dd><?= e($p['email'] ?? '') ?></dd>
                <dt>Role</dt><dd><?= e(ucfirst(str_replace('_', ' ', $p['role'] ?? 'user'))) ?></dd>
                <dt>Member since</dt><dd><?= e(!empty($p['created_at']) ? date('M j, Y', strtotime($p['created_at'])) : '—') ?></dd>
                <?php if (!empty($p['last_login_at'])): ?>
                    <dt>Last login</dt><dd><?= e(time_ago($p['last_login_at'])) ?></dd>
                <?php endif; ?>
            </dl>
            <div class="quick-links">
                <a class="btn btn--ghost" href="/bookmarks">My Bookmarks</a>
                <a class="btn btn--ghost" href="/history">Watch History</a>
            </div>
        </div>
    </div>
</div>
