<?php

/**
 * Base Model with common CRUD operations
 *
 * @package DzieKas\Core
 */

declare(strict_types=1);

namespace App\Core;

abstract class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find record by ID.
     *
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$id]);

        return $result ?: null;
    }

    /**
     * Find record by slug.
     *
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$slug]);

        return $result ?: null;
    }

    /**
     * Get all records with optional conditions.
     *
     * @param array<int|string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public function all(string $where = '1=1', array $params = [], string $orderBy = 'id DESC', ?int $limit = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy}";
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Paginate records.
     *
     * @param array<int|string, mixed> $params
     * @return array{data: array<int, array<string, mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public function paginate(int $page, int $perPage, string $where = '1=1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$where}";
        $total = (int) ($this->db->fetchOne($countSql, $params)['total'] ?? 0);

        $sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->db->fetchAll($sql, $params);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Create a new record.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        $data['updated_at'] = $data['updated_at'] ?? date('Y-m-d H:i:s');

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update a record.
     *
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->update($this->table, $data, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Delete a record.
     */
    public function delete(int $id): int
    {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Count records.
     *
     * @param array<int|string, mixed> $params
     */
    public function count(string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$where}";
        return (int) ($this->db->fetchOne($sql, $params)['total'] ?? 0);
    }
}
