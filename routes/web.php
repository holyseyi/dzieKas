<?php

/**
 * Public Web Routes
 *
 * @package DzieKas
 */

declare(strict_types=1);

/** @var App\Core\Router $router */

// Home
$router->get('/', 'HomeController@index');

// Content
$router->get('/movie/{slug}', 'ContentController@show');
$router->get('/series/{slug}', 'ContentController@series');
$router->get('/anime/{slug}', 'ContentController@series');
$router->get('/k-drama/{slug}', 'ContentController@series');

// Browse
$router->get('/movies', 'BrowseController@movies');
$router->get('/tv-series', 'BrowseController@tvSeries');
$router->get('/anime', 'BrowseController@anime');
$router->get('/k-dramas', 'BrowseController@kDramas');
$router->get('/documentaries', 'BrowseController@documentaries');
$router->get('/trending', 'BrowseController@trending');
$router->get('/latest', 'BrowseController@latest');
$router->get('/genre/{slug}', 'BrowseController@genre');
$router->get('/country/{slug}', 'BrowseController@country');
$router->get('/year/{year}', 'BrowseController@year');

// Search
$router->get('/search', 'SearchController@index');

// Auth
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotForm');
$router->post('/forgot-password', 'AuthController@forgot');
$router->get('/reset-password/{token}', 'AuthController@resetForm');
$router->post('/reset-password/{token}', 'AuthController@reset');

// User (authenticated)
$router->get('/profile', 'UserController@profile', ['AuthMiddleware']);
$router->post('/profile', 'UserController@updateProfile', ['AuthMiddleware']);
$router->get('/bookmarks', 'UserController@bookmarks', ['AuthMiddleware']);
$router->get('/history', 'UserController@history', ['AuthMiddleware']);
$router->post('/bookmark/{contentId}', 'UserController@toggleBookmark', ['AuthMiddleware']);
$router->post('/rate/{contentId}', 'UserController@rate', ['AuthMiddleware']);
$router->post('/comment/{contentId}', 'UserController@comment', ['AuthMiddleware']);
$router->post('/like/{contentId}', 'UserController@like');
$router->post('/report-link', 'UserController@reportLink');

// Static pages
$router->get('/contact', 'PageController@contact');
$router->post('/contact', 'PageController@submitContact');
$router->get('/privacy-policy', 'PageController@privacy');
$router->get('/terms-of-service', 'PageController@terms');
$router->get('/dmca', 'PageController@dmca');
$router->post('/newsletter', 'PageController@newsletter');

// SEO
$router->get('/sitemap.xml', 'SeoController@sitemap');
$router->get('/robots.txt', 'SeoController@robots');
$router->get('/feed.xml', 'SeoController@rss');

// Theme toggle
$router->post('/toggle-dark-mode', 'HomeController@toggleDarkMode');

// Ad tracking
$router->get('/ad/click/{id}', 'PageController@adClick');
