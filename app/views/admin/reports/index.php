<?php /** @var array<int,array<string,mixed>> $reports */ ?>
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>User</th><th>Reason</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
            <?php foreach ($reports as $r): ?>
                <tr>
                    <td><?= e($r['username'] ?? 'Guest') ?></td>
                    <td><?= e($r['reason'] ?? '') ?></td>
                    <td><?= e(ucfirst($r['status'] ?? 'pending')) ?></td>
                    <td><?= e($r['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
