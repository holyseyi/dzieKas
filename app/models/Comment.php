<?php

/**
 * Comment Model
 *
 * @package DzieKas\Models
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Comment extends Model
{
    protected string $table = 'comments';

    /**
     * Get approved comments for content.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getForContent(int $contentId, int $limit = 50): array
    {
        $sql = "SELECT cm.*, u.username, u.display_name, u.avatar
                FROM comments cm
                JOIN users u ON cm.user_id = u.id
                WHERE cm.content_id = ? AND cm.is_approved = 1 AND cm.parent_id IS NULL
                ORDER BY cm.created_at DESC LIMIT ?";

        $comments = $this->db->fetchAll($sql, [$contentId, $limit]);

        foreach ($comments as &$comment) {
            $comment['replies'] = $this->db->fetchAll(
                "SELECT cm.*, u.username, u.display_name, u.avatar
                 FROM comments cm JOIN users u ON cm.user_id = u.id
                 WHERE cm.parent_id = ? AND cm.is_approved = 1
                 ORDER BY cm.created_at ASC",
                [$comment['id']]
            );
        }

        return $comments;
    }
}
