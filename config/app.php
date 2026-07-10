<?php

/**
 * Application Configuration
 *
 * @package DzieKas
 */

declare(strict_types=1);

return [
    'name' => 'DzieKas Entertainment',
    'tagline' => 'Your Ultimate Movie & Entertainment Portal',
    'url' => getenv('APP_URL') ?: 'http://localhost/dzieKas/public',
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'debug' => (bool) (getenv('APP_DEBUG') ?: true),
    'maintenance_mode' => false,
    'version' => '1.0.0',
    'per_page' => 24,
    'admin_per_page' => 20,
    'upload_max_size' => 10 * 1024 * 1024, // 10MB
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    'allowed_video_types' => ['video/mp4', 'video/webm'],
    'session_lifetime' => 7200,
    'login_max_attempts' => 5,
    'login_lockout_minutes' => 15,
    'cache_ttl' => 3600,
    'trending_days' => 7,
    'image_sizes' => [
        'poster' => ['width' => 300, 'height' => 450],
        'banner' => ['width' => 1280, 'height' => 720],
        'thumbnail' => ['width' => 200, 'height' => 300],
        'avatar' => ['width' => 150, 'height' => 150],
    ],
];
