<?php
namespace App\Controllers\Api;

use App\Services\AuthService;
use App\Services\AuditLogService;
use App\Services\JwtService;

class AuthController
{
    protected \Base $app;

    public function __construct()
    {
        $this->app = \Base::instance();
    }

    public function login(): void
    {
        $identity = trim((string) ($this->app->get('POST.identity') ?: $this->app->get('POST.user_name')));
        $password = (string) $this->app->get('POST.password');

        $auth = AuthService::authenticate($identity, $password);

        if (!$auth['ok']) {
            $this->json($this->loginErrorPayload($auth['error']), $this->loginErrorStatus($auth['error']));
            return;
        }

        $user = $auth['user'];

        // Create JWT access token
        try {
            $issued = JwtService::issueForUser($user);
        } catch (\RuntimeException $e) {
            $this->json(['ok' => false, 'error' => 'jwt_not_configured'], 500);
            return;
        }

        // Store as httpOnly cookie
        JwtService::setAccessCookie($issued['token'], $issued['exp']);

        // Diary
        try {
            AuditLogService::writeDiary(
                'Đã đăng nhập vào hệ thống (API)',
                (int) $user->user_id,
                (int) $user->unit_id,
                (string) $user->user_name,
                (string) $user->full_name
            );
        } catch (\Throwable $e) {
            // Non-fatal
        }

        // Online
        try {
            AuthService::setOnline($user->user_id, true);
        } catch (\Throwable $e) {
            // Non-fatal
        }

        $this->json([
            'ok' => true,
            'access_token' => $issued['token'],
            'token_type' => 'Bearer',
            'expires_in' => $issued['expires_in'],
            'user_id' => $issued['payload']['user_id'],
            'office_id' => $issued['payload']['office_id'],
        ]);
    }

    public function logout(): void
    {
        $result = JwtService::verify();
        if (!$result['ok']) {
            $this->json(['ok' => false, 'error' => $result['error']], $result['status']);
            return;
        }

        $claims = $result['claims'];
        $userId = JwtService::userIdFromClaims($claims);
        $officeId = JwtService::officeIdFromClaims($claims);

        // Diary
        try {
            AuditLogService::writeDiary(
                'Đã thoát khỏi hệ thống (API)',
                (int) $userId,
                $officeId,
                '',
                ''
            );
        } catch (\Throwable $e) {
            // Non-fatal
        }

        // Offline
        try {
            if ($userId !== null && $userId !== '') {
                AuthService::setOnline($userId, false);
            }
        } catch (\Throwable $e) {
            // Non-fatal
        }

        JwtService::clearAccessCookie();
        $this->json(['ok' => true]);
    }

    private function loginErrorPayload(string $error): array
    {
        $map = [
            AuthService::ERR_EMPTY => 'identity_and_password_required',
            AuthService::ERR_DB => 'db_not_configured',
            AuthService::ERR_DENIED => 'access_denied',
        ];

        return [
            'ok' => false,
            'error' => $map[$error] ?? 'invalid_credentials',
        ];
    }

    private function loginErrorStatus(string $error): int
    {
        if ($error === AuthService::ERR_EMPTY) {
            return 422;
        }
        if ($error === AuthService::ERR_DB) {
            return 500;
        }
        if ($error === AuthService::ERR_DENIED) {
            return 403;
        }

        return 401;
    }

    private function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
