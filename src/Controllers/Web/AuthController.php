<?php
namespace App\Controllers\web;

use App\Services\AuthService;
use App\Services\AuditLogService;
use App\Services\JwtService;

class AuthController
{
    protected $app;

    public function __construct()
    {
        $this->app = \Base::instance();
    }

    /**
     * Handle login form submit (POST /login).
     * Form fields: user_name, password (see pages/login.php).
     */
    public function login(): void
    {
        $auth = AuthService::authenticate(
            (string) $this->app->get('POST.user_name'),
            (string) $this->app->get('POST.password')
        );

        if (!$auth['ok']) {
            $query = $auth['error'] === AuthService::ERR_DENIED ? 'denied' : 'failed';
            $this->app->reroute('/login?login=' . $query);
            return;
        }

        // Lưu user vào session
        AuthService::applyWebSession($auth);

        // Create JWT access token and store as httpOnly cookie
        try {
            $issued = JwtService::issueForUser($auth['user']);
            JwtService::setAccessCookie($issued['token'], $issued['exp']);
        } catch (\Throwable $e) {
            // Non-fatal: session login still works without JWT cookie
        }

        // Diary
        try {
            AuditLogService::writeDiary('Đã đăng nhập vào hệ thống');
        } catch (\Throwable $e) {
            // Do not block process on audit failure
        }

        // Online
        try {
            AuthService::setOnline($auth['user']->user_id, true);
        } catch (\Throwable $e) {
            // Non-fatal
        }

        // Regenerate session id
        if (session_status() === \PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $this->app->reroute('/');
    }

    public function logout(): void
    {
        // Diary
        try {
            AuditLogService::writeDiary('Đã thoát khỏi hệ thống');
        } catch (\Throwable $e) {
            // Do not block process on audit failure
        }

        // Offline
        try {
            $userId = $this->app->get('SESSION.userID');
            if ($userId !== null && $userId !== '') {
                AuthService::setOnline($userId, false);
            }
        } catch (\Throwable $e) {
            // Non-fatal
        }

        JwtService::clearAccessCookie();
        $this->app->clear('SESSION');
        $this->app->reroute('/login');
    }
}
