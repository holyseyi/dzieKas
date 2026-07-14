<?php

/**
 * Admin Dashboard Controller
 *
 * @package DzieKas\Controllers\Admin
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();

        $stats = [
            'total_content' => (int) ($db->fetchOne("SELECT COUNT(*) as c FROM content")['c'] ?? 0),
            'total_users' => (int) ($db->fetchOne("SELECT COUNT(*) as c FROM users")['c'] ?? 0),
            'total_comments' => (int) ($db->fetchOne("SELECT COUNT(*) as c FROM comments")['c'] ?? 0),
            'total_views' => (int) ($db->fetchOne("SELECT SUM(view_count) as c FROM content")['c'] ?? 0),
            'pending_comments' => (int) ($db->fetchOne("SELECT COUNT(*) as c FROM comments WHERE is_approved = 0")['c'] ?? 0),
            'pending_reports' => (int) ($db->fetchOne("SELECT COUNT(*) as c FROM link_reports WHERE status = 'pending'")['c'] ?? 0),
        ];

        $recentContent = $db->fetchAll(
            "SELECT id, title, type, status, view_count, created_at FROM content ORDER BY created_at DESC LIMIT 10"
        );

        $topContent = $db->fetchAll(
            "SELECT id, title, type, view_count, download_count FROM content WHERE status = 'published' ORDER BY view_count DESC LIMIT 10"
        );

        $recentUsers = $db->fetchAll(
            "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5"
        );

        $recentVideos = $db->fetchAll(
            "SELECT mf.id, mf.original_name, mf.path, mf.file_size, mf.created_at, mf2.name as folder_name
             FROM media_files mf
             JOIN media_folders mf2 ON mf.folder_id = mf2.id
             WHERE mf.mime_type LIKE 'video/%'
             ORDER BY mf.created_at DESC LIMIT 10"
        );

        // Chart data - views last 7 days
        $chartData = $db->fetchAll(
            "SELECT date(created_at) as date, COUNT(*) as count FROM audit_logs
             WHERE action = 'view' AND created_at >= datetime('now', '-7 days')
             GROUP BY date(created_at) ORDER BY date"
        );

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'recentContent' => $recentContent,
            'topContent' => $topContent,
            'recentUsers' => $recentUsers,
            'recentVideos' => $recentVideos,
            'chartData' => $chartData,
        ], 'layouts/admin');
    }
}
