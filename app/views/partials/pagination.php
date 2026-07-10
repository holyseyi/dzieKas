<?php
/**
 * Pagination control.
 *
 * @var int $page
 * @var int $totalPages
 */
$page = isset($page) ? (int) $page : 1;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
if ($totalPages < 2) {
    return;
}
$baseQuery = $_GET;
$buildUrl = static function (int $p) use ($baseQuery): string {
    $baseQuery['page'] = $p;
    return '?' . http_build_query($baseQuery);
};
$start = max(1, $page - 2);
$end = min($totalPages, $page + 2);
?>
<nav class="pagination" aria-label="Pagination">
    <?php if ($page > 1): ?>
        <a class="pagination__item" href="<?= e($buildUrl($page - 1)) ?>">‹ Prev</a>
    <?php endif; ?>

    <?php if ($start > 1): ?>
        <a class="pagination__item" href="<?= e($buildUrl(1)) ?>">1</a>
        <?php if ($start > 2): ?><span class="pagination__gap">…</span><?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $start; $i <= $end; $i++): ?>
        <a class="pagination__item <?= $i === $page ? 'is-active' : '' ?>" href="<?= e($buildUrl($i)) ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?><span class="pagination__gap">…</span><?php endif; ?>
        <a class="pagination__item" href="<?= e($buildUrl($totalPages)) ?>"><?= $totalPages ?></a>
    <?php endif; ?>

    <?php if ($page < $totalPages): ?>
        <a class="pagination__item" href="<?= e($buildUrl($page + 1)) ?>">Next ›</a>
    <?php endif; ?>
</nav>
