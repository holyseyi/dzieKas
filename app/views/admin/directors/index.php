<?php /** @var array<int,array<string,mixed>> $directors */ ?>
<div class="admin-toolbar">
    <form action="/admin/directors/store" method="post" class="admin-form-inline">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <input type="text" name="name" class="field__input" placeholder="Director name" required>
        <button type="submit" class="btn btn--primary">Add</button>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($directors as $d): ?>
                <tr>
                    <td><?= e($d['name']) ?></td>
                    <td><?= e($d['slug']) ?></td>
                    <td>
                        <form action="/admin/directors/delete/<?= (int) $d['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Delete this director?')">
                            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                            <button type="submit" class="link link--danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
