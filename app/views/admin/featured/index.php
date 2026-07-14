<?php /** @var array<int,array<string,mixed>> $featured */ ?>
<div class="admin-toolbar">
    <form action="/admin/featured/store" method="post" class="admin-form-inline">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <select name="content_id" class="field__input">
            <option value="">Select content...</option>
            <?php foreach ($content as $c): ?>
                <option value="<?= (int) $c['id'] ?>"><?= e($c['title']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="section" class="field__input" placeholder="Section" value="featured">
        <input type="number" name="sort_order" class="field__input" placeholder="Sort" value="0">
        <button type="submit" class="btn btn--primary">Add</button>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Content</th><th>Section</th><th>Sort</th></tr></thead>
        <tbody>
            <?php foreach ($featured as $f): ?>
                <tr>
                    <td><?= e($f['title']) ?></td>
                    <td><?= e($f['section']) ?></td>
                    <td><?= (int) $f['sort_order'] ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($featured)): ?><tr><td colspan="3" class="muted">No featured content.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
