<?php

/**
 * Admin Content Management Controller
 *
 * @package DzieKas\Controllers\Admin
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Content;
use App\Models\AuditLog;
use App\Helpers\Slug;
use App\Helpers\Image;
use App\Helpers\Video;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Validator;

class ContentController extends Controller
{
    public function index(): void
    {
        $contentModel = new Content();
        $page = max(1, (int) $this->query('page', 1));
        $type = $this->query('type');
        $search = $this->query('search');

        $where = '1=1';
        $params = [];

        if ($type) {
            $where .= ' AND type = ?';
            $params[] = $type;
        }
        if ($search) {
            $where .= ' AND title LIKE ?';
            $params[] = '%' . $search . '%';
        }

        $result = $contentModel->paginate($page, $this->config['admin_per_page'], $where, $params);

        $this->view('admin/content/index', [
            'title' => 'Manage Content',
            'items' => $result['data'],
            'page' => $result['page'],
            'totalPages' => $result['total_pages'],
            'total' => $result['total'],
            'type' => $type,
            'search' => $search,
        ], 'layouts/admin');
    }

    public function create(): void
    {
        $db = Database::getInstance();
        $this->view('admin/content/form', [
            'title' => 'Add Content',
            'content' => null,
            'categories' => $db->fetchAll('SELECT * FROM categories WHERE is_active = 1 ORDER BY name'),
            'genres' => $db->fetchAll('SELECT * FROM genres WHERE is_active = 1 ORDER BY name'),
            'countries' => $db->fetchAll('SELECT * FROM countries WHERE is_active = 1 ORDER BY name'),
            'languages' => $db->fetchAll('SELECT * FROM languages WHERE is_active = 1 ORDER BY name'),
            'actors' => $db->fetchAll('SELECT * FROM actors WHERE is_active = 1 ORDER BY name'),
            'directors' => $db->fetchAll('SELECT * FROM directors WHERE is_active = 1 ORDER BY name'),
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        $user = Session::get('user');
        $db = Database::getInstance();

        $title = Security::sanitize((string) $this->input('title', ''));
        $slug = Slug::unique($title, 'content');

        $data = [
            'type' => Security::sanitize((string) $this->input('type', 'movie')),
            'category_id' => $this->input('category_id') ?: null,
            'title' => $title,
            'original_title' => Security::sanitize((string) $this->input('original_title', '')),
            'slug' => $slug,
            'description' => $this->input('description', ''),
            'synopsis' => $this->input('synopsis', ''),
            'runtime' => $this->input('runtime') ?: null,
            'release_date' => $this->input('release_date') ?: null,
            'release_year' => $this->input('release_year') ?: null,
            'imdb_id' => Security::sanitize((string) $this->input('imdb_id', '')),
            'imdb_rating' => $this->input('imdb_rating') ?: null,
            'country_id' => $this->input('country_id') ?: null,
            'language_id' => $this->input('language_id') ?: null,
            'status' => Security::sanitize((string) $this->input('status', 'draft')),
            'is_featured' => $this->input('is_featured') ? 1 : 0,
            'trailer_url' => Security::sanitize((string) $this->input('trailer_url', '')),
            'published_at' => $this->input('status') === 'published' ? date('Y-m-d H:i:s') : null,
        ];

        if (!empty($_FILES['poster']['name'])) {
            $poster = Image::upload($_FILES['poster'], 'posters', $this->config['image_sizes']['poster']);
            if ($poster) {
                $data['poster'] = $poster;
            }
        }

        if (!empty($_FILES['banner']['name'])) {
            $banner = Image::upload($_FILES['banner'], 'banners', $this->config['image_sizes']['banner']);
            if ($banner) {
                $data['banner'] = $banner;
            }
        }

        if (!empty($_FILES['video_file']['name'])) {
            $video = Video::upload($_FILES['video_file'], 'videos');
            if ($video) {
                $data['video_path'] = $video;
                $data['video_type'] = 'upload';
            }
        }

        $contentModel = new Content();
        $contentId = $contentModel->create($data);

        // Sync genres
        $genreIds = $this->input('genres', []);
        if (is_array($genreIds)) {
            foreach ($genreIds as $genreId) {
                $db->insert('content_genres', ['content_id' => $contentId, 'genre_id' => $genreId]);
            }
        }

        // Sync actors
        $actorIds = $this->input('actors', []);
        if (is_array($actorIds)) {
            foreach ($actorIds as $actorId) {
                $db->insert('content_actors', ['content_id' => $contentId, 'actor_id' => $actorId]);
            }
        }

        // Sync directors
        $directorIds = $this->input('directors', []);
        if (is_array($directorIds)) {
            foreach ($directorIds as $directorId) {
                $db->insert('content_directors', ['content_id' => $contentId, 'director_id' => $directorId]);
            }
        }

        // Downloads
        $downloadTitles = $this->input('download_titles', []);
        $downloadUrls = $this->input('download_urls', []);
        $downloadQualities = $this->input('download_qualities', []);
        if (is_array($downloadUrls)) {
            foreach ($downloadUrls as $i => $url) {
                if ($url) {
                    $db->insert('downloads', [
                        'content_id' => $contentId,
                        'title' => $downloadTitles[$i] ?? 'Download',
                        'url' => Security::sanitize($url),
                        'quality' => $downloadQualities[$i] ?? '720p',
                    ]);
                }
            }
        }

        $auditLog = new AuditLog();
        $auditLog->log((int) $user['id'], 'create_content', 'content', $contentId, null, $data);

        Session::flash('success', 'Content created successfully.');
        $this->redirect('/admin/content');
    }

    public function edit(string $id): void
    {
        $db = Database::getInstance();
        $contentModel = new Content();
        $content = $contentModel->find((int) $id);

        if (!$content) {
            Session::flash('error', 'Content not found.');
            $this->redirect('/admin/content');
        }

        $content['genres'] = $db->fetchAll('SELECT genre_id FROM content_genres WHERE content_id = ?', [$id]);
        $content['actors'] = $db->fetchAll('SELECT actor_id FROM content_actors WHERE content_id = ?', [$id]);
        $content['directors'] = $db->fetchAll('SELECT director_id FROM content_directors WHERE content_id = ?', [$id]);
        $content['downloads'] = $db->fetchAll('SELECT * FROM downloads WHERE content_id = ? AND episode_id IS NULL', [$id]);

        $this->view('admin/content/form', [
            'title' => 'Edit Content',
            'content' => $content,
            'categories' => $db->fetchAll('SELECT * FROM categories WHERE is_active = 1 ORDER BY name'),
            'genres' => $db->fetchAll('SELECT * FROM genres WHERE is_active = 1 ORDER BY name'),
            'countries' => $db->fetchAll('SELECT * FROM countries WHERE is_active = 1 ORDER BY name'),
            'languages' => $db->fetchAll('SELECT * FROM languages WHERE is_active = 1 ORDER BY name'),
            'actors' => $db->fetchAll('SELECT * FROM actors WHERE is_active = 1 ORDER BY name'),
            'directors' => $db->fetchAll('SELECT * FROM directors WHERE is_active = 1 ORDER BY name'),
        ], 'layouts/admin');
    }

    public function update(string $id): void
    {
        $this->validateCsrf();
        $user = Session::get('user');
        $contentModel = new Content();
        $content = $contentModel->find((int) $id);

        if (!$content) {
            Session::flash('error', 'Content not found.');
            $this->redirect('/admin/content');
        }

        $title = Security::sanitize((string) $this->input('title', ''));
        $data = [
            'type' => Security::sanitize((string) $this->input('type', 'movie')),
            'category_id' => $this->input('category_id') ?: null,
            'title' => $title,
            'original_title' => Security::sanitize((string) $this->input('original_title', '')),
            'slug' => Slug::unique($title, 'content', (int) $id),
            'description' => $this->input('description', ''),
            'runtime' => $this->input('runtime') ?: null,
            'release_date' => $this->input('release_date') ?: null,
            'release_year' => $this->input('release_year') ?: null,
            'imdb_rating' => $this->input('imdb_rating') ?: null,
            'country_id' => $this->input('country_id') ?: null,
            'language_id' => $this->input('language_id') ?: null,
            'status' => Security::sanitize((string) $this->input('status', 'draft')),
            'is_featured' => $this->input('is_featured') ? 1 : 0,
            'trailer_url' => Security::sanitize((string) $this->input('trailer_url', '')),
            'video_type' => $this->input('remove_video') ? 'external' : ($this->input('video_type', 'upload')),
        ];

        if (!empty($_FILES['poster']['name'])) {
            $poster = Image::upload($_FILES['poster'], 'posters', $this->config['image_sizes']['poster']);
            if ($poster) {
                $data['poster'] = $poster;
            }
        }

        if (!empty($_FILES['video_file']['name'])) {
            $video = Video::upload($_FILES['video_file'], 'videos');
            if ($video) {
                $data['video_path'] = $video;
            }
        } elseif ($this->input('remove_video')) {
            $data['video_path'] = null;
        }

        $contentModel->update((int) $id, $data);

        $auditLog = new AuditLog();
        $auditLog->log((int) $user['id'], 'update_content', 'content', (int) $id, $content, $data);

        Session::flash('success', 'Content updated successfully.');
        $this->redirect('/admin/content');
    }

    public function delete(string $id): void
    {
        $this->validateCsrf();
        $user = Session::get('user');
        $contentModel = new Content();
        $contentModel->delete((int) $id);

        $auditLog = new AuditLog();
        $auditLog->log((int) $user['id'], 'delete_content', 'content', (int) $id);

        Session::flash('success', 'Content deleted.');
        $this->redirect('/admin/content');
    }
}
