<?php /** @var array<int,array<string,mixed>> $genres */ ?>
<div class="admin-toolbar">
    <form action="/admin/genres/store" method="post" class="admin-form-inline">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <input type="text" name="name" class="field__input" placeholder="Genre name" required>
        <button type="submit" class="btn btn--primary">Add</button>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Name</th><th>Slug</th><th>Count</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($genres as $g): ?>
                <tr>
                    <td><?= e($g['name']) ?></td>
                    <td><?= e($g['slug']) ?></td>
                    <td><?= (int) $g['count'] ?></td>
                    <td>
                        <form action="/admin/genres/delete/<?= (int) $g['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Delete this genre?')">
                            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                            <button type="submit" class="link link--danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
