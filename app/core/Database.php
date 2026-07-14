<?php

/**
 * PDO Database Singleton
 *
 * @package DzieKas\Core
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;
use RuntimeException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private array $queryCache = [];

    private function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $dbPath = $config['database'];

        if (!file_exists($dbPath)) {
            throw new RuntimeException('Database file not found. Please run the installation script.');
        }

        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0777, true);
        }

        if (!is_writable($dbDir)) {
            @chmod($dbDir, 0777);
        }

        if (file_exists($dbPath) && !is_writable($dbPath)) {
            @chmod($dbPath, 0666);
        }

        try {
            $this->pdo = new PDO('sqlite:' . $dbPath, null, null, $config['options']);
            $this->pdo->exec('PRAGMA foreign_keys = ON');
            $this->pdo->exec('PRAGMA journal_mode = WAL');
        } catch (\PDOException $e) {
            if (!is_writable($dbDir) || (file_exists($dbPath) && !is_writable($dbPath))) {
                @chmod($dbDir, 0777);
                if (file_exists($dbPath)) {
                    @chmod($dbPath, 0666);
                }
                $this->pdo = new PDO('sqlite:' . $dbPath, null, null, $config['options']);
                $this->pdo->exec('PRAGMA foreign_keys = ON');
                $this->pdo->exec('PRAGMA journal_mode = WAL');
            } else {
                throw new RuntimeException('Unable to open database file: ' . $dbPath . ' - ' . $e->getMessage(), 0, $e);
            }
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a prepared query.
     *
     * @param array<int|string, mixed> $params
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * Fetch all rows with optional query caching.
     *
     * @param array<int|string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public function fetchAll(string $sql, array $params = [], bool $cache = false): array
    {
        $cacheKey = $cache ? md5($sql . serialize($params)) : null;

        if ($cacheKey && isset($this->queryCache[$cacheKey])) {
            return $this->queryCache[$cacheKey];
        }

        $result = $this->query($sql, $params)->fetchAll();

        if ($cacheKey) {
            $this->queryCache[$cacheKey] = $result;
        }

        return $result;
    }

    /**
     * Fetch a single row.
     *
     * @param array<int|string, mixed> $params
     * @return array<string, mixed>|false
     */
    public function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Insert a record and return last insert ID.
     *
     * @param array<string, mixed> $data
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update records.
     *
     * @param array<string, mixed> $data
     * @param array<int|string, mixed> $whereParams
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(', ', array_map(fn ($col) => "{$col} = ?", array_keys($data)));
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";

        $stmt = $this->query($sql, array_merge(array_values($data), $whereParams));

        return $stmt->rowCount();
    }

    /**
     * Delete records.
     *
     * @param array<int|string, mixed> $params
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);

        return $stmt->rowCount();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function clearCache(): void
    {
        $this->queryCache = [];
    }
}
