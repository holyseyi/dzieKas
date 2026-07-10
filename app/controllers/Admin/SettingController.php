<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Setting;
use App\Helpers\Session;

class SettingController extends Controller
{
    public function index(): void
    {
        $settingModel = new Setting();
        $this->view('admin/settings/index', ['title' => 'Site Settings', 'settings' => $settingModel->getAll()], 'layouts/admin');
    }

    public function update(): void
    {
        $this->validateCsrf();
        $settingModel = new Setting();
        $keys = ['site_name', 'site_tagline', 'site_description', 'contact_email', 'maintenance_mode', 'allow_registration', 'dark_mode_default'];
        foreach ($keys as $key) {
            if ($this->input($key) !== null) {
                $settingModel->set($key, $this->input($key));
            }
        }
        Session::flash('success', 'Settings updated.');
        $this->redirect('/admin/settings');
    }
}
