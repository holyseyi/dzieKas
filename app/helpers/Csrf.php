<?php

/**
 * CSRF Protection Helper
 *
 * @package DzieKas\Helpers
 */

declare(strict_types=1);

namespace App\Helpers;

class Csrf
{
    private const TOKEN_KEY = '_csrf_token';

    public static function token(): string
    {
        if (!Session::has(self::TOKEN_KEY)) {
            Session::set(self::TOKEN_KEY, bin2hex(random_bytes(32)));
        }

        return Session::get(self::TOKEN_KEY);
    }

    public static function validate(string $token): bool
    {
        $stored = Session::get(self::TOKEN_KEY, '');

        return hash_equals((string) $stored, $token);
    }

    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
