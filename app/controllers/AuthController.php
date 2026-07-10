<?php

/**
 * Authentication Controller
 *
 * @package DzieKas\Controllers
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Validator;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (Session::get('user')) {
            $this->redirect('/profile');
        }

        $this->view('public/auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        $this->validateCsrf();

        $config = $this->config;
        $identifier = Security::sanitize((string) $this->input('identifier', ''));
        $password = (string) $this->input('password', '');

        if (!Security::checkRateLimit('login', $config['login_max_attempts'], $config['login_lockout_minutes'])) {
            Session::flash('error', 'Too many login attempts. Please try again later.');
            $this->redirect('/login');
        }

        $userModel = new User();
        $user = str_contains($identifier, '@')
            ? $userModel->findByEmail($identifier)
            : $userModel->findByUsername($identifier);

        if (!$user || !Security::verifyPassword($password, $user['password'])) {
            Security::recordFailedAttempt('login');
            Session::flash('error', 'Invalid credentials.');
            $this->redirect('/login');
        }

        if (!$user['is_active']) {
            Session::flash('error', 'Your account has been deactivated.');
            $this->redirect('/login');
        }

        Security::clearRateLimit('login');
        Session::regenerate();

        unset($user['password'], $user['reset_token']);
        Session::set('user', $user);

        $userModel->update((int) $user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $this->redirect('/profile');
    }

    public function registerForm(): void
    {
        $this->view('public/auth/register', ['title' => 'Register']);
    }

    public function register(): void
    {
        $this->validateCsrf();

        $validator = new Validator();
        $data = [
            'username' => Security::sanitize((string) $this->input('username', '')),
            'email' => Security::sanitize((string) $this->input('email', '')),
            'password' => (string) $this->input('password', ''),
            'password_confirm' => (string) $this->input('password_confirm', ''),
        ];

        if (!$validator->validate($data, [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ])) {
            Session::flash('error', $validator->firstError());
            $this->redirect('/register');
        }

        if ($data['password'] !== $data['password_confirm']) {
            Session::flash('error', 'Passwords do not match.');
            $this->redirect('/register');
        }

        $userModel = new User();

        if ($userModel->findByEmail($data['email'])) {
            Session::flash('error', 'Email already registered.');
            $this->redirect('/register');
        }

        if ($userModel->findByUsername($data['username'])) {
            Session::flash('error', 'Username already taken.');
            $this->redirect('/register');
        }

        $userModel->create([
            'role_id' => 4,
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Security::hashPassword($data['password']),
            'display_name' => $data['username'],
        ]);

        Session::flash('success', 'Registration successful! Please login.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/');
    }

    public function forgotForm(): void
    {
        $this->view('public/auth/forgot', ['title' => 'Forgot Password']);
    }

    public function forgot(): void
    {
        $this->validateCsrf();

        $email = Security::sanitize((string) $this->input('email', ''));
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user) {
            $token = Security::generateToken();
            $userModel->setResetToken((int) $user['id'], $token);
            // In production, send email with reset link
        }

        Session::flash('success', 'If that email exists, a reset link has been sent.');
        $this->redirect('/login');
    }

    public function resetForm(string $token): void
    {
        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            Session::flash('error', 'Invalid or expired reset token.');
            $this->redirect('/login');
        }

        $this->view('public/auth/reset', ['title' => 'Reset Password', 'token' => $token]);
    }

    public function reset(string $token): void
    {
        $this->validateCsrf();

        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            Session::flash('error', 'Invalid or expired reset token.');
            $this->redirect('/login');
        }

        $password = (string) $this->input('password', '');
        if (strlen($password) < 6) {
            Session::flash('error', 'Password must be at least 6 characters.');
            $this->redirect('/reset-password/' . $token);
        }

        $userModel->update((int) $user['id'], [
            'password' => Security::hashPassword($password),
            'reset_token' => null,
            'reset_token_expires' => null,
        ]);

        Session::flash('success', 'Password reset successfully. Please login.');
        $this->redirect('/login');
    }
}
