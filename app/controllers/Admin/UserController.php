<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User as UserModel;
use App\Helpers\Session;

class UserController extends Controller
{
    public function index(): void
    {
        $userModel = new UserModel();
        $page = max(1, (int) $this->query('page', 1));
        $result = $userModel->paginate($page, $this->config['admin_per_page']);
        $this->view('admin/users/index', ['title' => 'Users', 'users' => $result['data'], 'page' => $result['page'], 'totalPages' => $result['total_pages']], 'layouts/admin');
    }

    public function toggleActive(string $id): void
    {
        $this->validateCsrf();
        $userModel = new UserModel();
        $user = $userModel->find((int) $id);
        if ($user) {
            $userModel->update((int) $id, ['is_active' => $user['is_active'] ? 0 : 1]);
        }
        Session::flash('success', 'User status updated.');
        $this->redirect('/admin/users');
    }
}
