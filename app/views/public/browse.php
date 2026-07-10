<?php
/**
 * Browse / listing page.
 *
 * @var string $heading
 * @var array<int, array<string, mixed>> $items
 * @var int $page
 * @var int $totalPages
 */
?>
<div class="container listing">
    <header class="listing__head">
        <h1 class="listing__title"><?= e($heading ?? 'Browse') ?></h1>
    </header>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            <p>No titles found here yet.</p>
            <a class="btn btn--primary" href="/">Back to Home</a>
        </div>
    <?php else: ?>
        <div class="poster-grid">
            <?php foreach ($items as $item): ?>
                <?php include __DIR__ . '/../partials/content-card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php include __DIR__ . '/../partials/pagination.php'; ?>
    <?php endif; ?>
</div>
