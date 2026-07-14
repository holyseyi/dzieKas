<?php

/**
 * Admin Dashboard Routes
 *
 * @package DzieKas
 */

declare(strict_types=1);

/** @var App\Core\Router $router */

$adminMw = ['AuthMiddleware', 'AdminMiddleware'];

// Dashboard
$router->get('/admin', 'Admin\\DashboardController@index', $adminMw);
$router->get('/admin/dashboard', 'Admin\\DashboardController@index', $adminMw);

// Content Management
$router->get('/admin/content', 'Admin\\ContentController@index', $adminMw);
$router->get('/admin/content/create', 'Admin\\ContentController@create', $adminMw);
$router->post('/admin/content/store', 'Admin\\ContentController@store', $adminMw);
$router->get('/admin/content/edit/{id}', 'Admin\\ContentController@edit', $adminMw);
$router->post('/admin/content/update/{id}', 'Admin\\ContentController@update', $adminMw);
$router->post('/admin/content/delete/{id}', 'Admin\\ContentController@delete', $adminMw);

// Episodes
$router->get('/admin/episodes/{contentId}', 'Admin\\EpisodeController@index', $adminMw);
$router->post('/admin/episodes/store', 'Admin\\EpisodeController@store', $adminMw);
$router->post('/admin/episodes/delete/{id}', 'Admin\\EpisodeController@delete', $adminMw);

// Taxonomy
$router->get('/admin/genres', 'Admin\\GenreController@index', $adminMw);
$router->post('/admin/genres/store', 'Admin\\GenreController@store', $adminMw);
$router->post('/admin/genres/delete/{id}', 'Admin\\GenreController@destroy', $adminMw);
$router->get('/admin/countries', 'Admin\\CountryController@index', $adminMw);
$router->post('/admin/countries/store', 'Admin\\CountryController@store', $adminMw);
$router->post('/admin/countries/delete/{id}', 'Admin\\CountryController@destroy', $adminMw);
$router->get('/admin/actors', 'Admin\\ActorController@index', $adminMw);
$router->post('/admin/actors/store', 'Admin\\ActorController@store', $adminMw);
$router->post('/admin/actors/delete/{id}', 'Admin\\ActorController@destroy', $adminMw);
$router->get('/admin/directors', 'Admin\\DirectorController@index', $adminMw);
$router->post('/admin/directors/store', 'Admin\\DirectorController@store', $adminMw);
$router->post('/admin/directors/delete/{id}', 'Admin\\DirectorController@destroy', $adminMw);

// Users
$router->get('/admin/users', 'Admin\\UserController@index', $adminMw);
$router->post('/admin/users/toggle/{id}', 'Admin\\UserController@toggleActive', $adminMw);

// Comments
$router->get('/admin/comments', 'Admin\\CommentController@index', $adminMw);
$router->post('/admin/comments/approve/{id}', 'Admin\\CommentController@approve', $adminMw);
$router->post('/admin/comments/delete/{id}', 'Admin\\CommentController@delete', $adminMw);

// Ads
$router->get('/admin/ads', 'Admin\\AdController@index', $adminMw);
$router->post('/admin/ads/store', 'Admin\\AdController@store', $adminMw);
$router->post('/admin/ads/delete/{id}', 'Admin\\AdController@delete', $adminMw);

// Featured
$router->get('/admin/featured', 'Admin\\FeaturedController@index', $adminMw);
$router->post('/admin/featured/store', 'Admin\\FeaturedController@store', $adminMw);
$router->post('/admin/featured/delete/{id}', 'Admin\\FeaturedController@destroy', $adminMw);

// Settings
$router->get('/admin/settings', 'Admin\\SettingController@index', $adminMw);
$router->post('/admin/settings', 'Admin\\SettingController@update', $adminMw);

// SEO
$router->get('/admin/seo', 'Admin\\SeoController@index', $adminMw);

// Reports
$router->get('/admin/reports', 'Admin\\ReportController@index', $adminMw);

// Media Library
$router->get('/admin/media', 'Admin\\MediaController@index', $adminMw);
$router->post('/admin/media/folder', 'Admin\\MediaController@createFolder', $adminMw);
$router->post('/admin/media/upload', 'Admin\\MediaController@upload', $adminMw);
$router->post('/admin/media/folder/delete/{id}', 'Admin\\MediaController@deleteFolder', $adminMw);
$router->post('/admin/media/file/delete/{id}', 'Admin\\MediaController@deleteFile', $adminMw);

// Logs
$router->get('/admin/logs', 'Admin\\LogController@index', $adminMw);

// Backup
$router->get('/admin/backup', 'Admin\\BackupController@index', $adminMw);
$router->post('/admin/backup/create', 'Admin\\BackupController@create', $adminMw);
$router->post('/admin/backup/restore', 'Admin\\BackupController@restore', $adminMw);
