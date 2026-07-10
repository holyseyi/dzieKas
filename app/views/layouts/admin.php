<?php
/**
 * Admin dashboard layout.
 *
 * @var string $content
 * @var array<string, mixed> $config
 * @var array<string, mixed>|null $user
 * @var array<string, string> $flash
 */
$user = $user ?? null;
$flash = $flash ?? [];
$pageTitle = $title ?? 'Admin';
$currentPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/') ?: '/';
$nav = [
    'Overview' => [
        '/admin' => ['Dashboard', '▤'],
    ],
    'Catalog' => [
        '/admin/content' => ['Content', '🎬'],
        '/admin/featured' => ['Featured', '★'],
        '/admin/genres' => ['Genres', '☰'],
        '/admin/countries' => ['Countries', '⚑'],
        '/admin/actors' => ['Actors', '☺'],
        '/admin/directors' => ['Directors', '⌥'],
    ],
    'Community' => [
        '/admin/users' => ['Users', '⚇'],
        '/admin/comments' => ['Comments', '💬'],
        '/admin/reports' => ['Reports', '⚑'],
    ],
    'System' => [
        '/admin/ads' => ['Advertisements', '❏'],
        '/admin/seo' => ['SEO', '🔍'],
        '/admin/settings' => ['Settings', '⚙'],
        '/admin/logs' => ['Audit Logs', '≣'],
        '/admin/backup' => ['Backup', '⤓'],
    ],
];
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · <?= e($config['name']) ?> Admin</title>
    <link rel="icon" href="/assets/images/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin">
<aside class="admin-sidebar" data-admin-sidebar>
    <a class="brand admin-sidebar__brand" href="/admin">
        <span class="brand__mark">Dzie</span><span class="brand__accent">Kas</span>
    </a>
    <nav class="admin-nav">
        <?php foreach ($nav as $group => $links): ?>
            <p class="admin-nav__group"><?= e($group) ?></p>
            <?php foreach ($links as $href => [$label, $icon]): ?>
                <a href="<?= e($href) ?>" class="<?= $currentPath === rtrim($href, '/') ? 'is-active' : '' ?>">
                    <span class="admin-nav__icon"><?= $icon ?></span><?= e($label) ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>
</aside>

<div class="admin-main">
    <header class="admin-topbar">
        <button class="icon-btn" data-admin-toggle aria-label="Toggle sidebar">☰</button>
        <h1 class="admin-topbar__title"><?= e($pageTitle) ?></h1>
        <div class="admin-topbar__actions">
            <a class="btn btn--ghost" href="/" target="_blank" rel="noopener">View Site ↗</a>
            <span class="admin-topbar__user"><?= e($user['display_name'] ?? $user['username'] ?? 'Admin') ?></span>
            <a class="btn btn--ghost" href="/logout">Logout</a>
        </div>
    </header>

    <?php if (!empty($flash)): ?>
        <?php foreach ($flash as $type => $message): ?>
            <div class="flash flash--<?= e($type) ?>" data-flash><?= e($message) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="admin-content">
        <?= $content ?>
    </div>
</div>

<script src="/assets/js/app.js"></script>
</body>
</html>
