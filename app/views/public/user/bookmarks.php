<?php /** @var array<int, array<string, mixed>> $bookmarks */ ?>
<div class="container listing">
    <header class="listing__head"><h1 class="listing__title">My Bookmarks</h1></header>
    <?php if (empty($bookmarks)): ?>
        <div class="empty-state">
            <p>You haven't bookmarked anything yet.</p>
            <a class="btn btn--primary" href="/">Browse titles</a>
        </div>
    <?php else: ?>
        <div class="poster-grid">
            <?php foreach ($bookmarks as $item): ?>
                <?php include __DIR__ . '/../../partials/content-card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
