<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Helpers\Session;

class BackupController extends Controller
{
    public function index(): void
    {
        $backupDir = dirname(__DIR__, 3) . '/storage/backups';
        $backups = [];
        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.sqlite');
            foreach ($files as $file) {
                $backups[] = ['name' => basename($file), 'size' => filesize($file), 'date' => date('Y-m-d H:i:s', filemtime($file))];
            }
        }
        $this->view('admin/backup/index', ['title' => 'Database Backup', 'backups' => $backups], 'layouts/admin');
    }

    public function create(): void
    {
        $this->validateCsrf();
        $dbPath = dirname(__DIR__, 3) . '/database/dzieKas.sqlite';
        $backupDir = dirname(__DIR__, 3) . '/storage/backups';
        $backupFile = $backupDir . '/backup_' . date('Y-m-d_His') . '.sqlite';

        if (copy($dbPath, $backupFile)) {
            Session::flash('success', 'Backup created: ' . basename($backupFile));
        } else {
            Session::flash('error', 'Backup failed.');
        }
        $this->redirect('/admin/backup');
    }

    public function restore(): void
    {
        $this->validateCsrf();
        $backupName = basename((string) $this->input('backup_file', ''));
        $backupPath = dirname(__DIR__, 3) . '/storage/backups/' . $backupName;
        $dbPath = dirname(__DIR__, 3) . '/database/dzieKas.sqlite';

        if (file_exists($backupPath) && copy($backupPath, $dbPath)) {
            Session::flash('success', 'Database restored from ' . $backupName);
        } else {
            Session::flash('error', 'Restore failed.');
        }
        $this->redirect('/admin/backup');
    }
}
