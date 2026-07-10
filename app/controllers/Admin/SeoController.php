<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

class SeoController extends Controller
{
    public function index(): void
    {
        $this->view('admin/seo/index', [
            'title' => 'SEO Management',
            'sitemap_url' => $this->config['url'] . '/sitemap.xml',
            'robots_url' => $this->config['url'] . '/robots.txt',
            'feed_url' => $this->config['url'] . '/feed.xml',
        ], 'layouts/admin');
    }
}
