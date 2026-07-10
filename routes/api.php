<?php

/**
 * REST API Routes
 *
 * @package DzieKas
 */

declare(strict_types=1);

/** @var App\Core\Router $router */

$router->get('/api/movies', 'Api\\MovieController@index');
$router->get('/api/latest', 'Api\\MovieController@latest');
$router->get('/api/trending', 'Api\\MovieController@trending');
$router->get('/api/search', 'Api\\SearchController@index');
$router->get('/api/genres', 'Api\\GenreController@index');
$router->get('/api/categories', 'Api\\CategoryController@index');
$router->get('/api/movie/{slug}', 'Api\\MovieController@show');
$router->get('/api/series', 'Api\\SeriesController@index');
$router->get('/api/episodes/{contentId}', 'Api\\SeriesController@episodes');
$router->get('/api/settings', 'Api\\SettingController@index');
