<?php
namespace App\Controllers\Api;

use App\Services\JwtService;

abstract class BaseApiController
{
    protected \Base $app;
    /** @var array<string, mixed> */
    protected $jwtClaims = [];

    public function __construct()
    {
        $this->app = \Base::instance();
    }

    public function beforeroute(): void
    {
        $result = JwtService::verify();
        if (!$result['ok']) {
            $this->json(['ok' => false, 'error' => $result['error']], $result['status']);
            exit;
        }

        $this->jwtClaims = $result['claims'];
        $this->app->set('API.user', $this->jwtClaims);
    }

    protected function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
