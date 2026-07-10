<?php

/**
 * SEO Helper - Meta tags, OpenGraph, Schema.org
 *
 * @package DzieKas\Helpers
 */

declare(strict_types=1);

namespace App\Helpers;

class Seo
{
    /**
     * @param array<string, mixed> $data
     */
    public static function renderMeta(array $data): string
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $title = Security::e($data['title'] ?? $config['name']);
        $description = Security::e($data['description'] ?? $config['tagline']);
        $canonical = Security::e($data['canonical'] ?? $config['url']);
        $image = Security::e($data['image'] ?? $config['url'] . '/assets/images/og-default.jpg');
        $type = Security::e($data['type'] ?? 'website');

        $html = "<title>{$title}</title>\n";
        $html .= "<meta name=\"description\" content=\"{$description}\">\n";
        $html .= "<link rel=\"canonical\" href=\"{$canonical}\">\n";
        $html .= "<meta property=\"og:title\" content=\"{$title}\">\n";
        $html .= "<meta property=\"og:description\" content=\"{$description}\">\n";
        $html .= "<meta property=\"og:url\" content=\"{$canonical}\">\n";
        $html .= "<meta property=\"og:image\" content=\"{$image}\">\n";
        $html .= "<meta property=\"og:type\" content=\"{$type}\">\n";
        $html .= "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
        $html .= "<meta name=\"twitter:title\" content=\"{$title}\">\n";
        $html .= "<meta name=\"twitter:description\" content=\"{$description}\">\n";
        $html .= "<meta name=\"twitter:image\" content=\"{$image}\">\n";

        if (!empty($data['schema'])) {
            $html .= '<script type="application/ld+json">' . json_encode($data['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
        }

        return $html;
    }

    /**
     * Generate Movie Schema.org JSON-LD.
     *
     * @param array<string, mixed> $movie
     * @return array<string, mixed>
     */
    public static function movieSchema(array $movie): array
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Movie',
            'name' => $movie['title'] ?? '',
            'description' => $movie['description'] ?? '',
            'image' => Image::url($movie['poster'] ?? null),
            'datePublished' => $movie['release_date'] ?? '',
            'duration' => isset($movie['runtime']) ? 'PT' . $movie['runtime'] . 'M' : null,
            'aggregateRating' => isset($movie['imdb_rating']) ? [
                '@type' => 'AggregateRating',
                'ratingValue' => $movie['imdb_rating'],
                'bestRating' => '10',
                'ratingCount' => $movie['rating_count'] ?? 1,
            ] : null,
            'url' => rtrim($config['url'], '/') . '/movie/' . ($movie['slug'] ?? ''),
        ];
    }
}
