<?php
namespace App\Controllers\Api;

/**
 * GET /api/v1/auth/health — requires Authorization: Bearer (access_token).
 * Uses the same JWT_SECRET / iss / aud rules as BaseApiController.
 */
class AuthHealthController extends BaseApiController
{
    public function health(): void
    {
        $user = $this->jwtClaims['user'] ?? null;
        $exp = isset($this->jwtClaims['exp']) && is_numeric($this->jwtClaims['exp'])
            ? (int) $this->jwtClaims['exp']
            : null;

        $this->json([
            'ok' => true,
            'authenticated' => true,
            'sub' => $this->jwtClaims['sub'] ?? null,
            'exp' => $exp,
            'user' => is_array($user) ? $user : null,
        ]);
    }
}
