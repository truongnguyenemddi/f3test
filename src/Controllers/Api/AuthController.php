<?php
namespace App\Controllers\Api;

use App\Models\User;
use App\Services\DbService;
use Firebase\JWT\JWT;

class AuthController
{
    protected \Base $app;

    public function __construct()
    {
        $this->app = \Base::instance();
    }

    public function login(): void
    {
        $identity = trim((string) $this->app->get('POST.identity'));
        $password = (string) $this->app->get('POST.password');

        if ($identity === '' || $password === '') {
            $this->json(['ok' => false, 'error' => 'identity_and_password_required'], 422);
            return;
        }

        /** @var \DB\SQL|null $db */
        $db = DbService::get('maindb');
        if (!$db) {
            $this->json(['ok' => false, 'error' => 'db_not_configured'], 500);
            return;
        }

        $user = (new User($db))->findByIdentity($identity);
        if (!$user || !$user->verifyPassword($password)) {
            $this->json(['ok' => false, 'error' => 'invalid_credentials'], 401);
            return;
        }

        $secret = (string) $_ENV['JWT_SECRET'] ?? '';
        if ($secret === '') {
            $this->json(['ok' => false, 'error' => 'jwt_not_configured'], 500);
            return;
        }

        $now = time();
        $ttl = (int) $_ENV['JWT_TTL'] ?? 3600;
        if ($ttl <= 0) {
            $ttl = 3600;
        }

        $iss = (string) $_ENV['JWT_ISS'] ?? '';
        $aud = (string) $_ENV['JWT_AUD'] ?? '';

        $claims = [
            'iss' => $iss !== '' ? $iss : null,
            'aud' => $aud !== '' ? $aud : null,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttl,
            'sub' => (string) ($user->user_id ?? ''),
            'user' => [
                'user_id' => $user->user_id ?? null,
                'user_name' => $user->user_name ?? null,
                'full_name' => $user->full_name ?? null,
                'email' => $user->email ?? null,
                'role' => $user->role ?? null,
                'office_id' => $user->office_id ?? null,
            ],
        ];
        $claims = array_filter($claims, static fn($v) => $v !== null);
        $token = JWT::encode($claims, $secret, 'HS256');

        $this->json([
            'ok' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttl,
        ]);
    }

    public function logout(): void
    {
        // Stateless JWT: logout is handled client-side (discard token).
        $this->json(['ok' => true]);
    }

    private function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

