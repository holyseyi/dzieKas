<?php

/**
 * Static Pages Controller
 *
 * @package DzieKas\Controllers
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Advertisement;
use App\Helpers\Security;
use App\Helpers\Session;

class PageController extends Controller
{
    public function contact(): void
    {
        $this->view('public/pages/contact', ['title' => 'Contact Us']);
    }

    public function submitContact(): void
    {
        $this->validateCsrf();
        Session::flash('success', 'Thank you for your message. We will get back to you soon.');
        $this->redirect('/contact');
    }

    public function privacy(): void
    {
        $this->view('public/pages/privacy', ['title' => 'Privacy Policy']);
    }

    public function terms(): void
    {
        $this->view('public/pages/terms', ['title' => 'Terms of Service']);
    }

    public function dmca(): void
    {
        $this->view('public/pages/dmca', ['title' => 'DMCA Notice']);
    }

    public function newsletter(): void
    {
        $this->validateCsrf();
        $email = Security::sanitize((string) $this->input('email', ''));

        if (!Security::isValidEmail($email)) {
            Session::flash('error', 'Please enter a valid email.');
            $this->redirect('/');
        }

        $db = Database::getInstance();
        try {
            $db->insert('newsletter_subscribers', ['email' => $email]);
            Session::flash('success', 'Successfully subscribed to newsletter!');
        } catch (\Exception) {
            Session::flash('error', 'Email already subscribed.');
        }

        $this->redirect('/');
    }

    public function adClick(string $id): void
    {
        $adModel = new Advertisement();
        $ad = $adModel->find((int) $id);

        if ($ad) {
            $adModel->trackClick((int) $id);
            if ($ad['link_url']) {
                $this->redirect($ad['link_url']);
            }
        }

        $this->redirect('/');
    }
}
