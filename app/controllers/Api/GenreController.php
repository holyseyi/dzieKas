<?php

/**
 * API Genre Controller
 *
 * @package DzieKas\Controllers\Api
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index(): void
    {
        $genreModel = new Genre();
        $genres = $genreModel->getAllWithCount();

        $this->json(['success' => true, 'data' => $genres]);
    }
}
