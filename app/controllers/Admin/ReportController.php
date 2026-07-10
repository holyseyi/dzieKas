<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Session;

class ReportController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $reports = $db->fetchAll(
            "SELECT lr.*, u.username, d.title as download_title FROM link_reports lr
             LEFT JOIN users u ON lr.user_id = u.id LEFT JOIN downloads d ON lr.download_id = d.id
             ORDER BY lr.created_at DESC LIMIT 100"
        );
        $this->view('admin/reports/index', ['title' => 'Link Reports', 'reports' => $reports], 'layouts/admin');
    }
}
