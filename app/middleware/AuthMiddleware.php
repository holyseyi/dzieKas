<?php

/**
 * Authentication Middleware
 *
 * @package DzieKas\Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Session::get('user')) {
            header('Location: /login');
            exit;
        }
    }
}
