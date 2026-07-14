<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Slug;

class CountryController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $countries = $db->fetchAll('SELECT * FROM countries ORDER BY name');
        $this->view('admin/countries/index', ['title' => 'Countries', 'countries' => $countries], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        $name = Security::sanitize((string) $this->input('name', ''));
        Database::getInstance()->insert('countries', [
            'name' => $name,
            'slug' => Slug::generate($name),
            'code' => Security::sanitize((string) $this->input('code', '')),
        ]);
        Session::flash('success', 'Country added.');
        $this->redirect('/admin/countries');
    }

    public function destroy(string $id): void
    {
        $this->validateCsrf();
        Database::getInstance()->delete('countries', 'id = ?', [$id]);
        Session::flash('success', 'Country deleted.');
        $this->redirect('/admin/countries');
    }
}
