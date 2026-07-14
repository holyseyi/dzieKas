<?php

/**
 * Global view helper functions.
 *
 * Loaded once from bootstrap so every template can use short helpers
 * without repeating fully-qualified static calls.
 *
 * @package DzieKas\Helpers
 */

declare(strict_types=1);

use App\Helpers\Security;
use App\Helpers\Image;
use App\Helpers\Video;

if (!function_exists('e')) {
    /**
     * Escape a value for safe HTML output.
     */
    function e(mixed $value): string
    {
        return Security::e($value === null ? '' : (string) $value);
    }
}

if (!function_exists('img')) {
    /**
     * Resolve an uploaded image path to a public URL (with placeholder fallback).
     */
    function img(?string $path): string
    {
        return Image::url($path);
    }
}

if (!function_exists('video_url')) {
    /**
     * Resolve an uploaded video path to a public URL.
     */
    function video_url(?string $path): string
    {
        return Video::url($path);
    }
}

if (!function_exists('content_url')) {
    /**
     * Build the canonical detail URL for a content row.
     *
     * @param array<string, mixed> $item
     */
    function content_url(array $item): string
    {
        $type = $item['type'] ?? 'movie';
        $slug = $item['slug'] ?? '';

        return match ($type) {
            'series', 'anime', 'k-drama' => '/series/' . $slug,
            'video' => '/movie/' . $slug,
            default => '/movie/' . $slug,
        };
    }
}

if (!function_exists('str_excerpt')) {
    /**
     * Trim text to a maximum length with an ellipsis.
     */
    function str_excerpt(?string $text, int $length = 140): string
    {
        $text = trim(strip_tags((string) $text));
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . '…';
    }
}

if (!function_exists('type_label')) {
    /**
     * Human-friendly label for a content type.
     */
    function type_label(?string $type): string
    {
        return match ($type) {
            'series' => 'TV Series',
            'anime' => 'Anime',
            'k-drama' => 'K-Drama',
            'documentary' => 'Documentary',
            'video' => 'Video',
            'movie' => 'Movie',
            default => ucfirst((string) $type),
        };
    }
}

if (!function_exists('time_ago')) {
    /**
     * Relative "x ago" formatting for a datetime string.
     */
    function time_ago(?string $datetime): string
    {
        if (!$datetime) {
            return '';
        }

        $ts = strtotime($datetime);
        if ($ts === false) {
            return (string) $datetime;
        }

        $diff = time() - $ts;
        if ($diff < 60) {
            return 'just now';
        }

        $units = [
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
        ];

        foreach ($units as $secs => $name) {
            if ($diff >= $secs) {
                $count = (int) floor($diff / $secs);
                return $count . ' ' . $name . ($count > 1 ? 's' : '') . ' ago';
            }
        }

        return 'just now';
    }
}
