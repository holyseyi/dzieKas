<?php

/**
 * DzieKas Installation Script
 *
 * Run: php database/install.php
 * Creates database, schema, seed data, and admin user.
 *
 * @package DzieKas
 */

declare(strict_types=1);

echo "=== DzieKas Entertainment Portal Installer ===\n\n";

$basePath = dirname(__DIR__);
$dbPath = $basePath . '/database/dzieKas.sqlite';
$schemaPath = $basePath . '/database/schema.sql';
$seedPath = $basePath . '/database/seed.sql';

// Create database directory if needed
if (!is_dir($basePath . '/database')) {
    mkdir($basePath . '/database', 0755, true);
}

// Create storage directories
$dirs = [
    'storage/uploads/posters',
    'storage/uploads/banners',
    'storage/uploads/screenshots',
    'storage/uploads/trailers',
    'storage/uploads/subtitles',
    'storage/uploads/avatars',
    'storage/cache',
    'storage/logs',
    'storage/backups',
];

foreach ($dirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
        echo "Created directory: {$dir}\n";
    }
}

// Remove existing database for fresh install
if (file_exists($dbPath)) {
    echo "Existing database found. Recreating...\n";
    unlink($dbPath);
}

// Create SQLite database
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');

echo "Database created: {$dbPath}\n";

// Run schema
$schema = file_get_contents($schemaPath);
$pdo->exec($schema);
echo "Schema applied successfully.\n";

// Run seed data
$seed = file_get_contents($seedPath);
$pdo->exec($seed);
echo "Seed data loaded successfully.\n";

// Create admin user
echo "\n--- Create Admin User ---\n";
echo "Username [admin]: ";
$username = trim(fgets(STDIN)) ?: 'admin';

echo "Email [admin@dziekas.com]: ";
$email = trim(fgets(STDIN)) ?: 'admin@dziekas.com';

echo "Password [admin123]: ";
$password = trim(fgets(STDIN)) ?: 'admin123';

$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $pdo->prepare('INSERT INTO users (role_id, username, email, password, display_name, is_active, email_verified_at) VALUES (1, ?, ?, ?, ?, 1, datetime("now"))');
$stmt->execute([$username, $email, $hashedPassword, ucfirst($username)]);

echo "\nAdmin user created successfully!\n";
echo "Username: {$username}\n";
echo "Email: {$email}\n";

// Create symlink for uploads in public
$publicStorage = $basePath . '/public/storage';
$storageUploads = $basePath . '/storage/uploads';
if (!file_exists($publicStorage)) {
    if (PHP_OS_FAMILY !== 'Windows') {
        symlink($storageUploads, $publicStorage);
        echo "\nSymlink created: public/storage -> storage/uploads\n";
    } else {
        echo "\nNote: On Windows, manually copy or symlink storage/uploads to public/storage\n";
    }
}

echo "\n=== Installation Complete! ===\n";
echo "Access the site at: http://localhost/dzieKas/public\n";
echo "Admin panel at: http://localhost/dzieKas/public/admin\n";
echo "\nIMPORTANT: Change the default admin password after first login!\n";
