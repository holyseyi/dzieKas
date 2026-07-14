<?php
/**
 * Movie detail page.
 *
 * @var array<string, mixed> $content
 * @var array<int, array<string, mixed>> $comments
 * @var array<int, array<string, mixed>> $related
 * @var bool $isBookmarked
 * @var array<string, mixed>|null $user
 */
$c = $content;
$genres = $c['genres'] ?? [];
$actors = $c['actors'] ?? [];
$directors = $c['directors'] ?? [];
$downloads = $c['downloads'] ?? [];
$streams = $c['streaming_links'] ?? [];
$screenshots = $c['screenshots'] ?? [];
$trailers = $c['trailers'] ?? [];
$user = $user ?? null;
?>
<div class="detail-hero" style="background-image:linear-gradient(to top, var(--bg) 6%, rgba(10,12,20,.7)), url('<?= e(img($c['banner'] ?? $c['poster'] ?? null)) ?>')">
    <div class="container detail-hero__inner">
        <div class="detail-poster">
            <img src="<?= e(img($c['poster'] ?? null)) ?>" alt="<?= e($c['title']) ?>"
                 onerror="this.onerror=null;this.src='/assets/images/placeholder-poster.svg';">
        </div>
        <div class="detail-info">
            <span class="detail-info__type"><?= e(type_label($c['type'] ?? 'movie')) ?></span>
            <h1 class="detail-info__title"><?= e($c['title']) ?></h1>
            <?php if (!empty($c['original_title']) && $c['original_title'] !== $c['title']): ?>
                <p class="detail-info__orig"><?= e($c['original_title']) ?></p>
            <?php endif; ?>
            <div class="detail-info__meta">
                <?php if (!empty($c['release_year'])): ?><span><?= e($c['release_year']) ?></span><?php endif; ?>
                <?php if (!empty($c['runtime'])): ?><span><?= e($c['runtime']) ?> min</span><?php endif; ?>
                <?php if (!empty($c['imdb_rating'])): ?><span class="pill pill--gold">IMDb <?= e(number_format((float) $c['imdb_rating'], 1)) ?></span><?php endif; ?>
                <?php if (!empty($c['country_name'])): ?><span><?= e($c['country_name']) ?></span><?php endif; ?>
                <?php if (!empty($c['language_name'])): ?><span><?= e($c['language_name']) ?></span><?php endif; ?>
            </div>
            <?php if (!empty($genres)): ?>
                <div class="detail-genres">
                    <?php foreach ($genres as $g): ?>
                        <a class="chip" href="/genre/<?= e($g['slug']) ?>"><?= e($g['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <p class="detail-info__desc"><?= nl2br(e($c['description'] ?? '')) ?></p>
            <div class="detail-stats">
                <span>👁 <?= e(number_format((int) ($c['view_count'] ?? 0))) ?> views</span>
                <span>⤓ <?= e(number_format((int) ($c['download_count'] ?? 0))) ?> downloads</span>
                <span data-like-count>♥ <?= e(number_format((int) ($c['like_count'] ?? 0))) ?> likes</span>
            </div>
            <div class="detail-actions">
                <?php if (!empty($trailers) || !empty($c['trailer_url'])): ?>
                    <a class="btn btn--primary" href="#watch">▶ Watch Trailer</a>
                <?php endif; ?>
                <button class="btn btn--ghost" data-like="<?= (int) $c['id'] ?>">♥ Like</button>
                <?php if ($user): ?>
                    <button class="btn btn--ghost" data-bookmark="<?= (int) $c['id'] ?>" data-active="<?= !empty($isBookmarked) ? '1' : '0' ?>">
                        <?= !empty($isBookmarked) ? '★ Bookmarked' : '☆ Bookmark' ?>
                    </button>
                <?php else: ?>
                    <a class="btn btn--ghost" href="/login">☆ Bookmark</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container detail-body">
    <div class="detail-body__main">
        <?php if (!empty($c['synopsis'])): ?>
            <section class="panel">
                <h2 class="panel__title">Synopsis</h2>
                <p><?= nl2br(e($c['synopsis'])) ?></p>
            </section>
        <?php endif; ?>

        <?php if (!empty($c['video_path'])): ?>
            <section class="panel" id="watch">
                <h2 class="panel__title">Watch</h2>
                <div class="video-embed">
                    <video src="<?= e(video_url($c['video_path'])) ?>" controls preload="metadata" style="width:100%;border-radius:8px;background:#000;"></video>
                </div>
            </section>
        <?php elseif (!empty($trailers) || !empty($c['trailer_url'])): ?>
            <section class="panel" id="watch">
                <h2 class="panel__title">Trailer</h2>
                <?php
                $trailerUrl = $trailers[0]['url'] ?? $c['trailer_url'] ?? '';
                $embed = str_replace(['watch?v=', 'youtu.be/'], ['embed/', 'www.youtube.com/embed/'], $trailerUrl);
                ?>
                <?php if ($embed): ?>
                    <div class="video-embed">
                        <iframe src="<?= e($embed) ?>" title="Trailer" frameborder="0" allowfullscreen loading="lazy"></iframe>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($streams)): ?>
            <section class="panel">
                <h2 class="panel__title">Stream</h2>
                <div class="link-list">
                    <?php foreach ($streams as $s): ?>
                        <a class="link-row" href="<?= e($s['url']) ?>" target="_blank" rel="noopener nofollow">
                            <span><?= e($s['title'] ?: ($s['provider'] ?? 'Server')) ?></span>
                            <?php if (!empty($s['quality'])): ?><span class="pill"><?= e($s['quality']) ?></span><?php endif; ?>
                            <span class="link-row__go">Play ▶</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($downloads)): ?>
            <section class="panel">
                <h2 class="panel__title">Download</h2>
                <div class="link-list">
                    <?php foreach ($downloads as $d): ?>
                        <a class="link-row" href="<?= e($d['url']) ?>" target="_blank" rel="noopener nofollow">
                            <span><?= e($d['title']) ?></span>
                            <?php if (!empty($d['quality'])): ?><span class="pill"><?= e($d['quality']) ?></span><?php endif; ?>
                            <?php if (!empty($d['size'])): ?><span class="muted"><?= e($d['size']) ?></span><?php endif; ?>
                            <span class="link-row__go">⤓ Get</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($screenshots)): ?>
            <section class="panel">
                <h2 class="panel__title">Screenshots</h2>
                <div class="shots">
                    <?php foreach ($screenshots as $s): ?>
                        <img src="<?= e(img($s['image_path'])) ?>" alt="<?= e($s['caption'] ?? '') ?>" loading="lazy">
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

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
                            <p class="comment__head">
                                <strong><?= e($cm['display_name'] ?: $cm['username']) ?></strong>
                                <span class="muted"><?= e(time_ago($cm['created_at'] ?? null)) ?></span>
                            </p>
                            <p><?= nl2br(e($cm['body'])) ?></p>
                            <?php foreach (($cm['replies'] ?? []) as $rep): ?>
                                <div class="comment comment--reply">
                                    <img class="comment__avatar" src="<?= e($rep['avatar'] ? img($rep['avatar']) : '/assets/images/avatar.svg') ?>"
                                         onerror="this.src='/assets/images/avatar.svg'" alt="">
                                    <div class="comment__body">
                                        <p class="comment__head"><strong><?= e($rep['display_name'] ?: $rep['username']) ?></strong></p>
                                        <p><?= nl2br(e($rep['body'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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
                <?php if (!empty($c['release_date'])): ?><dt>Released</dt><dd><?= e($c['release_date']) ?></dd><?php endif; ?>
                <?php if (!empty($c['runtime'])): ?><dt>Runtime</dt><dd><?= e($c['runtime']) ?> min</dd><?php endif; ?>
                <?php if (!empty($directors)): ?>
                    <dt>Director</dt>
                    <dd><?= e(implode(', ', array_map(fn ($d) => $d['name'], $directors))) ?></dd>
                <?php endif; ?>
                <?php if (!empty($c['user_rating_avg'])): ?>
                    <dt>User Rating</dt><dd><?= e(number_format((float) $c['user_rating_avg'], 1)) ?> / 10 (<?= (int) ($c['user_rating_count'] ?? 0) ?>)</dd>
                <?php endif; ?>
            </dl>
        </div>
    </aside>
</div>

<?php if (!empty($related)): ?>
    <div class="container">
        <?php $sectionTitle = 'You May Also Like'; $sectionItems = $related; $sectionLink = null;
        include __DIR__ . '/../partials/content-row.php'; ?>
    </div>
<?php endif; ?>
