<?php /** @var array<int,array<string,mixed>> $actors */ ?>
<div class="admin-toolbar">
    <form action="/admin/actors/store" method="post" class="admin-form-inline">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <input type="text" name="name" class="field__input" placeholder="Actor name" required>
        <button type="submit" class="btn btn--primary">Add</button>
    </form>
    <a class="btn btn--ghost" href="/admin/content/create">+ Add Content</a>
</div>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($actors as $a): ?>
                <tr>
                    <td><?= e($a['name']) ?></td>
                    <td><?= e($a['slug']) ?></td>
                    <td>
                        <a class="link" href="/admin/content/create?actor_id=<?= (int) $a['id'] ?>">Add Content</a>
                        <form action="/admin/actors/delete/<?= (int) $a['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Delete this actor?')">
                            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                            <button type="submit" class="link link--danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>