<?php /** @var array<string,mixed> $content */ ?>
<div class="admin-form">
    <form action="<?= e($content ? '/admin/content/update/' . (int) $content['id'] : '/admin/content/store') ?>" method="post" enctype="multipart/form-data" class="form">
        <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">

        <div class="form__grid">
            <div class="form__main">
                <section class="panel">
                    <h2 class="panel__title">Basic Information</h2>
                    <div class="field">
                        <label class="field__label">Title</label>
                        <input type="text" name="title" class="field__input" value="<?= e($content['title'] ?? '') ?>" required>
                    </div>
                    <div class="field">
                        <label class="field__label">Type</label>
                        <select name="type" class="field__input">
                            <?php foreach (['movie', 'series', 'anime', 'k-drama', 'documentary', 'video'] as $t): ?>
                                <option value="<?= e($t) ?>" <?= ($content['type'] ?? 'movie') === $t ? 'selected' : '' ?>><?= e(ucfirst($t)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field__label">Category</label>
                        <select name="category_id" class="field__input">
                            <option value="">-- None --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>" <?= (int) ($content['category_id'] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field__label">Genres</label>
                        <select name="genres[]" class="field__input" multiple size="6">
                            <?php foreach ($genres as $g): ?>
                                <option value="<?= (int) $g['id'] ?>" <?= in_array((int) $g['id'], array_map('intval', array_column($content['genres'] ?? [], 'genre_id')), true) ? 'selected' : '' ?>><?= e($g['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field__help">Hold Ctrl/Cmd to select multiple</span>
                    </div>
                    <div class="field">
                        <label class="field__label">Actors</label>
                        <select name="actors[]" class="field__input" multiple size="6">
                            <?php foreach ($actors as $a): ?>
                                <option value="<?= (int) $a['id'] ?>" <?= in_array((int) $a['id'], array_map('intval', array_column($content['actors'] ?? [], 'actor_id')), true) ? 'selected' : '' ?>><?= e($a['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field__help">Hold Ctrl/Cmd to select multiple</span>
                    </div>
                    <div class="field">
                        <label class="field__label">Directors</label>
                        <select name="directors[]" class="field__input" multiple size="6">
                            <?php foreach ($directors as $d): ?>
                                <option value="<?= (int) $d['id'] ?>" <?= in_array((int) $d['id'], array_map('intval', array_column($content['directors'] ?? [], 'director_id')), true) ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field__help">Hold Ctrl/Cmd to select multiple</span>
                    </div>
                    <div class="field">
                        <label class="field__label">Country</label>
                        <select name="country_id" class="field__input">
                            <option value="">-- Select Country --</option>
                            <?php foreach ($countries as $c): ?>
                                <option value="<?= (int) $c['id'] ?>" <?= (int) ($content['country_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field__label">Language</label>
                        <select name="language_id" class="field__input">
                            <option value="">-- Select Language --</option>
                            <?php foreach ($languages as $l): ?>
                                <option value="<?= (int) $l['id'] ?>" <?= (int) ($content['language_id'] ?? 0) === (int) $l['id'] ? 'selected' : '' ?>><?= e($l['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field__label">Description</label>
                        <textarea name="description" class="field__input" rows="3"><?= e($content['description'] ?? '') ?></textarea>
                    </div>
                    <div class="field">
                        <label class="field__label">Synopsis</label>
                        <textarea name="synopsis" class="field__input" rows="5"><?= e($content['synopsis'] ?? '') ?></textarea>
                    </div>
                </section>

                <section class="panel">
                    <h2 class="panel__title">Media</h2>
                    <div class="field">
                        <label class="field__label">Poster Image</label>
                        <input type="file" name="poster" class="field__input" accept="image/*">
                        <?php if (!empty($content['poster'])): ?>
                            <img src="<?= e(img($content['poster'])) ?>" class="form__preview" alt="Poster">
                        <?php endif; ?>
                    </div>
                    <div class="field">
                        <label class="field__label">Banner Image</label>
                        <input type="file" name="banner" class="field__input" accept="image/*">
                        <?php if (!empty($content['banner'])): ?>
                            <img src="<?= e(img($content['banner'])) ?>" class="form__preview" alt="Banner">
                        <?php endif; ?>
                    </div>
                    <div class="field">
                        <label class="field__label">Upload Video (MP4 / WebM)</label>
                        <input type="file" name="video_file" class="field__input" accept="video/mp4,video/webm">
                        <?php if (!empty($content['video_path'])): ?>
                            <video src="<?= e(video_url($content['video_path'])) ?>" class="form__preview" controls></video>
                            <label class="field__check">
                                <input type="checkbox" name="remove_video" value="1">
                                <span>Remove uploaded video</span>
                            </label>
                        <?php endif; ?>
                    </div>
                    <div class="field">
                        <label class="field__label">Or Select from Media Library</label>
                        <select name="media_video_id" class="field__input">
                            <option value="">-- Select a video --</option>
                            <?php foreach ($mediaVideos as $mv): ?>
                                <option value="<?= (int) $mv['id'] ?>" <?= (!empty($content['video_path']) && $content['video_path'] === $mv['path']) ? 'selected' : '' ?>>
                                    <?= e($mv['original_name']) ?> (<?= e(format_bytes((int) $mv['file_size'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <a class="link" href="/admin/media" target="_blank" rel="noopener">Open Media Library ↗</a>
                    </div>
                    <div class="field">
                        <label class="field__label">Trailer URL (YouTube, etc.)</label>
                        <input type="url" name="trailer_url" class="field__input" value="<?= e($content['trailer_url'] ?? '') ?>">
                    </div>
                </section>
            </div>

            <div class="form__side">
                <section class="panel">
                    <h2 class="panel__title">Publish</h2>
                    <div class="field">
                        <label class="field__label">Status</label>
                        <select name="status" class="field__input">
                            <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                                <option value="<?= e($s) ?>" <?= ($content['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field__check">
                            <input type="checkbox" name="is_featured" value="1" <?= !empty($content['is_featured']) ? 'checked' : '' ?>>
                            <span>Featured on homepage</span>
                        </label>
                    </div>
                    <div class="field">
                        <label class="field__label">Runtime (minutes)</label>
                        <input type="number" name="runtime" class="field__input" value="<?= e($content['runtime'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label class="field__label">Release Date</label>
                        <input type="date" name="release_date" class="field__input" value="<?= e($content['release_date'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label class="field__label">Release Year</label>
                        <input type="number" name="release_year" class="field__input" value="<?= e($content['release_year'] ?? '') ?>">
                    </div>
                </section>

                <section class="panel">
                    <h2 class="panel__title">Details</h2>
                    <div class="field">
                        <label class="field__label">Original Title</label>
                        <input type="text" name="original_title" class="field__input" value="<?= e($content['original_title'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label class="field__label">IMDb ID</label>
                        <input type="text" name="imdb_id" class="field__input" value="<?= e($content['imdb_id'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label class="field__label">IMDb Rating</label>
                        <input type="number" step="0.1" name="imdb_rating" class="field__input" value="<?= e($content['imdb_rating'] ?? '') ?>">
                    </div>
                </section>

                <div class="form__actions">
                    <button type="submit" class="btn btn--primary btn--block"><?= $content ? 'Update' : 'Create' ?> Content</button>
                    <a href="/admin/content" class="btn btn--ghost btn--block">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
