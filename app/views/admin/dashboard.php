<?php /** @var array<string,mixed> $stats */ ?>
<?php /** @var array<int,array<string,mixed>> $recentVideos */ ?>
<div class="dashboard">
    <div class="dashboard__actions" style="margin-bottom:1.5rem">
        <a class="btn btn--primary" href="/admin/media">📁 Media Library</a>
        <a class="btn btn--ghost" href="/admin/content/create">+ Add Content</a>
    </div>
    <div class="stats-grid">
        <?php foreach ($stats as $label => $value): ?>
            <div class="stat-card">
                <h3><?= e($value) ?></h3>
                <p><?= e(ucwords(str_replace('_', ' ', $label))) ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="dashboard__grid">
        <section class="panel">
            <h2 class="panel__title">Recent Content</h2>
            <table class="table">
                <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Views</th></tr></thead>
                <tbody>
                    <?php foreach ($recentContent as $c): ?>
                        <tr>
                            <td><?= e($c['title']) ?></td>
                            <td><?= e(ucfirst($c['type'])) ?></td>
                            <td><?= e(ucfirst($c['status'])) ?></td>
                            <td><?= (int) $c['view_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h2 class="panel__title">Top Content</h2>
            <table class="table">
                <thead><tr><th>Title</th><th>Views</th><th>Downloads</th></tr></thead>
                <tbody>
                    <?php foreach ($topContent as $c): ?>
                        <tr>
                            <td><?= e($c['title']) ?></td>
                            <td><?= (int) $c['view_count'] ?></td>
                            <td><?= (int) $c['download_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h2 class="panel__title">Recent Users</h2>
            <table class="table">
                <thead><tr><th>Username</th><th>Email</th><th>Joined</th></tr></thead>
                <tbody>
                    <?php foreach ($recentUsers as $u): ?>
                        <tr>
                            <td><?= e($u['username']) ?></td>
                            <td><?= e($u['email']) ?></td>
                            <td><?= e($u['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h2 class="panel__title">Recent Videos</h2>
            <table class="table">
                <thead><tr><th>Name</th><th>Folder</th><th>Size</th><th>Uploaded</th></tr></thead>
                <tbody>
                    <?php foreach ($recentVideos as $v): ?>
                        <tr>
                            <td>
                                <a class="link" href="/storage/<?= e(ltrim($v['path'], '/')) ?>" target="_blank" rel="noopener">
                                    🎬 <?= e($v['original_name']) ?>
                                </a>
                            </td>
                            <td><?= e($v['folder_name']) ?></td>
                            <td><?= e(format_bytes((int) $v['file_size'])) ?></td>
                            <td><?= e($v['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentVideos)): ?><tr><td colspan="4" class="muted">No videos uploaded yet. <a class="link" href="/admin/media">Upload now</a></td></tr><?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>
