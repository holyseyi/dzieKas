<?php

/**
 * Browse Controller
 *
 * @package DzieKas\Controllers
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Content;
use App\Helpers\Seo;

class BrowseController extends Controller
{
    public function movies(): void
    {
        $this->browseByType('movie', 'Movies');
    }

    public function tvSeries(): void
    {
        $this->browseByType('series', 'TV Series');
    }

    public function anime(): void
    {
        $this->browseByType('anime', 'Anime');
    }

    public function kDramas(): void
    {
        $this->browseByType('k-drama', 'K-Dramas');
    }

    public function documentaries(): void
    {
        $this->browseByType('documentary', 'Documentaries');
    }

    public function trending(): void
    {
        $contentModel = new Content();
        $page = max(1, (int) $this->query('page', 1));
        $items = $contentModel->getTrending($this->config['per_page']);

        $this->view('public/browse', [
            'title' => 'Trending - ' . $this->config['name'],
            'seo' => Seo::renderMeta(['title' => 'Trending Now', 'description' => 'Most trending entertainment content']),
            'heading' => 'Trending Now',
            'items' => $items,
            'page' => $page,
            'totalPages' => 1,
        ]);
    }

    public function latest(): void
    {
        $contentModel = new Content();
        $page = max(1, (int) $this->query('page', 1));
        $result = $contentModel->paginate($page, $this->config['per_page'], "status = 'published'", [], 'published_at DESC');

        $this->view('public/browse', [
            'title' => 'Latest - ' . $this->config['name'],
            'heading' => 'Latest Uploads',
            'items' => $result['data'],
            'page' => $result['page'],
            'totalPages' => $result['total_pages'],
        ]);
    }

    public function genre(string $slug): void
    {
        $contentModel = new Content();
        $page = max(1, (int) $this->query('page', 1));
        $result = $contentModel->getByGenre($slug, $page, $this->config['per_page']);

        $this->view('public/browse', [
            'title' => ucfirst($slug) . ' - ' . $this->config['name'],
            'heading' => ucfirst(str_replace('-', ' ', $slug)) . ' Movies & Shows',
            'items' => $result['data'],
            'page' => $result['page'],
            'totalPages' => $result['total_pages'],
        ]);
    }

    public function country(string $slug): void
    {
        $contentModel = new Content();
        $page = max(1, (int) $this->query('page', 1));
        $items = $contentModel->getByCountry($slug, $this->config['per_page'] * $page);

        $this->view('public/browse', [
            'title' => ucfirst($slug) . ' - ' . $this->config['name'],
            'heading' => ucfirst(str_replace('-', ' ', $slug)) . ' Entertainment',
            'items' => $items,
            'page' => $page,
            'totalPages' => 1,
        ]);
    }

    public function year(string $year): void
    {
        $contentModel = new Content();
        $page = max(1, (int) $this->query('page', 1));
        $result = $contentModel->getByYear((int) $year, $page, $this->config['per_page']);

        $this->view('public/browse', [
            'title' => $year . ' Releases - ' . $this->config['name'],
            'heading' => 'Released in ' . $year,
            'items' => $result['data'],
            'page' => $result['page'],
            'totalPages' => $result['total_pages'],
        ]);
    }

    private function browseByType(string $type, string $heading): void
    {
        $contentModel = new Content();
        $page = max(1, (int) $this->query('page', 1));
        $result = $contentModel->paginate(
            $page,
            $this->config['per_page'],
            "type = ? AND status = 'published'",
            [$type],
            'published_at DESC'
        );

        $this->view('public/browse', [
            'title' => $heading . ' - ' . $this->config['name'],
            'heading' => $heading,
            'items' => $result['data'],
            'page' => $result['page'],
            'totalPages' => $result['total_pages'],
        ]);
    }
}
