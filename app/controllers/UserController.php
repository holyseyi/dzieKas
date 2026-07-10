<?php

/**
 * User Profile Controller
 *
 * @package DzieKas\Controllers
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use App\Models\Comment;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Image;

class UserController extends Controller
{
    public function profile(): void
    {
        $user = Session::get('user');
        $userModel = new User();
        $fullUser = $userModel->findWithRole((int) $user['id']);

        $this->view('public/user/profile', [
            'title' => 'My Profile',
            'profile' => $fullUser,
        ]);
    }

    public function updateProfile(): void
    {
        $this->validateCsrf();
        $user = Session::get('user');
        $userModel = new User();

        $data = [
            'display_name' => Security::sanitize((string) $this->input('display_name', '')),
            'bio' => Security::sanitize((string) $this->input('bio', '')),
        ];

        if (!empty($_FILES['avatar']['name'])) {
            $avatar = Image::upload($_FILES['avatar'], 'avatars', $this->config['image_sizes']['avatar']);
            if ($avatar) {
                $data['avatar'] = $avatar;
            }
        }

        $userModel->update((int) $user['id'], $data);

        $updated = $userModel->findWithRole((int) $user['id']);
        unset($updated['password']);
        Session::set('user', $updated);

        Session::flash('success', 'Profile updated successfully.');
        $this->redirect('/profile');
    }

    public function bookmarks(): void
    {
        $user = Session::get('user');
        $userModel = new User();
        $bookmarks = $userModel->getBookmarks((int) $user['id']);

        $this->view('public/user/bookmarks', [
            'title' => 'My Bookmarks',
            'bookmarks' => $bookmarks,
        ]);
    }

    public function history(): void
    {
        $user = Session::get('user');
        $userModel = new User();
        $history = $userModel->getWatchHistory((int) $user['id']);

        $this->view('public/user/history', [
            'title' => 'Watch History',
            'history' => $history,
        ]);
    }

    public function toggleBookmark(string $contentId): void
    {
        $this->validateCsrf();
        $user = Session::get('user');
        $db = Database::getInstance();

        $existing = $db->fetchOne(
            'SELECT id FROM bookmarks WHERE user_id = ? AND content_id = ?',
            [$user['id'], $contentId]
        );

        if ($existing) {
            $db->delete('bookmarks', 'id = ?', [$existing['id']]);
            $this->json(['bookmarked' => false]);
        } else {
            $db->insert('bookmarks', ['user_id' => $user['id'], 'content_id' => $contentId]);
            $this->json(['bookmarked' => true]);
        }
    }

    public function rate(string $contentId): void
    {
        $this->validateCsrf();
        $user = Session::get('user');
        $rating = (int) $this->input('rating', 0);
        $review = Security::sanitize((string) $this->input('review', ''));

        if ($rating < 1 || $rating > 10) {
            $this->json(['error' => 'Invalid rating'], 400);
        }

        $db = Database::getInstance();
        $existing = $db->fetchOne(
            'SELECT id FROM ratings WHERE user_id = ? AND content_id = ?',
            [$user['id'], $contentId]
        );

        if ($existing) {
            $db->update('ratings', ['rating' => $rating, 'review' => $review], 'id = ?', [$existing['id']]);
        } else {
            $db->insert('ratings', [
                'user_id' => $user['id'],
                'content_id' => $contentId,
                'rating' => $rating,
                'review' => $review,
            ]);
        }

        // Update average rating
        $avg = $db->fetchOne(
            'SELECT AVG(rating) as avg, COUNT(*) as count FROM ratings WHERE content_id = ?',
            [$contentId]
        );
        $db->update('content', [
            'user_rating_avg' => round((float) ($avg['avg'] ?? 0), 1),
            'user_rating_count' => (int) ($avg['count'] ?? 0),
        ], 'id = ?', [$contentId]);

        $this->json(['success' => true, 'avg' => round((float) ($avg['avg'] ?? 0), 1)]);
    }

    public function comment(string $contentId): void
    {
        $this->validateCsrf();
        $user = Session::get('user');
        $body = Security::sanitize((string) $this->input('body', ''));

        if (strlen($body) < 3) {
            Session::flash('error', 'Comment too short.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $commentModel = new Comment();
        $commentModel->create([
            'user_id' => $user['id'],
            'content_id' => $contentId,
            'body' => $body,
            'parent_id' => $this->input('parent_id'),
        ]);

        Session::flash('success', 'Comment posted!');
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }

    public function like(string $contentId): void
    {
        $db = Database::getInstance();
        $user = Session::get('user');

        $db->insert('content_likes', [
            'user_id' => $user['id'] ?? null,
            'content_id' => $contentId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $db->query('UPDATE content SET like_count = like_count + 1 WHERE id = ?', [$contentId]);

        $content = $db->fetchOne('SELECT like_count FROM content WHERE id = ?', [$contentId]);
        $this->json(['likes' => $content['like_count'] ?? 0]);
    }

    public function reportLink(): void
    {
        $this->validateCsrf();
        $db = Database::getInstance();
        $user = Session::get('user');

        $db->insert('link_reports', [
            'user_id' => $user['id'] ?? null,
            'download_id' => $this->input('download_id'),
            'streaming_link_id' => $this->input('streaming_link_id'),
            'reason' => Security::sanitize((string) $this->input('reason', '')),
        ]);

        $this->json(['success' => true, 'message' => 'Report submitted. Thank you!']);
    }
}
