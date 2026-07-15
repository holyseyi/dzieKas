<?php /** @var array<int,array<string,mixed>> $countries */ ?>
<div class="admin-toolbar">
    <form action="/admin/countries/store" method="post" class="admin-form-inline">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <input type="text" name="name" class="field__input" placeholder="Country name" required>
        <input type="text" name="code" class="field__input" placeholder="Code" maxlength="3">
        <button type="submit" class="btn btn--primary">Add</button>
    </form>
    <a class="btn btn--ghost" href="/admin/content/create">+ Add Content</a>
</div>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Name</th><th>Slug</th><th>Code</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($countries as $c): ?>
                <tr>
                    <td><?= e($c['name']) ?></td>
                    <td><?= e($c['slug']) ?></td>
                    <td><?= e($c['code'] ?? '') ?></td>
                    <td>
                        <a class="link" href="/admin/content/create?country_id=<?= (int) $c['id'] ?>">Add Content</a>
                        <form action="/admin/countries/delete/<?= (int) $c['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Delete this country?')">
                            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                            <button type="submit" class="link link--danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>