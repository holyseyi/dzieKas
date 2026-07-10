<?php

/**
 * SEO Controller - Sitemap, Robots, RSS
 *
 * @package DzieKas\Controllers
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class SeoController extends Controller
{
    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $db = Database::getInstance();
        $baseUrl = rtrim($this->config['url'], '/');

        $content = $db->fetchAll("SELECT slug, type, updated_at FROM content WHERE status = 'published' ORDER BY updated_at DESC");

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        echo "<url><loc>{$baseUrl}/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>";

        $staticPages = ['/movies', '/tv-series', '/anime', '/k-dramas', '/trending', '/latest', '/contact'];
        foreach ($staticPages as $page) {
            echo "<url><loc>{$baseUrl}{$page}</loc><changefreq>daily</changefreq><priority>0.8</priority></url>";
        }

        foreach ($content as $item) {
            $path = in_array($item['type'], ['series', 'anime', 'k-drama']) ? '/series/' : '/movie/';
            $lastmod = date('Y-m-d', strtotime($item['updated_at']));
            echo "<url><loc>{$baseUrl}{$path}{$item['slug']}</loc><lastmod>{$lastmod}</lastmod><changefreq>weekly</changefreq><priority>0.7</priority></url>";
        }

        echo '</urlset>';
        exit;
    }

    public function robots(): void
    {
        header('Content-Type: text/plain');
        $baseUrl = rtrim($this->config['url'], '/');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /api/\n";
        echo "Sitemap: {$baseUrl}/sitemap.xml\n";
        exit;
    }

    public function rss(): void
    {
        header('Content-Type: application/rss+xml; charset=utf-8');
        $db = Database::getInstance();
        $baseUrl = rtrim($this->config['url'], '/');

        $items = $db->fetchAll(
            "SELECT title, slug, description, published_at, type FROM content WHERE status = 'published' ORDER BY published_at DESC LIMIT 50"
        );

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<rss version="2.0"><channel>';
        echo '<title>' . htmlspecialchars($this->config['name']) . '</title>';
        echo '<link>' . $baseUrl . '</link>';
        echo '<description>' . htmlspecialchars($this->config['tagline']) . '</description>';

        foreach ($items as $item) {
            $path = in_array($item['type'], ['series', 'anime', 'k-drama']) ? '/series/' : '/movie/';
            echo '<item>';
            echo '<title>' . htmlspecialchars($item['title']) . '</title>';
            echo '<link>' . $baseUrl . $path . $item['slug'] . '</link>';
            echo '<description>' . htmlspecialchars(mb_substr($item['description'] ?? '', 0, 300)) . '</description>';
            echo '<pubDate>' . date('r', strtotime($item['published_at'])) . '</pubDate>';
            echo '</item>';
        }

        echo '</channel></rss>';
        exit;
    }
}
