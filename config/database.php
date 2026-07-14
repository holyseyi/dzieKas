<?php

/**
 * Database Configuration
 *
 * @package DzieKas
 */

declare(strict_types=1);

return [
    'driver' => 'sqlite',
    'database' => '/tmp/dzieKas.sqlite',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
