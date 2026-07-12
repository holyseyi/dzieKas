<?php /** @var array<int, array<string, mixed>> $history */ ?>
<div class="container listing">
    <header class="listing__head"><h1 class="listing__title">Watch History</h1></header>
    <?php if (empty($history)): ?>
        <div class="empty-state">
            <p>Your watch history is empty.</p>
            <a class="btn btn--primary" href="/">Start watching</a>
        </div>
    <?php else: ?>
        <div class="history-list">
            <?php foreach ($history as $h): ?>
                <a class="history-row" href="<?= e(content_url($h)) ?>">
                    <img src="<?= e(img($h['poster'] ?? null)) ?>" alt="<?= e($h['title']) ?>"
                         onerror="this.onerror=null;this.src='/assets/images/placeholder-poster.svg';">
                    <div class="history-row__info">
                        <p class="history-row__title"><?= e($h['title']) ?></p>
                        <?php if (!empty($h['episode_title'])): ?>
                            <p class="muted">E<?= e($h['episode_number']) ?> · <?= e($h['episode_title']) ?></p>
                        <?php endif; ?>
                        <p class="muted"><?= e(time_ago($h['watched_at'] ?? null)) ?></p>
                    </div>
                    <?php if (!empty($h['completed'])): ?>
                        <span class="pill pill--gold">Completed</span>
                    <?php elseif (!empty($h['progress'])): ?>
                        <span class="pill"><?= (int) $h['progress'] ?>%</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
