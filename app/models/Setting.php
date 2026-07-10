<?php

/**
 * Site Settings Model
 *
 * @package DzieKas\Models
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected string $table = 'site_settings';

    /**
     * Get setting value by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $result = $this->db->fetchOne('SELECT value, type FROM site_settings WHERE key = ?', [$key]);

        if (!$result) {
            return $default;
        }

        return match ($result['type']) {
            'boolean' => $result['value'] === '1',
            'integer' => (int) $result['value'],
            'json' => json_decode($result['value'] ?? '{}', true),
            default => $result['value'],
        };
    }

    /**
     * Set setting value.
     */
    public function set(string $key, mixed $value): void
    {
        $existing = $this->db->fetchOne('SELECT id FROM site_settings WHERE key = ?', [$key]);

        $stringValue = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

        if ($existing) {
            $this->db->update('site_settings', ['value' => $stringValue, 'updated_at' => date('Y-m-d H:i:s')], 'key = ?', [$key]);
        } else {
            $this->db->insert('site_settings', ['key' => $key, 'value' => $stringValue]);
        }
    }

    /**
     * Get all settings as key-value array.
     *
     * @return array<string, mixed>
     */
    public function getAll(): array
    {
        $rows = $this->db->fetchAll('SELECT key, value, type FROM site_settings');
        $settings = [];

        foreach ($rows as $row) {
            $settings[$row['key']] = match ($row['type']) {
                'boolean' => $row['value'] === '1',
                'integer' => (int) $row['value'],
                'json' => json_decode($row['value'] ?? '{}', true),
                default => $row['value'],
            };
        }

        return $settings;
    }
}
