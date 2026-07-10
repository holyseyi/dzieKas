<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Slug;

class DirectorController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $directors = $db->fetchAll('SELECT * FROM directors ORDER BY name');
        $this->view('admin/directors/index', ['title' => 'Directors', 'directors' => $directors], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        $name = Security::sanitize((string) $this->input('name', ''));
        Database::getInstance()->insert('directors', ['name' => $name, 'slug' => Slug::generate($name)]);
        Session::flash('success', 'Director added.');
        $this->redirect('/admin/directors');
    }
}
