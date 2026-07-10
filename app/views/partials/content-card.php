<?php
/**
 * Reusable content poster card.
 *
 * Expects $item (array<string,mixed>) in scope.
 *
 * @var array<string, mixed> $item
 */
$cardUrl = content_url($item);
$rating = isset($item['imdb_rating']) ? (float) $item['imdb_rating'] : 0.0;
?>
<article class="card">
    <a class="card__link" href="<?= e($cardUrl) ?>">
        <div class="card__poster">
            <img src="<?= e(img($item['poster'] ?? null)) ?>"
                 alt="<?= e($item['title'] ?? '') ?>"
                 loading="lazy"
                 onerror="this.onerror=null;this.src='/assets/images/placeholder-poster.svg';">
            <span class="card__type"><?= e(type_label($item['type'] ?? 'movie')) ?></span>
            <?php if ($rating > 0): ?>
                <span class="card__rating">★ <?= e(number_format($rating, 1)) ?></span>
            <?php endif; ?>
            <span class="card__overlay"><i>▶</i></span>
        </div>
        <div class="card__body">
            <h3 class="card__title"><?= e($item['title'] ?? 'Untitled') ?></h3>
            <div class="card__meta">
                <?php if (!empty($item['release_year'])): ?>
                    <span><?= e($item['release_year']) ?></span>
                <?php endif; ?>
                <?php if (!empty($item['view_count'])): ?>
                    <span><?= e(number_format((int) $item['view_count'])) ?> views</span>
                <?php endif; ?>
            </div>
        </div>
    </a>
</article>
