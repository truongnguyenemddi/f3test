<?php
namespace App\Controllers;

use App\Models\User;
use App\Services\Auth\AuthzService;
use App\Utils\Diary;

class AuthController {
    protected $app;

    public function __construct() {
        $this->app = \Base::instance();
    }

    private function getClientIp(): string {
        $server = (array)($this->app->get('SERVER') ?? []);
        $clientIp = trim((string)($server['HTTP_CLIENT_IP'] ?? ''));
        if ($clientIp !== '') return $clientIp;

        $xff = trim((string)($server['HTTP_X_FORWARDED_FOR'] ?? ''));
        if ($xff !== '') {
            $parts = array_map('trim', explode(',', $xff));
            if (count($parts) > 0 && $parts[0] !== '') return $parts[0];
        }

        return trim((string)($server['REMOTE_ADDR'] ?? ''));
    }

    private function regenerateSessionIdKeepingData(): void {
        // Don't close/restart session; let F3/PHP session management handle it.
        if (function_exists('session_status') && session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
    }

    /**
     * Handle login form submit
     */
    public function login() {
        $identity = trim((string) $this->app->get('POST.identity'));
        $password = (string) $this->app->get('POST.password');

        if ($identity === '' || $password === '') {
            $this->app->set('SESSION.flash_error', 'Vui lòng nhập tài khoản và mật khẩu.');
            $this->app->reroute('/login');
            return;
        }

        /** @var \DB\SQL $db */
        $db = $this->app->get('DB');

        // Match old behavior: login by username (primary) + check locked=0
        $user = (new User($db))->findByIdentity($identity);
        if (!$user || !$user->verifyPassword($password) || (int)($user->locked ?? 0) !== 0) {
            $this->app->reroute('/login?login=failed');
            return;
        }

        $userId = (string)($user->user_id ?? '');
        $officeId = (string)($user->office_id ?? '');
        $unitId = (string)($user->unit_id ?? '');

        // Permission / module strings
        $permission = AuthzService::computePermission($db, $userId, $unitId);
        if ($permission === '|' || trim($permission) === '') {
            $this->app->reroute('/login?login=denied');
            return;
        }

        $clientIp = $this->getClientIp();
        $macAddress = (string)($user->mac_address ?? '');
        if (trim($macAddress) !== '' && strpos($macAddress, '|' . $clientIp . '|') === false) {
            $this->app->reroute('/login?login=failed');
            return;
        }

        $listModule = AuthzService::computeModules($db, $userId, $unitId);
        $officeInfo = AuthzService::officeType1Info($db, $unitId);
        $listRole = $officeInfo['listRole'];
        $officeName = $officeInfo['officeName'];

        // CSRF token
        $csrf = bin2hex(random_bytes(16));

        // Set legacy session keys (for compatibility with old project)
        $this->app->set('SESSION.userID', $userId);
        $this->app->set('SESSION.officeID', $officeId);
        $this->app->set('SESSION.unitID', $unitId);
        $this->app->set('SESSION.fullName', (string)($user->full_name ?? ''));
        $this->app->set('SESSION.avatar', (string)($user->avatar ?? ''));
        $this->app->set('SESSION.userName', (string)($user->user_name ?? ''));
        $this->app->set('SESSION.limitDate', (string)($user->limit_date ?? ''));
        $this->app->set('SESSION.permission', $permission);
        $this->app->set('SESSION.listModule', $listModule);
        $this->app->set('SESSION.csrf_token', $csrf);
        $this->app->set('SESSION.listRole', $listRole);
        $this->app->set('SESSION.officeName', $officeName);

        // Keep new session shape too (used by current middleware)
        $this->app->set('SESSION.user', [
            'user_id' => $userId,
            'user_name' => (string)($user->user_name ?? ''),
            'full_name' => (string)($user->full_name ?? ''),
            'email' => (string)($user->email ?? ''),
            'role' => (string)($user->role ?? ''),
            'office_id' => $officeId,
            'unit_id' => $unitId,
            'permission' => $permission,
            'list_module' => $listModule,
        ]);

        // Online flag
        $db->exec('UPDATE user SET is_online = 1 WHERE user_id = ?', [$userId]);

        // Diary log (replacement for GhiNhatKy)
        Diary::log('Đã đăng nhập vào hệ thống', [
            'user_id' => $userId,
            'user_name' => (string)($user->user_name ?? ''),
            'ip' => $clientIp,
        ]);

        // Regenerate session id like old project
        $this->regenerateSessionIdKeepingData();

        $this->app->reroute('/');
    }

    /**
     * Handle logout
     */
    public function logout() {
        // best-effort online flag
        try {
            /** @var \DB\SQL $db */
            $db = $this->app->get('DB');
            $userId = (string)$this->app->get('SESSION.userID');
            if ($userId !== '') $db->exec('UPDATE user SET is_online = 0 WHERE user_id = ?', [$userId]);
        } catch (\Throwable $e) {
            // ignore
        }

        $this->app->clear('SESSION');
        $this->app->set('SESSION.flash_success', 'Đã đăng xuất.');
        $this->app->reroute('/login');
    }
}
