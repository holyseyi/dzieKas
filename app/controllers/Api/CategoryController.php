<?php

/**
 * API Category Controller
 *
 * @package DzieKas\Controllers\Api
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Database;

class CategoryController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $categories = $db->fetchAll(
            "SELECT c.*, COUNT(cnt.id) as content_count FROM categories c
             LEFT JOIN content cnt ON c.id = cnt.category_id AND cnt.status = 'published'
             WHERE c.is_active = 1 GROUP BY c.id ORDER BY c.sort_order"
        );

        $this->json(['success' => true, 'data' => $categories]);
    }
}
