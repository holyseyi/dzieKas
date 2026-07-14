<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Slug;

class ActorController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $actors = $db->fetchAll('SELECT * FROM actors ORDER BY name');
        $this->view('admin/actors/index', ['title' => 'Actors', 'actors' => $actors], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        $name = Security::sanitize((string) $this->input('name', ''));
        Database::getInstance()->insert('actors', ['name' => $name, 'slug' => Slug::generate($name)]);
        Session::flash('success', 'Actor added.');
        $this->redirect('/admin/actors');
    }

    public function destroy(string $id): void
    {
        $this->validateCsrf();
        Database::getInstance()->delete('actors', 'id = ?', [$id]);
        Session::flash('success', 'Actor deleted.');
        $this->redirect('/admin/actors');
    }
}
