<?php

/**
 * Base Controller
 *
 * @package DzieKas\Core
 */

declare(strict_types=1);

namespace App\Core;

use App\Helpers\Session;
use App\Helpers\Csrf;
use App\Helpers\Lang;

class Controller
{
    protected array $config;

    public function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config/app.php';
    }

    /**
     * Render a view with layout.
     *
     * @param array<string, mixed> $data
     */
    protected function view(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        $data['config'] = $this->config;
        $data['csrf_token'] = Csrf::token();
        $data['user'] = Session::get('user');
        $data['flash'] = Session::getFlash();
        $data['lang'] = Lang::getInstance();
        $data['dark_mode'] = Session::get('dark_mode', false);

        extract($data);

        ob_start();
        $viewPath = dirname(__DIR__) . '/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View {$view} not found.");
        }
        require $viewPath;
        $content = ob_get_clean();

        if ($layout) {
            $layoutPath = dirname(__DIR__) . '/views/' . str_replace('.', '/', $layout) . '.php';
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Return JSON response.
     *
     * @param array<string, mixed> $data
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect to URL.
     */
    protected function redirect(string $url, int $status = 302): void
    {
        header("Location: {$url}", true, $status);
        exit;
    }

    /**
     * Get POST input.
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET query parameter.
     */
    protected function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Validate CSRF token on POST requests.
     */
    protected function validateCsrf(): void
    {
        $token = $this->input('_csrf_token', '');
        if (!Csrf::validate((string) $token)) {
            http_response_code(403);
            $this->view('errors/403', ['title' => 'Forbidden']);
            exit;
        }
    }
}
