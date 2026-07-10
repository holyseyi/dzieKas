<?php

/**
 * Localization Helper
 *
 * @package DzieKas\Helpers
 */

declare(strict_types=1);

namespace App\Helpers;

class Lang
{
    private static ?Lang $instance = null;
    private array $translations = [];
    private string $locale;

    private function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $this->locale = Session::get('locale', $config['locale']);
        $this->loadTranslations();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function loadTranslations(): void
    {
        $file = dirname(__DIR__, 2) . "/config/lang/{$this->locale}.php";
        if (file_exists($file)) {
            $this->translations = require $file;
        }
    }

    public function get(string $key, ?string $default = null): string
    {
        return $this->translations[$key] ?? $default ?? $key;
    }

    public function __(string $key, ?string $default = null): string
    {
        return $this->get($key, $default);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
