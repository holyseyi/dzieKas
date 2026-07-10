<?php
/**
 * Public site layout.
 *
 * @var string $content  Rendered inner view HTML
 * @var array<string, mixed> $config
 * @var array<string, mixed>|null $user
 * @var array<string, string> $flash
 * @var bool $dark_mode
 */
$user = $user ?? null;
$flash = $flash ?? [];
$dark = !empty($dark_mode);
$isAdmin = $user && in_array($user['role_slug'] ?? ($user['role'] ?? ''), ['super_admin', 'admin', 'editor'], true);
$pageTitle = $title ?? $config['name'];
$navItems = [
    '/movies' => 'Movies',
    '/tv-series' => 'TV Series',
    '/anime' => 'Anime',
    '/k-dramas' => 'K-Dramas',
    '/documentaries' => 'Documentaries',
    '/trending' => 'Trending',
    '/latest' => 'Latest',
];
$currentPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/') ?: '/';
?>
<!DOCTYPE html>
<html lang="en" class="<?= $dark ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!empty($seo)): ?>
        <?= $seo ?>
    <?php else: ?>
        <title><?= e($pageTitle) ?></title>
        <meta name="description" content="<?= e($config['tagline']) ?>">
    <?php endif; ?>
    <link rel="icon" href="/assets/images/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container site-header__inner">
        <button class="nav-toggle" aria-label="Toggle menu" data-nav-toggle>☰</button>
        <a class="brand" href="/">
            <span class="brand__mark">Dzie</span><span class="brand__accent">Kas</span>
        </a>
        <nav class="main-nav" data-nav>
            <a href="/" class="<?= $currentPath === '/' ? 'is-active' : '' ?>">Home</a>
            <?php foreach ($navItems as $href => $label): ?>
                <a href="<?= e($href) ?>" class="<?= $currentPath === $href ? 'is-active' : '' ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <form class="search-box" action="/search" method="get" role="search">
            <input type="search" name="q" placeholder="Search movies, series, actors..."
                   value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off" data-search-input>
            <button type="submit" aria-label="Search">⌕</button>
            <div class="search-box__results" data-search-results></div>
        </form>
        <div class="header-actions">
            <form action="/toggle-dark-mode" method="post" class="inline-form">
                <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                <button type="submit" class="icon-btn" title="Toggle theme"><?= $dark ? '☀' : '☾' ?></button>
            </form>
            <?php if ($user): ?>
                <div class="dropdown" data-dropdown>
                    <button class="avatar-btn" data-dropdown-toggle>
                        <img src="<?= e($user['avatar'] ? img($user['avatar']) : '/assets/images/avatar.svg') ?>"
                             onerror="this.src='/assets/images/avatar.svg'" alt="Account">
                        <span><?= e($user['display_name'] ?? $user['username'] ?? 'Account') ?></span>
                    </button>
                    <div class="dropdown__menu">
                        <a href="/profile">Profile</a>
                        <a href="/bookmarks">Bookmarks</a>
                        <a href="/history">Watch History</a>
                        <?php if ($isAdmin): ?><a href="/admin">Admin Panel</a><?php endif; ?>
                        <a href="/logout">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a class="btn btn--ghost" href="/login">Login</a>
                <a class="btn btn--primary" href="/register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (!empty($flash)): ?>
    <div class="container">
        <?php foreach ($flash as $type => $message): ?>
            <div class="flash flash--<?= e($type) ?>" data-flash><?= e($message) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main class="site-main">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__col">
            <a class="brand" href="/"><span class="brand__mark">Dzie</span><span class="brand__accent">Kas</span></a>
            <p><?= e($config['tagline']) ?></p>
        </div>
        <div class="site-footer__col">
            <h4>Browse</h4>
            <a href="/movies">Movies</a>
            <a href="/tv-series">TV Series</a>
            <a href="/anime">Anime</a>
            <a href="/k-dramas">K-Dramas</a>
        </div>
        <div class="site-footer__col">
            <h4>Company</h4>
            <a href="/contact">Contact</a>
            <a href="/privacy-policy">Privacy Policy</a>
            <a href="/terms-of-service">Terms of Service</a>
            <a href="/dmca">DMCA</a>
        </div>
        <div class="site-footer__col">
            <h4>Newsletter</h4>
            <p>Get the latest releases in your inbox.</p>
            <form action="/newsletter" method="post" class="newsletter">
                <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                <input type="email" name="email" placeholder="you@example.com" required>
                <button type="submit" class="btn btn--primary">Subscribe</button>
            </form>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container">
            &copy; <?= date('Y') ?> <?= e($config['name']) ?>. All rights reserved.
        </div>
    </div>
</footer>

<script src="/assets/js/app.js"></script>
</body>
</html>
