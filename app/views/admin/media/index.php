<?php /** @var array<string,mixed> $folder */ ?>
<?php /** @var array<int,array<string,mixed>> $folders */ ?>
<?php /** @var array<int,array<string,mixed>> $files */ ?>
<?php /** @var array<int,array<string,mixed>> $breadcrumbs */ ?>
<div class="admin-toolbar">
    <h2 style="margin:0">Media Library</h2>
    <div class="admin-search">
        <form action="/admin/media" method="get" class="admin-form-inline">
            <button type="submit" class="btn btn--ghost">Refresh</button>
        </form>
    </div>
</div>

<?php if (!empty($breadcrumbs)): ?>
    <nav class="breadcrumb" style="margin:1rem 0">
        <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <a class="link" href="/admin/media?folder_id=<?= (int) $crumb['id'] ?>"><?= e($crumb['name']) ?></a>
            <?php if ($i < count($breadcrumbs) - 1): ?>
                <span class="muted">/</span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>

<div class="admin-form" style="margin-bottom:1.5rem">
    <form action="/admin/media/folder" method="post" class="admin-form-inline">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <input type="hidden" name="parent_id" value="<?= (int) ($folder['id'] ?? 0) ?>">
        <input type="text" name="name" class="field__input" placeholder="New folder name" required>
        <button type="submit" class="btn btn--primary">Create Folder</button>
    </form>

    <form action="/admin/media/upload" method="post" enctype="multipart/form-data" class="admin-form-inline" style="margin-top:.75rem">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <input type="hidden" name="folder_id" value="<?= (int) ($folder['id'] ?? 0) ?>">
        <input type="file" name="file" class="field__input" required>
        <button type="submit" class="btn btn--primary">Upload File</button>
    </form>
</div>

<?php if (!empty($folders)): ?>
    <section class="panel" style="margin-bottom:1.5rem">
        <h2 class="panel__title">Folders</h2>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Name</th><th>Path</th><th>Created</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($folders as $f): ?>
                        <tr>
                            <td>
                                <a class="link" href="/admin/media?folder_id=<?= (int) $f['id'] ?>">
                                    📁 <?= e($f['name']) ?>
                                </a>
                            </td>
                            <td><?= e($f['path']) ?></td>
                            <td><?= e($f['created_at']) ?></td>
                            <td>
                                <a class="link" href="/admin/media?folder_id=<?= (int) $f['id'] ?>">Open</a>
                                <form action="/admin/media/folder/delete/<?= (int) $f['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Delete folder and all its contents?')">
                                    <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                                    <button type="submit" class="link link--danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<section class="panel">
    <h2 class="panel__title">Files</h2>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Name</th><th>Type</th><th>Size</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td>
                            <?php if (str_starts_with((string) $file['mime_type'], 'video/')): ?>
                                🎬
                            <?php elseif (str_starts_with((string) $file['mime_type'], 'image/')): ?>
                                🖼
                            <?php else: ?>
                                📄
                            <?php endif; ?>
                            <a class="link" href="/storage/<?= e(ltrim($file['path'], '/')) ?>" target="_blank" rel="noopener">
                                <?= e($file['original_name']) ?>
                            </a>
                        </td>
                        <td><?= e($file['mime_type'] ?? 'unknown') ?></td>
                        <td><?= $this->formatBytes((int) $file['file_size']) ?></td>
                        <td><?= e($file['created_at']) ?></td>
                        <td>
                            <?php if (str_starts_with((string) $file['mime_type'], 'video/')): ?>
                                <a class="link" href="/storage/<?= e(ltrim($file['path'], '/')) ?>" target="_blank" rel="noopener">Play</a>
                            <?php endif; ?>
                            <form action="/admin/media/file/delete/<?= (int) $file['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Delete this file?')">
                                <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                                <button type="submit" class="link link--danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($files)): ?>
                    <tr><td colspan="5" class="muted">No files in this folder.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
