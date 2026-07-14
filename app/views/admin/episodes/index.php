<?php /** @var array<string,mixed> $content */ ?>
<?php /** @var array<int,array<string,mixed>> $seasons */ ?>
<?php /** @var array<int,array<string,mixed>> $mediaVideos */ ?>
<div class="admin-toolbar">
    <a class="btn btn--ghost" href="/admin/content">← Back to Content</a>
    <h2 style="margin:0"><?= e($content['title']) ?> — Episodes</h2>
</div>

<div class="panel" style="margin-top:1rem">
    <h2 class="panel__title">Add Episode</h2>
    <form action="/admin/episodes/store" method="post" enctype="multipart/form-data" class="form">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
        <input type="hidden" name="content_id" value="<?= (int) $content['id'] ?>">
        <div class="form__grid">
            <div class="form__main">
                <div class="field">
                    <label class="field__label">Season #</label>
                    <input type="number" name="season_number" class="field__input" value="1" required>
                </div>
                <div class="field">
                    <label class="field__label">Episode #</label>
                    <input type="number" name="episode_number" class="field__input" value="1" required>
                </div>
                <div class="field">
                    <label class="field__label">Title</label>
                    <input type="text" name="title" class="field__input" required>
                </div>
                <div class="field">
                    <label class="field__label">Description</label>
                    <textarea name="description" class="field__input" rows="3"></textarea>
                </div>
                <div class="field">
                    <label class="field__label">Runtime (minutes)</label>
                    <input type="number" name="runtime" class="field__input">
                </div>
                <div class="field">
                    <label class="field__label">Air Date</label>
                    <input type="date" name="air_date" class="field__input">
                </div>
                <div class="field">
                    <label class="field__label">Upload Episode Video</label>
                    <input type="file" name="episode_video" class="field__input" accept="video/mp4,video/webm">
                </div>
                <div class="field">
                    <label class="field__label">Or Select from Media Library</label>
                    <select name="media_video_id" class="field__input">
                        <option value="">-- Select a video --</option>
                        <?php foreach ($mediaVideos as $mv): ?>
                            <option value="<?= (int) $mv['id'] ?>">
                                <?= e($mv['original_name']) ?> (<?= e($mv['folder_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form__side">
                <button type="submit" class="btn btn--primary btn--block">Add Episode</button>
            </div>
        </div>
    </form>
</div>

<?php if (empty($seasons)): ?>
    <p class="muted" style="margin-top:1rem">No seasons yet. Add an episode above to create Season 1.</p>
<?php else: ?>
    <?php foreach ($seasons as $season): ?>
        <section class="panel" style="margin-top:1rem">
            <h2 class="panel__title">Season <?= (int) $season['season_number'] ?> — <?= e($season['title'] ?? '') ?></h2>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>#</th><th>Title</th><th>Description</th><th>Runtime</th><th>Air Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach (($season['episodes'] ?? []) as $ep): ?>
                            <tr>
                                <td><?= (int) $ep['episode_number'] ?></td>
                                <td><?= e($ep['title']) ?></td>
                                <td><?= e($ep['description'] ?? '') ?></td>
                                <td><?= e($ep['runtime'] ?? '') ?></td>
                        <td><?= e($ep['air_date'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($ep['video_path'])): ?>
                                <span class="pill pill--success">Has Video</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="/admin/episodes/delete/<?= (int) $ep['id'] ?>" method="post" class="inline-form" onsubmit="return confirm('Delete this episode?')">
                                <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
                                <button type="submit" class="link link--danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($season['episodes'])): ?><tr><td colspan="7" class="muted">No episodes in this season.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>
