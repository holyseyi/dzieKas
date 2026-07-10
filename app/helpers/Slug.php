<?php

/**
 * URL Slug Generator
 *
 * @package DzieKas\Helpers
 */

declare(strict_types=1);

namespace App\Helpers;

use App\Core\Database;

class Slug
{
    /**
     * Generate a URL-friendly slug from text.
     */
    public static function generate(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug) ?? '';
        $slug = preg_replace('/[\s-]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        return $slug ?: 'item-' . time();
    }

    /**
     * Generate unique slug for a table.
     */
    public static function unique(string $text, string $table, ?int $excludeId = null): string
    {
        $db = Database::getInstance();
        $baseSlug = self::generate($text);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM {$table} WHERE slug = ?";
            $params = [$slug];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $existing = $db->fetchOne($sql . ' LIMIT 1', $params);

            if (!$existing) {
                break;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
