<?php

/**
 * Maintenance Mode Middleware
 *
 * @package DzieKas\Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Database;

class MaintenanceMiddleware
{
    public function handle(): void
    {
        $db = Database::getInstance();
        $setting = $db->fetchOne("SELECT value FROM site_settings WHERE key = 'maintenance_mode'");

        if (($setting['value'] ?? '0') === '1') {
            $user = \App\Helpers\Session::get('user');
            if (!$user || !in_array($user['role'] ?? '', ['admin', 'super_admin'], true)) {
                http_response_code(503);
                require dirname(__DIR__) . '/views/errors/maintenance.php';
                exit;
            }
        }
    }
}
