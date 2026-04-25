<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    protected $app;

    public function __construct() {
        $this->app = \Base::instance();
    }

    /**
     * Handle login form submit
     */
    public function login() {
        $identity = trim((string) $this->app->get('POST.identity'));
        $password = (string) $this->app->get('POST.password');

        if ($identity === '' || $password === '') {
            $this->app->set('SESSION.flash_error', 'Please enter username/email and password.');
            $this->app->reroute('/login');
            return;
        }

        /** @var \DB\SQL $db */
        $db = $this->app->get('DB');
        $user = (new User($db))->findByIdentity($identity);

        if (!$user || !$user->verifyPassword($password)) {
            $this->app->set('SESSION.flash_error', 'Invalid username/email or password.');
            $this->app->reroute('/login');
            return;
        }

        $this->app->set('SESSION.user', [
            'user_id' => $user->user_id ?? null,
            'user_name' => $user->user_name ?? null,
            'full_name' => $user->full_name ?? null,
            'email' => $user->email ?? null,
            'role' => $user->role ?? null,
            'office_id' => $user->office_id ?? null,
        ]);

        $this->app->reroute('/');
    }

    /**
     * Handle logout
     */
    public function logout() {
        $this->app->clear('SESSION.user');
        $this->app->set('SESSION.flash_success', 'You have been logged out.');
        $this->app->reroute('/login');
    }
}
