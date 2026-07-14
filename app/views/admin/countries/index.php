<?php /** @var array<int,array<string,mixed>> $countries */ ?>
<div class="admin-toolbar">
    <form action="/admin/countries/store" method="post" class="admin-form-inline">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <input type="text" name="name" class="field__input" placeholder="Country name" required>
        <input type="text" name="code" class="field__input" placeholder="Code" maxlength="3">
        <button type="submit" class="btn btn--primary">Add</button>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Name</th><th>Slug</th><th>Code</th></tr></thead>
        <tbody>
            <?php foreach ($countries as $c): ?>
                <tr>
                    <td><?= e($c['name']) ?></td>
                    <td><?= e($c['slug']) ?></td>
                    <td><?= e($c['code'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
