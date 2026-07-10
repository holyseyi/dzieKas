<?php

/**
 * Advertisement Model
 *
 * @package DzieKas\Models
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Advertisement extends Model
{
    protected string $table = 'advertisements';

    /**
     * Get active ads for a position.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByPosition(string $position): array
    {
        $sql = "SELECT * FROM advertisements
                WHERE position = ? AND is_active = 1
                AND (starts_at IS NULL OR starts_at <= datetime('now'))
                AND (ends_at IS NULL OR ends_at >= datetime('now'))
                ORDER BY sort_order";

        return $this->db->fetchAll($sql, [$position]);
    }

    /**
     * Track ad impression.
     */
    public function trackImpression(int $id): void
    {
        $this->db->query('UPDATE advertisements SET impressions = impressions + 1 WHERE id = ?', [$id]);
    }

    /**
     * Track ad click.
     */
    public function trackClick(int $id): void
    {
        $this->db->query('UPDATE advertisements SET clicks = clicks + 1 WHERE id = ?', [$id]);
    }
}
