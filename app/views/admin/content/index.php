<?php /** @var array<int,array<string,mixed>> $items */ ?>
<div class="admin-toolbar">
    <div>
        <a class="btn btn--primary" href="/admin/content/create">+ Add Content</a>
        <a class="btn btn--ghost" href="/admin/media">📁 Media Library</a>
    </div>
    <form class="admin-search" action="/admin/content" method="get">
        <input type="text" name="search" class="field__input" placeholder="Search content..." value="<?= e($search ?? '') ?>">
        <select name="type" class="field__input">
            <option value="">All Types</option>
            <?php foreach (['movie', 'series', 'anime', 'k-drama', 'documentary', 'video'] as $t): ?>
                <option value="<?= e($t) ?>" <?= ($type ?? '') === $t ? 'selected' : '' ?>><?= e(ucfirst($t)) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn--ghost">Filter</button>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Status</th>
                <th>Video</th>
                <th>Featured</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= (int) $item['id'] ?></td>
                    <td>
                        <a href="/admin/content/edit/<?= (int) $item['id'] ?>"><?= e($item['title']) ?></a>
                        <?php if (!empty($item['video_path'])): ?>
                            <span class="pill pill--success">Has Video</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e(ucfirst($item['type'] ?? 'movie')) ?></td>
                    <td>
                        <span class="pill pill--<?= match($item['status'] ?? 'draft') { 'published' => 'success', 'draft' => 'ghost', default => 'muted' } ?>">
                            <?= e(ucfirst($item['status'] ?? 'draft')) ?>
                        </span>
                    </td>
                    <td><?= !empty($item['video_path']) ? 'Yes' : 'No' ?></td>
                    <td><?= !empty($item['is_featured']) ? '★' : '' ?></td>
                    <td>
                        <a class="link" href="/admin/content/edit/<?= (int) $item['id'] ?>">Edit</a>
                        <form action="/admin/content/delete/<?= (int) $item['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Delete this content?')">
                            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                            <button type="submit" class="link link--danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <tr><td colspan="7" class="muted">No content found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($totalPages) && $totalPages > 1): ?>
    <?php include __DIR__ . '/../partials/pagination.php'; ?>
<?php endif; ?>
