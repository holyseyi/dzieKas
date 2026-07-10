<?php

/**
 * Home Controller
 *
 * @package DzieKas\Controllers
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Content;
use App\Models\Genre;
use App\Models\Advertisement;
use App\Core\Database;
use App\Helpers\Session;
use App\Helpers\Seo;

class HomeController extends Controller
{
    public function index(): void
    {
        $contentModel = new Content();
        $genreModel = new Genre();
        $db = Database::getInstance();

        $hero = $contentModel->getFeatured('hero', 5);
        $featured = $contentModel->getFeatured('featured', 12);
        $latest = $contentModel->getLatest(12);
        $trending = $contentModel->getTrending(12);
        $updatedSeries = $contentModel->getRecentlyUpdatedSeries(12);
        $popularWeek = $contentModel->getPopularThisWeek(12);
        $anime = $contentModel->getByCategory('anime', 12);
        $kDramas = $contentModel->getByCategory('k-drama', 12);
        $nollywood = $contentModel->getByCountry('nigeria', 12);
        $hollywood = $contentModel->getByCountry('united-states', 12);
        $bollywood = $contentModel->getByCountry('india', 12);
        $tvShows = $contentModel->getByCategory('tv-series', 12);
        $genres = $genreModel->getAllWithCount();

        $years = $db->fetchAll(
            "SELECT DISTINCT release_year FROM content WHERE release_year IS NOT NULL AND status = 'published' ORDER BY release_year DESC LIMIT 20"
        );

        $countries = $db->fetchAll(
            "SELECT co.*, COUNT(c.id) as content_count FROM countries co
             LEFT JOIN content c ON co.id = c.country_id AND c.status = 'published'
             WHERE co.is_active = 1 GROUP BY co.id HAVING content_count > 0 ORDER BY content_count DESC"
        );

        $announcements = $db->fetchAll(
            "SELECT * FROM announcements WHERE is_active = 1 AND (starts_at IS NULL OR starts_at <= datetime('now')) AND (ends_at IS NULL OR ends_at >= datetime('now')) ORDER BY created_at DESC LIMIT 3"
        );

        $adModel = new Advertisement();

        $this->view('public/home', [
            'title' => $this->config['name'] . ' - ' . $this->config['tagline'],
            'seo' => Seo::renderMeta([
                'title' => $this->config['name'],
                'description' => $this->config['tagline'],
            ]),
            'hero' => $hero,
            'featured' => $featured,
            'latest' => $latest,
            'trending' => $trending,
            'updatedSeries' => $updatedSeries,
            'popularWeek' => $popularWeek,
            'anime' => $anime,
            'kDramas' => $kDramas,
            'nollywood' => $nollywood,
            'hollywood' => $hollywood,
            'bollywood' => $bollywood,
            'tvShows' => $tvShows,
            'genres' => $genres,
            'years' => $years,
            'countries' => $countries,
            'announcements' => $announcements,
            'headerAds' => $adModel->getByPosition('header'),
        ]);
    }

    public function toggleDarkMode(): void
    {
        $current = Session::get('dark_mode', false);
        Session::set('dark_mode', !$current);

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }
}
