<?php
namespace App\Controllers\Api;

use App\Services\JwtService;

/**
 * GET /api/v1/auth/health — Bearer or jwt_access_token cookie.
 */
class AuthHealthController extends BaseApiController
{
    public function health(): void
    {
        $exp = isset($this->jwtClaims['exp']) && is_numeric($this->jwtClaims['exp'])
            ? (int) $this->jwtClaims['exp']
            : null;

        $this->json([
            'ok' => true,
            'authenticated' => true,
            'user_id' => JwtService::userIdFromClaims($this->jwtClaims),
            'office_id' => JwtService::officeIdFromClaims($this->jwtClaims),
            'exp' => $exp,
        ]);
    }
}
