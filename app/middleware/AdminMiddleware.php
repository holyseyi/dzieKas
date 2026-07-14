<?php

/**
 * Admin Role Middleware
 *
 * @package DzieKas\Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Session;

class AdminMiddleware
{
    public function handle(): void
    {
        $user = Session::get('user');

        if (!$user || !in_array($user['role_slug'] ?? ($user['role'] ?? ''), ['super_admin', 'admin', 'editor', 'Super Admin', 'Admin', 'Editor'], true)) {
            http_response_code(403);
            echo 'Access denied.';
            exit;
        }
    }
}
