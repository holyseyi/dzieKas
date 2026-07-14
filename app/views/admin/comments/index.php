<?php /** @var array<int,array<string,mixed>> $comments */ ?>
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>User</th><th>Content</th><th>Comment</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($comments as $c): ?>
                <tr>
                    <td><?= e($c['username']) ?></td>
                    <td><?= e($c['content_title']) ?></td>
                    <td><?= e(str_excerpt($c['body'] ?? '', 80)) ?></td>
                    <td><?= e($c['created_at']) ?></td>
                    <td>
                        <?php if (empty($c['is_approved'])): ?>
                            <form action="/admin/comments/approve/<?= (int) $c['id'] ?>" method="post" class="inline-form">
                                <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                                <button type="submit" class="link">Approve</button>
                            </form>
                        <?php endif; ?>
                        <form action="/admin/comments/delete/<?= (int) $c['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Delete comment?')">
                            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                            <button type="submit" class="link link--danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
