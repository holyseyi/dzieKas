<?php

/**
 * PSR-4 Style Autoloader
 *
 * @package DzieKas\Core
 */

declare(strict_types=1);

namespace App\Core;

class Autoloader
{
    /**
     * Register the autoloader.
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    /**
     * Load a class file based on namespace.
     */
    public static function load(string $class): void
    {
        $prefix = 'App\\';
        $baseDir = dirname(__DIR__) . '/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
}
