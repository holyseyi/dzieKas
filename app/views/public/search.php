<?php
/**
 * Search results page.
 *
 * @var string $query
 * @var array<int, array<string, mixed>> $results
 */
$query = $query ?? '';
?>
<div class="container listing">
    <header class="listing__head">
        <h1 class="listing__title">Search</h1>
        <form class="search-inline" action="/search" method="get">
            <input type="search" name="q" value="<?= e($query) ?>" placeholder="Search movies, series, actors..." autofocus>
            <button class="btn btn--primary" type="submit">Search</button>
        </form>
    </header>

    <?php if ($query === ''): ?>
        <div class="empty-state"><p>Type a keyword to search the catalog.</p></div>
    <?php elseif (empty($results)): ?>
        <div class="empty-state">
            <p>No results for <strong>"<?= e($query) ?>"</strong>.</p>
            <a class="btn btn--primary" href="/">Back to Home</a>
        </div>
    <?php else: ?>
        <p class="listing__count"><?= count($results) ?> result<?= count($results) === 1 ? '' : 's' ?> for "<?= e($query) ?>"</p>
        <div class="poster-grid">
            <?php foreach ($results as $item): ?>
                <?php include __DIR__ . '/../partials/content-card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
