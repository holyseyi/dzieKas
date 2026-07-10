<?php

/**
 * Application Bootstrap
 *
 * @package DzieKas
 */

declare(strict_types=1);

// Error reporting based on environment
$config = require dirname(__DIR__) . '/config/app.php';
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Set timezone
date_default_timezone_set($config['timezone']);

// Autoloader
require dirname(__DIR__) . '/app/core/Autoloader.php';
App\Core\Autoloader::register();

// Start session
App\Helpers\Session::start();

// Global view helper functions (e(), img(), content_url(), ...)
require dirname(__DIR__) . '/app/helpers/view_functions.php';

// Gzip compression
if (extension_loaded('zlib') && !ob_get_level()) {
    ob_start('ob_gzhandler');
}

// Browser cache headers for static assets
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (preg_match('/\.(css|js|jpg|jpeg|png|gif|webp|ico|woff2?)$/i', $uri)) {
    header('Cache-Control: public, max-age=31536000');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
}
