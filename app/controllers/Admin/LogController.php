<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class LogController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $logs = $db->fetchAll(
            "SELECT al.*, u.username FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 200"
        );
        $this->view('admin/logs/index', ['title' => 'Audit Logs', 'logs' => $logs], 'layouts/admin');
    }
}
