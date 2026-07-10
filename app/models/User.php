<?php

/**
 * User Model
 *
 * @package DzieKas\Models
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    /**
     * Find user by email.
     *
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT u.*, r.name as role, r.slug as role_slug
                FROM users u JOIN roles r ON u.role_id = r.id
                WHERE u.email = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$email]);

        return $result ?: null;
    }

    /**
     * Find user by username.
     *
     * @return array<string, mixed>|null
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT u.*, r.name as role, r.slug as role_slug
                FROM users u JOIN roles r ON u.role_id = r.id
                WHERE u.username = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$username]);

        return $result ?: null;
    }

    /**
     * Get user with role info.
     *
     * @return array<string, mixed>|null
     */
    public function findWithRole(int $id): ?array
    {
        $sql = "SELECT u.*, r.name as role, r.slug as role_slug
                FROM users u JOIN roles r ON u.role_id = r.id
                WHERE u.id = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$id]);

        return $result ?: null;
    }

    /**
     * Update reset token.
     */
    public function setResetToken(int $userId, string $token): void
    {
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->update($userId, [
            'reset_token' => $token,
            'reset_token_expires' => $expires,
        ]);
    }

    /**
     * Find user by reset token.
     *
     * @return array<string, mixed>|null
     */
    public function findByResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expires > datetime('now') LIMIT 1";
        $result = $this->db->fetchOne($sql, [$token]);

        return $result ?: null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getBookmarks(int $userId, int $limit = 20): array
    {
        $sql = "SELECT c.* FROM content c
                JOIN bookmarks b ON c.id = b.content_id
                WHERE b.user_id = ? ORDER BY b.created_at DESC LIMIT ?";

        return $this->db->fetchAll($sql, [$userId, $limit]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getWatchHistory(int $userId, int $limit = 20): array
    {
        $sql = "SELECT c.*, wh.progress, wh.completed, wh.watched_at, e.title as episode_title, e.episode_number
                FROM watch_history wh
                JOIN content c ON wh.content_id = c.id
                LEFT JOIN episodes e ON wh.episode_id = e.id
                WHERE wh.user_id = ?
                ORDER BY wh.watched_at DESC LIMIT ?";

        return $this->db->fetchAll($sql, [$userId, $limit]);
    }
}
