<?php

/**
 * URL Router with clean URL support
 *
 * @package DzieKas\Core
 */

declare(strict_types=1);

namespace App\Core;

class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: string, middleware: array<int, string>}> */
    private array $routes = [];

    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Register a GET route.
     *
     * @param array<int, string> $middleware
     */
    public function get(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $pattern, $handler, $middleware);
    }

    /**
     * Register a POST route.
     *
     * @param array<int, string> $middleware
     */
    public function post(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $pattern, $handler, $middleware);
    }

    /**
     * Register a PUT route.
     *
     * @param array<int, string> $middleware
     */
    public function put(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $pattern, $handler, $middleware);
    }

    /**
     * Register a DELETE route.
     *
     * @param array<int, string> $middleware
     */
    public function delete(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $pattern, $handler, $middleware);
    }

    /**
     * @param array<int, string> $middleware
     */
    private function addRoute(string $method, string $pattern, string $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(string $method, string $uri): void
    {
        $uri = $this->normalizeUri($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute($route['pattern'], $uri);
            if ($params === null) {
                continue;
            }

            $this->runMiddleware($route['middleware']);
            $this->invokeHandler($route['handler'], $params);

            return;
        }

        http_response_code(404);
        $controller = new Controller();
        $controller->view('errors/404', ['title' => 'Page Not Found']);
    }

    private function normalizeUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        $uri = rawurldecode($uri);

        if ($this->basePath && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath)) ?: '/';
        }

        return rtrim($uri, '/') ?: '/';
    }

    /**
     * @return array<string, string>|null
     */
    private function matchRoute(string $pattern, string $uri): ?array
    {
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /**
     * @param array<int, string> $middleware
     */
    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $mw) {
            $class = "App\\Middleware\\{$mw}";
            if (class_exists($class)) {
                (new $class())->handle();
            }
        }
    }

    /**
     * @param array<string, string> $params
     */
    private function invokeHandler(string $handler, array $params): void
    {
        [$controllerName, $method] = explode('@', $handler);
        $class = "App\\Controllers\\{$controllerName}";

        if (!class_exists($class)) {
            throw new \RuntimeException("Controller {$class} not found.");
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method {$method} not found in {$class}.");
        }

        call_user_func_array([$controller, $method], $params);
    }
}
