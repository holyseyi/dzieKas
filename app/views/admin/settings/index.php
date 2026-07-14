<?php /** @var array<int,array<string,mixed>> $settings */ ?>
<form action="/admin/settings" method="post" class="form">
    <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
    <section class="panel">
        <h2 class="panel__title">General</h2>
        <?php foreach (['site_name', 'site_tagline', 'site_description', 'contact_email'] as $key): ?>
            <div class="field">
                <label class="field__label"><?= e(ucwords(str_replace('_', ' ', $key))) ?></label>
                <input type="text" name="<?= e($key) ?>" class="field__input" value="<?= e($settings[array_search($key, array_column($settings, 'key'))]['value'] ?? '') ?>">
            </div>
        <?php endforeach; ?>
    </section>
    <section class="panel">
        <h2 class="panel__title">Options</h2>
        <?php foreach (['maintenance_mode', 'allow_registration', 'dark_mode_default'] as $key): ?>
            <div class="field">
                <label class="field__check">
                    <input type="checkbox" name="<?= e($key) ?>" value="1" <?= !empty($settings[array_search($key, array_column($settings, 'key'))]['value']) ? 'checked' : '' ?>>
                    <span><?= e(ucwords(str_replace('_', ' ', $key))) ?></span>
                </label>
            </div>
        <?php endforeach; ?>
    </section>
    <button type="submit" class="btn btn--primary">Save Settings</button>
</form>
