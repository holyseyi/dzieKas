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
        <thead><tr><th>Name</th><th>Slug</th><th>Count</th></tr></thead>
        <tbody>
            <?php foreach ($genres as $g): ?>
                <tr>
                    <td><?= e($g['name']) ?></td>
                    <td><?= e($g['slug']) ?></td>
                    <td><?= (int) $g['count'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
