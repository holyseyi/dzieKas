<?php /** @var array<string,mixed> $stats */ ?>
<div class="dashboard">
    <div class="stats-grid">
        <?php foreach ($stats as $label => $value): ?>
            <div class="stat-card">
                <h3><?= e($value) ?></h3>
                <p><?= e(ucwords(str_replace('_', ' ', $label))) ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="dashboard__grid">
        <section class="panel">
            <h2 class="panel__title">Recent Content</h2>
            <table class="table">
                <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Views</th></tr></thead>
                <tbody>
                    <?php foreach ($recentContent as $c): ?>
                        <tr>
                            <td><?= e($c['title']) ?></td>
                            <td><?= e(ucfirst($c['type'])) ?></td>
                            <td><?= e(ucfirst($c['status'])) ?></td>
                            <td><?= (int) $c['view_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h2 class="panel__title">Top Content</h2>
            <table class="table">
                <thead><tr><th>Title</th><th>Views</th><th>Downloads</th></tr></thead>
                <tbody>
                    <?php foreach ($topContent as $c): ?>
                        <tr>
                            <td><?= e($c['title']) ?></td>
                            <td><?= (int) $c['view_count'] ?></td>
                            <td><?= (int) $c['download_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h2 class="panel__title">Recent Users</h2>
            <table class="table">
                <thead><tr><th>Username</th><th>Email</th><th>Joined</th></tr></thead>
                <tbody>
                    <?php foreach ($recentUsers as $u): ?>
                        <tr>
                            <td><?= e($u['username']) ?></td>
                            <td><?= e($u['email']) ?></td>
                            <td><?= e($u['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>
