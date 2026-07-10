<?php

/**
 * Search Controller
 *
 * @package DzieKas\Controllers
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Content;

class SearchController extends Controller
{
    public function index(): void
    {
        $query = trim((string) $this->query('q', ''));
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax || $this->query('ajax')) {
            if (strlen($query) < 2) {
                $this->json(['results' => []]);
                return;
            }

            $contentModel = new Content();
            $results = $contentModel->search($query, 10);

            $formatted = array_map(fn ($item) => [
                'title' => $item['title'],
                'slug' => $item['slug'],
                'type' => $item['type'],
                'year' => $item['release_year'],
                'poster' => \App\Helpers\Image::url($item['poster'] ?? null),
                'url' => $this->getContentUrl($item),
            ], $results);

            $this->json(['results' => $formatted, 'query' => $query]);
            return;
        }

        $results = [];
        if (strlen($query) >= 2) {
            $contentModel = new Content();
            $results = $contentModel->search($query, $this->config['per_page']);
        }

        $this->view('public/search', [
            'title' => 'Search: ' . $query . ' - ' . $this->config['name'],
            'query' => $query,
            'results' => $results,
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
