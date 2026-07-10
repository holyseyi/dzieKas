<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Slug;

class EpisodeController extends Controller
{
    public function index(string $contentId): void
    {
        $db = Database::getInstance();
        $content = $db->fetchOne('SELECT * FROM content WHERE id = ?', [$contentId]);
        $seasons = $db->fetchAll('SELECT * FROM seasons WHERE content_id = ? ORDER BY season_number', [$contentId]);
        foreach ($seasons as &$s) {
            $s['episodes'] = $db->fetchAll('SELECT * FROM episodes WHERE season_id = ? ORDER BY episode_number', [$s['id']]);
        }
        $this->view('admin/episodes/index', ['title' => 'Episodes', 'content' => $content, 'seasons' => $seasons], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        $db = Database::getInstance();
        $contentId = (int) $this->input('content_id');
        $seasonNumber = (int) $this->input('season_number', 1);

        $season = $db->fetchOne('SELECT id FROM seasons WHERE content_id = ? AND season_number = ?', [$contentId, $seasonNumber]);
        if (!$season) {
            $seasonId = $db->insert('seasons', ['content_id' => $contentId, 'season_number' => $seasonNumber, 'title' => "Season {$seasonNumber}"]);
        } else {
            $seasonId = $season['id'];
        }

        $title = Security::sanitize((string) $this->input('title', ''));
        $epNum = (int) $this->input('episode_number', 1);

        $db->insert('episodes', [
            'content_id' => $contentId,
            'season_id' => $seasonId,
            'episode_number' => $epNum,
            'title' => $title,
            'slug' => Slug::generate($title) . '-s' . $seasonNumber . 'e' . $epNum,
            'description' => $this->input('description', ''),
            'runtime' => $this->input('runtime') ?: null,
            'air_date' => $this->input('air_date') ?: null,
        ]);

        Session::flash('success', 'Episode added.');
        $this->redirect('/admin/episodes/' . $contentId);
    }

    public function delete(string $id): void
    {
        $this->validateCsrf();
        $db = Database::getInstance();
        $ep = $db->fetchOne('SELECT content_id FROM episodes WHERE id = ?', [$id]);
        $db->delete('episodes', 'id = ?', [$id]);
        Session::flash('success', 'Episode deleted.');
        $this->redirect('/admin/episodes/' . ($ep['content_id'] ?? ''));
    }
}
