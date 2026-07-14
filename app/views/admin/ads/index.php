<?php /** @var array<int,array<string,mixed>> $ads */ ?>
<div class="admin-toolbar">
    <form action="/admin/ads/store" method="post" class="admin-form-inline">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <input type="text" name="name" class="field__input" placeholder="Ad name" required>
        <input type="text" name="position" class="field__input" placeholder="Position" value="sidebar">
        <button type="submit" class="btn btn--primary">Add</button>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Name</th><th>Position</th><th>Active</th></tr></thead>
        <tbody>
            <?php foreach ($ads as $a): ?>
                <tr>
                    <td><?= e($a['name']) ?></td>
                    <td><?= e($a['position']) ?></td>
                    <td><?= !empty($a['is_active']) ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
