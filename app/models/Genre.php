<?php

/**
 * Genre Model
 *
 * @package DzieKas\Models
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Genre extends Model
{
    protected string $table = 'genres';

    /**
     * Get all active genres with content count.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllWithCount(): array
    {
        $sql = "SELECT g.*, COUNT(cg.content_id) as content_count
                FROM genres g
                LEFT JOIN content_genres cg ON g.id = cg.genre_id
                LEFT JOIN content c ON cg.content_id = c.id AND c.status = 'published'
                WHERE g.is_active = 1
                GROUP BY g.id ORDER BY g.name";

        return $this->db->fetchAll($sql);
    }
}
