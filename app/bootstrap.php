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

// Auto-initialize SQLite database if missing
$dbConfig = require dirname(__DIR__) . '/config/database.php';
$dbPath = $dbConfig['database'];
$dbDir = dirname($dbPath);

if (!is_dir($dbDir) || !is_writable($dbDir)) {
    @mkdir($dbDir, 0777, true);
    @chmod($dbDir, 0777);
}

if (!file_exists($dbPath)) {
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');
        
        $schema = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
        if ($schema) {
            $pdo->exec($schema);
        }
        
        $seed = file_get_contents(dirname(__DIR__) . '/database/seed.sql');
        if ($seed) {
            $pdo->exec($seed);
        }
        
        $pdo->exec("INSERT OR IGNORE INTO users (role_id, username, email, password, display_name, is_active, email_verified_at) VALUES (1, 'admin', 'admin@dziekas.com', '" . password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]) . "', 'Admin', 1, datetime('now'))");
    } catch (\Throwable $e) {
        // Database initialization failed
    }
}

@chmod($dbPath, 0666);

// Ensure storage directories exist
$storageDirs = [
    dirname(__DIR__) . '/storage/uploads/posters',
    dirname(__DIR__) . '/storage/uploads/banners',
    dirname(__DIR__) . '/storage/uploads/screenshots',
    dirname(__DIR__) . '/storage/uploads/trailers',
    dirname(__DIR__) . '/storage/uploads/subtitles',
    dirname(__DIR__) . '/storage/uploads/avatars',
    dirname(__DIR__) . '/storage/uploads/videos',
    dirname(__DIR__) . '/storage/cache',
    dirname(__DIR__) . '/storage/logs',
    dirname(__DIR__) . '/storage/backups',
];
foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
        @chmod($dir, 0777);
    }
}

// Ensure public/storage symlink exists
$publicStorage = dirname(__DIR__) . '/public/storage';
$storageUploads = dirname(__DIR__) . '/storage/uploads';
if (!file_exists($publicStorage)) {
    @symlink($storageUploads, $publicStorage);
}

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
