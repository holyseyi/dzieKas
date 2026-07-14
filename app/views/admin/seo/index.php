<?php /** @var array<string,mixed> $sitemap_url */ ?>
<div class="admin-toolbar">
    <a class="btn btn--ghost" href="<?= e($sitemap_url) ?>" target="_blank">View Sitemap</a>
    <a class="btn btn--ghost" href="<?= e($robots_url) ?>" target="_blank">View Robots.txt</a>
    <a class="btn btn--ghost" href="<?= e($feed_url) ?>" target="_blank">View RSS Feed</a>
</div>

<section class="panel">
    <h2 class="panel__title">SEO Links</h2>
    <ul class="link-list">
        <li><a class="link-row" href="<?= e($sitemap_url) ?>" target="_blank"><?= e($sitemap_url) ?></a></li>
        <li><a class="link-row" href="<?= e($robots_url) ?>" target="_blank"><?= e($robots_url) ?></a></li>
        <li><a class="link-row" href="<?= e($feed_url) ?>" target="_blank"><?= e($feed_url) ?></a></li>
    </ul>
</section>
