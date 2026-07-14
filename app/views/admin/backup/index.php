<?php /** @var array<int,array<string,mixed>> $backups */ ?>
<div class="admin-toolbar">
    <form action="/admin/backup/create" method="post" class="admin-form-inline">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <button type="submit" class="btn btn--primary">Create Backup</button>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Name</th><th>Size</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($backups as $b): ?>
                <tr>
                    <td><?= e($b['name']) ?></td>
                    <td><?= number_format((int) $b['size']) ?> bytes</td>
                    <td><?= e($b['date']) ?></td>
                    <td>
                        <form action="/admin/backup/restore" method="post" class="inline-form" onsubmit="return confirm('Restore this backup? Current data will be replaced.')">
                            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                            <input type="hidden" name="backup_file" value="<?= e($b['name']) ?>">
                            <button type="submit" class="link">Restore</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($backups)): ?><tr><td colspan="4" class="muted">No backups found.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
