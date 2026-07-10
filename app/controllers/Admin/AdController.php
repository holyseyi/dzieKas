<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Advertisement;
use App\Helpers\Security;
use App\Helpers\Session;

class AdController extends Controller
{
    public function index(): void
    {
        $adModel = new Advertisement();
        $ads = $adModel->all('1=1', [], 'position, sort_order');
        $this->view('admin/ads/index', ['title' => 'Advertisements', 'ads' => $ads], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        $adModel = new Advertisement();
        $adModel->create([
            'name' => Security::sanitize((string) $this->input('name', '')),
            'position' => Security::sanitize((string) $this->input('position', 'sidebar')),
            'ad_code' => $this->input('ad_code', ''),
            'image_url' => Security::sanitize((string) $this->input('image_url', '')),
            'link_url' => Security::sanitize((string) $this->input('link_url', '')),
            'is_active' => $this->input('is_active') ? 1 : 0,
        ]);
        Session::flash('success', 'Advertisement added.');
        $this->redirect('/admin/ads');
    }

    public function delete(string $id): void
    {
        $this->validateCsrf();
        (new Advertisement())->delete((int) $id);
        Session::flash('success', 'Advertisement deleted.');
        $this->redirect('/admin/ads');
    }
}
