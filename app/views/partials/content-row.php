<?php
/**
 * Horizontal titled content row.
 *
 * @var string $sectionTitle
 * @var array<int, array<string, mixed>> $sectionItems
 * @var string|null $sectionLink
 */
if (empty($sectionItems)) {
    return;
}
?>
<section class="row">
    <div class="row__head">
        <h2 class="row__title"><?= e($sectionTitle) ?></h2>
        <?php if (!empty($sectionLink)): ?>
            <a class="row__more" href="<?= e($sectionLink) ?>">View all →</a>
        <?php endif; ?>
    </div>
    <div class="row__scroller">
        <?php foreach ($sectionItems as $item): ?>
            <?php include __DIR__ . '/content-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
