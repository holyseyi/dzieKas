<?php
/**
 * Series detail page.
 *
 * @var array<string, mixed> $content
 * @var array<int, array<string, mixed>> $seasons
 * @var array<int, array<string, mixed>> $comments
 * @var array<int, array<string, mixed>> $related
 * @var array<string, mixed>|null $continueWatching
 * @var array<string, mixed>|null $user
 */
$c = $content;
$genres = $c['genres'] ?? [];
$actors = $c['actors'] ?? [];
$user = $user ?? null;
?>
<div class="detail-hero" style="background-image:linear-gradient(to top, var(--bg) 6%, rgba(10,12,20,.7)), url('<?= e(img($c['banner'] ?? $c['poster'] ?? null)) ?>')">
    <div class="container detail-hero__inner">
        <div class="detail-poster">
            <img src="<?= e(img($c['poster'] ?? null)) ?>" alt="<?= e($c['title']) ?>"
                 onerror="this.onerror=null;this.src='/assets/images/placeholder-poster.svg';">
        </div>
        <div class="detail-info">
            <span class="detail-info__type"><?= e(type_label($c['type'] ?? 'series')) ?></span>
            <h1 class="detail-info__title"><?= e($c['title']) ?></h1>
            <div class="detail-info__meta">
                <?php if (!empty($c['release_year'])): ?><span><?= e($c['release_year']) ?></span><?php endif; ?>
                <span><?= count($seasons ?? []) ?> season<?= count($seasons ?? []) === 1 ? '' : 's' ?></span>
                <?php if (!empty($c['imdb_rating'])): ?><span class="pill pill--gold">IMDb <?= e(number_format((float) $c['imdb_rating'], 1)) ?></span><?php endif; ?>
                <?php if (!empty($c['country_name'])): ?><span><?= e($c['country_name']) ?></span><?php endif; ?>
            </div>
            <?php if (!empty($genres)): ?>
                <div class="detail-genres">
                    <?php foreach ($genres as $g): ?>
                        <a class="chip" href="/genre/<?= e($g['slug']) ?>"><?= e($g['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <p class="detail-info__desc"><?= nl2br(e($c['description'] ?? '')) ?></p>
            <?php if (!empty($continueWatching)): ?>
                <div class="continue">
                    Continue: S<?= e($continueWatching['season_number'] ?? 1) ?>E<?= e($continueWatching['episode_number'] ?? 1) ?>
                    — <?= e($continueWatching['episode_title'] ?? '') ?>
                </div>
            <?php endif; ?>
            <div class="detail-actions">
                <a class="btn btn--primary" href="#episodes">▶ View Episodes</a>
                <?php if ($user): ?>
                    <button class="btn btn--ghost" data-bookmark="<?= (int) $c['id'] ?>">☆ Bookmark</button>
                <?php else: ?>
                    <a class="btn btn--ghost" href="/login">☆ Bookmark</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container detail-body">
    <div class="detail-body__main">
        <section class="panel" id="episodes">
            <h2 class="panel__title">Episodes</h2>
            <?php if (empty($seasons)): ?>
                <p class="muted">No episodes have been added yet.</p>
            <?php else: ?>
                <?php foreach ($seasons as $si => $season): ?>
                    <div class="season" data-accordion>
                        <button class="season__head" data-accordion-toggle>
                            <span><?= e($season['title'] ?: 'Season ' . $season['season_number']) ?></span>
                            <span class="muted"><?= count($season['episodes'] ?? []) ?> episodes</span>
                        </button>
                        <div class="season__body <?= $si === 0 ? 'is-open' : '' ?>">
                            <?php foreach (($season['episodes'] ?? []) as $ep): ?>
                                <div class="episode">
                                    <span class="episode__num"><?= e($ep['episode_number']) ?></span>
                                    <div class="episode__info">
                                        <p class="episode__title"><?= e($ep['title']) ?></p>
                                        <?php if (!empty($ep['air_date'])): ?><p class="muted"><?= e($ep['air_date']) ?></p><?php endif; ?>
                                    </div>
                                    <div class="episode__links">
                                        <?php if (!empty($ep['stream_count'])): ?><span class="pill">▶ <?= (int) $ep['stream_count'] ?></span><?php endif; ?>
                                        <?php if (!empty($ep['download_count'])): ?><span class="pill">⤓ <?= (int) $ep['download_count'] ?></span><?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($season['episodes'])): ?><p class="muted">No episodes in this season.</p><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <?php if (!empty($actors)): ?>
            <section class="panel">
                <h2 class="panel__title">Cast</h2>
                <div class="cast-grid">
                    <?php foreach ($actors as $a): ?>
                        <div class="cast">
                            <img src="<?= e($a['photo'] ? img($a['photo']) : '/assets/images/avatar.svg') ?>"
                                 onerror="this.src='/assets/images/avatar.svg'" alt="<?= e($a['name']) ?>" loading="lazy">
                            <p class="cast__name"><?= e($a['name']) ?></p>
                            <?php if (!empty($a['character_name'])): ?><p class="cast__role"><?= e($a['character_name']) ?></p><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="panel" id="comments">
            <h2 class="panel__title">Comments (<?= count($comments ?? []) ?>)</h2>
            <?php if ($user): ?>
                <form class="comment-form" action="/comment/<?= (int) $c['id'] ?>" method="post">
                    <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                    <textarea name="body" rows="3" placeholder="Share your thoughts..." required></textarea>
                    <button class="btn btn--primary" type="submit">Post Comment</button>
                </form>
            <?php else: ?>
                <p class="muted"><a href="/login">Login</a> to join the conversation.</p>
            <?php endif; ?>
            <div class="comment-list">
                <?php foreach (($comments ?? []) as $cm): ?>
                    <div class="comment">
                        <img class="comment__avatar" src="<?= e($cm['avatar'] ? img($cm['avatar']) : '/assets/images/avatar.svg') ?>"
                             onerror="this.src='/assets/images/avatar.svg'" alt="">
                        <div class="comment__body">
                            <p class="comment__head"><strong><?= e($cm['display_name'] ?: $cm['username']) ?></strong>
                                <span class="muted"><?= e(time_ago($cm['created_at'] ?? null)) ?></span></p>
                            <p><?= nl2br(e($cm['body'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($comments)): ?><p class="muted">Be the first to comment.</p><?php endif; ?>
            </div>
        </section>
    </div>

    <aside class="detail-body__side">
        <div class="panel">
            <h3 class="panel__title">Details</h3>
            <dl class="facts">
                <?php if (!empty($c['category_name'])): ?><dt>Category</dt><dd><?= e($c['category_name']) ?></dd><?php endif; ?>
                <?php if (!empty($c['language_name'])): ?><dt>Language</dt><dd><?= e($c['language_name']) ?></dd><?php endif; ?>
                <dt>Views</dt><dd><?= e(number_format((int) ($c['view_count'] ?? 0))) ?></dd>
            </dl>
        </div>
    </aside>
</div>

<?php if (!empty($related)): ?>
    <div class="container">
        <?php $sectionTitle = 'Related Series'; $sectionItems = $related; $sectionLink = null;
        include __DIR__ . '/../partials/content-row.php'; ?>
    </div>
<?php endif; ?>
