<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Session;

class FeaturedController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $featured = $db->fetchAll(
            "SELECT fc.*, c.title, c.poster FROM featured_content fc JOIN content c ON fc.content_id = c.id ORDER BY fc.section, fc.sort_order"
        );
        $content = $db->fetchAll("SELECT id, title FROM content WHERE status = 'published' ORDER BY title");
        $this->view('admin/featured/index', ['title' => 'Featured Content', 'featured' => $featured, 'content' => $content], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        Database::getInstance()->insert('featured_content', [
            'content_id' => (int) $this->input('content_id'),
            'section' => $this->input('section', 'featured'),
            'sort_order' => (int) $this->input('sort_order', 0),
        ]);
        Session::flash('success', 'Featured content added.');
        $this->redirect('/admin/featured');
    }
}
