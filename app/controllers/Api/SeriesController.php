<?php

/**
 * API Series Controller
 *
 * @package DzieKas\Controllers\Api
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Content;
use App\Models\Episode;

class SeriesController extends Controller
{
    public function index(): void
    {
        $contentModel = new Content();
        $page = max(1, (int) $this->query('page', 1));
        $result = $contentModel->paginate(
            $page,
            $this->config['per_page'],
            "type IN ('series', 'anime', 'k-drama') AND status = 'published'",
            [],
            'published_at DESC'
        );

        $this->json([
            'success' => true,
            'data' => $result['data'],
            'meta' => [
                'page' => $result['page'],
                'total' => $result['total'],
                'total_pages' => $result['total_pages'],
            ],
        ]);
    }

    public function episodes(string $contentId): void
    {
        $episodeModel = new Episode();
        $seasons = $episodeModel->getSeasonsWithEpisodes((int) $contentId);

        $this->json(['success' => true, 'data' => $seasons]);
    }
}
