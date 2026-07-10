<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Slug;

class GenreController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $genres = $db->fetchAll('SELECT g.*, COUNT(cg.content_id) as count FROM genres g LEFT JOIN content_genres cg ON g.id = cg.genre_id GROUP BY g.id ORDER BY g.name');
        $this->view('admin/genres/index', ['title' => 'Genres', 'genres' => $genres], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        $name = Security::sanitize((string) $this->input('name', ''));
        Database::getInstance()->insert('genres', ['name' => $name, 'slug' => Slug::generate($name)]);
        Session::flash('success', 'Genre added.');
        $this->redirect('/admin/genres');
    }
}
