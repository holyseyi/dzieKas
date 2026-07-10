<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Session;

class CommentController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $comments = $db->fetchAll(
            "SELECT cm.*, u.username, c.title as content_title FROM comments cm
             JOIN users u ON cm.user_id = u.id JOIN content c ON cm.content_id = c.id
             ORDER BY cm.created_at DESC LIMIT 100"
        );
        $this->view('admin/comments/index', ['title' => 'Comments', 'comments' => $comments], 'layouts/admin');
    }

    public function approve(string $id): void
    {
        $this->validateCsrf();
        Database::getInstance()->update('comments', ['is_approved' => 1], 'id = ?', [$id]);
        Session::flash('success', 'Comment approved.');
        $this->redirect('/admin/comments');
    }

    public function delete(string $id): void
    {
        $this->validateCsrf();
        Database::getInstance()->delete('comments', 'id = ?', [$id]);
        Session::flash('success', 'Comment deleted.');
        $this->redirect('/admin/comments');
    }
}
