<?php
/**
 * Home page.
 *
 * @var array<int, array<string, mixed>> $hero
 * @var array<int, array<string, mixed>> $featured
 * @var array<int, array<string, mixed>> $latest
 * @var array<int, array<string, mixed>> $trending
 * @var array<int, array<string, mixed>> $updatedSeries
 * @var array<int, array<string, mixed>> $popularWeek
 * @var array<int, array<string, mixed>> $anime
 * @var array<int, array<string, mixed>> $kDramas
 * @var array<int, array<string, mixed>> $nollywood
 * @var array<int, array<string, mixed>> $hollywood
 * @var array<int, array<string, mixed>> $bollywood
 * @var array<int, array<string, mixed>> $tvShows
 * @var array<int, array<string, mixed>> $genres
 * @var array<int, array<string, mixed>> $years
 * @var array<int, array<string, mixed>> $countries
 * @var array<int, array<string, mixed>> $announcements
 * @var array<int, array<string, mixed>> $headerAds
 */
$heroItems = !empty($hero) ? $hero : array_slice($featured ?: $latest, 0, 5);
?>

<?php if (!empty($announcements)): ?>
    <div class="container announcements">
        <?php foreach ($announcements as $a): ?>
            <div class="announcement announcement--<?= e($a['type'] ?? 'info') ?>">
                <strong><?= e($a['title']) ?></strong>
                <?php if (!empty($a['body'])): ?><span><?= e($a['body']) ?></span><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($heroItems)): ?>
<section class="hero" data-hero>
    <div class="hero__slides">
        <?php foreach ($heroItems as $i => $h): ?>
            <div class="hero__slide <?= $i === 0 ? 'is-active' : '' ?>"
                 style="background-image:linear-gradient(to top, rgba(10,12,20,.96), rgba(10,12,20,.35)), url('<?= e(img($h['banner'] ?? $h['poster'] ?? null)) ?>')">
                <div class="container hero__content">
                    <span class="hero__badge"><?= e(type_label($h['type'] ?? 'movie')) ?></span>
                    <h1 class="hero__title"><?= e($h['title']) ?></h1>
                    <div class="hero__meta">
                        <?php if (!empty($h['release_year'])): ?><span><?= e($h['release_year']) ?></span><?php endif; ?>
                        <?php if (!empty($h['imdb_rating'])): ?><span>★ <?= e(number_format((float) $h['imdb_rating'], 1)) ?></span><?php endif; ?>
                        <?php if (!empty($h['runtime'])): ?><span><?= e($h['runtime']) ?> min</span><?php endif; ?>
                    </div>
                    <p class="hero__desc"><?= e(str_excerpt($h['description'] ?? '', 200)) ?></p>
                    <div class="hero__actions">
                        <a class="btn btn--primary btn--lg" href="<?= e(content_url($h)) ?>">▶ Watch Now</a>
                        <a class="btn btn--ghost btn--lg" href="<?= e(content_url($h)) ?>">More Info</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($heroItems) > 1): ?>
        <div class="hero__dots">
            <?php foreach ($heroItems as $i => $h): ?>
                <button class="hero__dot <?= $i === 0 ? 'is-active' : '' ?>" data-hero-dot="<?= $i ?>"></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<div class="container">
    <?php
    $rows = [
        ['Featured', $featured, null],
        ['Trending Now', $trending, '/trending'],
        ['Latest Uploads', $latest, '/latest'],
        ['Popular This Week', $popularWeek, null],
        ['Recently Updated Series', $updatedSeries, '/tv-series'],
        ['TV Shows', $tvShows, '/tv-series'],
        ['Anime', $anime, '/anime'],
        ['K-Dramas', $kDramas, '/k-dramas'],
        ['Nollywood', $nollywood, '/country/nigeria'],
        ['Hollywood', $hollywood, '/country/united-states'],
        ['Bollywood', $bollywood, '/country/india'],
    ];
    if (!empty($latestVideos)) {
        array_unshift($rows, ['Latest Videos', $latestVideos, null]);
    }
    foreach ($rows as [$sectionTitle, $sectionItems, $sectionLink]) {
        include __DIR__ . '/../partials/content-row.php';
    }
    ?>

    <?php if (!empty($genres)): ?>
        <section class="row">
            <div class="row__head"><h2 class="row__title">Browse by Genre</h2></div>
            <div class="chip-grid">
                <?php foreach ($genres as $g): ?>
                    <a class="chip" href="/genre/<?= e($g['slug']) ?>">
                        <?= e($g['name']) ?>
                        <?php if (isset($g['content_count'])): ?><span><?= (int) $g['content_count'] ?></span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($years)): ?>
        <section class="row">
            <div class="row__head"><h2 class="row__title">Browse by Year</h2></div>
            <div class="chip-grid">
                <?php foreach ($years as $y): ?>
                    <a class="chip" href="/year/<?= e($y['release_year']) ?>"><?= e($y['release_year']) ?></a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
