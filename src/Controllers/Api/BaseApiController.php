<?php
namespace App\Controllers\Api;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

abstract class BaseApiController
{
    protected \Base $app;
    protected array $jwtClaims = [];

    public function __construct()
    {
        $this->app = \Base::instance();
    }

    public function beforeroute(): void
    {
        $secret = (string) $_ENV['JWT_SECRET'] ?? '';
        if ($secret === '') {
            $this->json(['ok' => false, 'error' => 'jwt_not_configured'], 500);
            exit;
        }

        $auth = (string) ($this->app->get('HEADERS.Authorization') ?? '');
        if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
            $this->json(['ok' => false, 'error' => 'missing_bearer_token'], 401);
            exit;
        }

        $iss = (string) $_ENV['JWT_ISS'] ?? '';
        $aud = (string) $_ENV['JWT_AUD'] ?? '';

        JWT::$leeway = (int) $_ENV['JWT_LEEWAY'] ?? 0;
        try {
            $decoded = JWT::decode(trim($m[1]), new Key($secret, 'HS256'));
        } catch (\Throwable $e) {
            $this->json(['ok' => false, 'error' => 'invalid_token'], 401);
            exit;
        }

        $claims = json_decode(json_encode($decoded), true);
        if (!is_array($claims)) {
            $this->json(['ok' => false, 'error' => 'invalid_token'], 401);
            exit;
        }
        if ($iss !== '' && ($claims['iss'] ?? null) !== $iss) {
            $this->json(['ok' => false, 'error' => 'invalid_token'], 401);
            exit;
        }
        if ($aud !== '' && ($claims['aud'] ?? null) !== $aud) {
            $this->json(['ok' => false, 'error' => 'invalid_token'], 401);
            exit;
        }

        $this->jwtClaims = $claims;
        $this->app->set('API.user', $this->jwtClaims);
    }

    protected function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

