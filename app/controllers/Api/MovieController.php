<?php

/**
 * API Movie Controller
 *
 * @package DzieKas\Controllers\Api
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Content;

class MovieController extends Controller
{
    public function index(): void
    {
        $contentModel = new Content();
        $page = max(1, (int) $this->query('page', 1));
        $result = $contentModel->paginate(
            $page,
            $this->config['per_page'],
            "type = 'movie' AND status = 'published'",
            [],
            'published_at DESC'
        );

        $this->json([
            'success' => true,
            'data' => $this->formatItems($result['data']),
            'meta' => [
                'page' => $result['page'],
                'per_page' => $result['per_page'],
                'total' => $result['total'],
                'total_pages' => $result['total_pages'],
            ],
        ]);
    }

    public function latest(): void
    {
        $contentModel = new Content();
        $limit = min(50, (int) $this->query('limit', 12));
        $items = $contentModel->getLatest($limit);

        $this->json(['success' => true, 'data' => $this->formatItems($items)]);
    }

    public function trending(): void
    {
        $contentModel = new Content();
        $limit = min(50, (int) $this->query('limit', 12));
        $days = (int) $this->query('days', $this->config['trending_days']);
        $items = $contentModel->getTrending($limit, $days);

        $this->json(['success' => true, 'data' => $this->formatItems($items)]);
    }

    public function show(string $slug): void
    {
        $contentModel = new Content();
        $content = $contentModel->getFull($slug);

        if (!$content) {
            $this->json(['success' => false, 'error' => 'Not found'], 404);
        }

        $this->json(['success' => true, 'data' => $content]);
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private function formatItems(array $items): array
    {
        return array_map(fn ($item) => [
            'id' => $item['id'],
            'title' => $item['title'],
            'slug' => $item['slug'],
            'type' => $item['type'],
            'poster' => \App\Helpers\Image::url($item['poster'] ?? null),
            'release_year' => $item['release_year'],
            'imdb_rating' => $item['imdb_rating'],
            'view_count' => $item['view_count'],
            'published_at' => $item['published_at'],
        ], $items);
    }
}
