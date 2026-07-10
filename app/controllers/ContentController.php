<?php

/**
 * Content Detail Controller
 *
 * @package DzieKas\Controllers
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Content;
use App\Models\Episode;
use App\Models\Comment;
use App\Helpers\Seo;
use App\Helpers\Session;
use App\Core\Database;

class ContentController extends Controller
{
    public function show(string $slug): void
    {
        $contentModel = new Content();
        $content = $contentModel->getFull($slug);

        if (!$content) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Not Found']);
            return;
        }

        $contentModel->incrementViews((int) $content['id']);

        $commentModel = new Comment();
        $comments = $commentModel->getForContent((int) $content['id']);
        $related = $contentModel->getRelated((int) $content['id']);

        $isBookmarked = false;
        $user = Session::get('user');
        if ($user) {
            $db = Database::getInstance();
            $bookmark = $db->fetchOne(
                'SELECT id FROM bookmarks WHERE user_id = ? AND content_id = ?',
                [$user['id'], $content['id']]
            );
            $isBookmarked = (bool) $bookmark;

            // Record watch history
            $db->query(
                'INSERT INTO watch_history (user_id, content_id, watched_at) VALUES (?, ?, datetime("now"))',
                [$user['id'], $content['id']]
            );
        }

        $this->view('public/movie-detail', [
            'title' => $content['title'] . ' - ' . $this->config['name'],
            'seo' => Seo::renderMeta([
                'title' => $content['title'],
                'description' => mb_substr($content['description'] ?? '', 0, 160),
                'canonical' => $this->config['url'] . '/movie/' . $content['slug'],
                'image' => \App\Helpers\Image::url($content['poster'] ?? null),
                'type' => 'video.movie',
                'schema' => Seo::movieSchema($content),
            ]),
            'content' => $content,
            'comments' => $comments,
            'related' => $related,
            'isBookmarked' => $isBookmarked,
        ]);
    }

    public function series(string $slug): void
    {
        $contentModel = new Content();
        $content = $contentModel->getFull($slug);

        if (!$content) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Not Found']);
            return;
        }

        $contentModel->incrementViews((int) $content['id']);

        $episodeModel = new Episode();
        $seasons = $episodeModel->getSeasonsWithEpisodes((int) $content['id']);

        $commentModel = new Comment();
        $comments = $commentModel->getForContent((int) $content['id']);
        $related = $contentModel->getRelated((int) $content['id']);

        $continueWatching = null;
        $user = Session::get('user');
        if ($user) {
            $db = Database::getInstance();
            $continueWatching = $db->fetchOne(
                "SELECT wh.*, e.title as episode_title, e.episode_number, e.slug as episode_slug, s.season_number
                 FROM watch_history wh
                 JOIN episodes e ON wh.episode_id = e.id
                 JOIN seasons s ON e.season_id = s.id
                 WHERE wh.user_id = ? AND wh.content_id = ?
                 ORDER BY wh.watched_at DESC LIMIT 1",
                [$user['id'], $content['id']]
            );
        }

        $this->view('public/series-detail', [
            'title' => $content['title'] . ' - ' . $this->config['name'],
            'seo' => Seo::renderMeta([
                'title' => $content['title'],
                'description' => mb_substr($content['description'] ?? '', 0, 160),
                'type' => 'video.tv_show',
            ]),
            'content' => $content,
            'seasons' => $seasons,
            'comments' => $comments,
            'related' => $related,
            'continueWatching' => $continueWatching,
        ]);
    }
}
