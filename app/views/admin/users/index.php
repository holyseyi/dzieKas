<?php /** @var array<int,array<string,mixed>> $users */ ?>
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= (int) $u['id'] ?></td>
                    <td><?= e($u['username']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= e(ucfirst($u['role'] ?? 'user')) ?></td>
                    <td><?= !empty($u['is_active']) ? 'Yes' : 'No' ?></td>
                    <td>
                        <form action="/admin/users/toggle/<?= (int) $u['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Toggle status?')">
                            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                            <button type="submit" class="link">Toggle</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if (!empty($totalPages) && $totalPages > 1): ?>
    <?php include __DIR__ . '/../partials/pagination.php'; ?>
<?php endif; ?>
