<?php

/**
 * API Search Controller
 *
 * @package DzieKas\Controllers\Api
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Content;

class SearchController extends Controller
{
    public function index(): void
    {
        $query = trim((string) $this->query('q', ''));

        if (strlen($query) < 2) {
            $this->json(['success' => false, 'error' => 'Query must be at least 2 characters'], 400);
        }

        $contentModel = new Content();
        $limit = min(50, (int) $this->query('limit', 20));
        $results = $contentModel->search($query, $limit);

        $this->json([
            'success' => true,
            'query' => $query,
            'count' => count($results),
            'data' => array_map(fn ($item) => [
                'title' => $item['title'],
                'slug' => $item['slug'],
                'type' => $item['type'],
                'year' => $item['release_year'],
                'poster' => \App\Helpers\Image::url($item['poster'] ?? null),
                'url' => $this->getContentUrl($item),
            ], $results),
        ]);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function getContentUrl(array $item): string
    {
        return match ($item['type']) {
            'series', 'anime', 'k-drama' => '/series/' . $item['slug'],
            default => '/movie/' . $item['slug'],
        };
    }
}
