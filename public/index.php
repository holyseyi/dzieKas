<?php

/**
 * DzieKas Entertainment Portal - Front Controller
 *
 * @package DzieKas
 */

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Router;

// Determine base path for subdirectory installs
$scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = $scriptName === '/' ? '' : $scriptName;

$router = new Router($basePath);

// Load route definitions
require dirname(__DIR__) . '/routes/web.php';
require dirname(__DIR__) . '/routes/api.php';
require dirname(__DIR__) . '/routes/admin.php';

// Dispatch request
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Handle PUT/DELETE via _method field
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$router->dispatch($method, $uri);
