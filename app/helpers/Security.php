<?php

/**
 * Security & Sanitization Helper
 *
 * @package DzieKas\Helpers
 */

declare(strict_types=1);

namespace App\Helpers;

class Security
{
    /**
     * Escape HTML output to prevent XSS.
     */
    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sanitize string input.
     */
    public static function sanitize(string $input): string
    {
        return trim(strip_tags($input));
    }

    /**
     * Hash password using bcrypt.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password against hash.
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate secure random token.
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validate email format.
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Rate limit check for login attempts.
     */
    public static function checkRateLimit(string $key, int $maxAttempts, int $lockoutMinutes): bool
    {
        $attempts = Session::get("rate_limit_{$key}", ['count' => 0, 'locked_until' => 0]);

        if ($attempts['locked_until'] > time()) {
            return false;
        }

        if ($attempts['count'] >= $maxAttempts) {
            Session::set("rate_limit_{$key}", [
                'count' => $attempts['count'],
                'locked_until' => time() + ($lockoutMinutes * 60),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Record a failed login attempt.
     */
    public static function recordFailedAttempt(string $key): void
    {
        $attempts = Session::get("rate_limit_{$key}", ['count' => 0, 'locked_until' => 0]);
        $attempts['count']++;
        Session::set("rate_limit_{$key}", $attempts);
    }

    /**
     * Clear rate limit on successful login.
     */
    public static function clearRateLimit(string $key): void
    {
        Session::remove("rate_limit_{$key}");
    }
}
