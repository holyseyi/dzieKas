<?php /** @var array<int,array<string,mixed>> $logs */ ?>
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>User</th><th>Action</th><th>Entity</th><th>Date</th></tr></thead>
        <tbody>
            <?php foreach ($logs as $l): ?>
                <tr>
                    <td><?= e($l['username'] ?? 'System') ?></td>
                    <td><?= e($l['action']) ?></td>
                    <td><?= e($l['entity_type'] ?? '') ?> #<?= (int) $l['entity_id'] ?></td>
                    <td><?= e($l['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
