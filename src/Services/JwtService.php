<?php
namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * JWT issue/verify aligned with legacy payload: user_id, office_id (unit_id).
 */
final class JwtService
{
    public const COOKIE_NAME = 'jwt_access_token';

    /**
     * @return array{token: string, exp: int, expires_in: int, payload: array<string, mixed>}
     */
    public static function issueForUser(User $user): array
    {
        $now = time();
        $ttl = (int) ($_ENV['JWT_TTL'] ?? 604800);
        if ($ttl <= 0) {
            $ttl = 604800;
        }
        $exp = $now + $ttl;

        $payload = [
            'iss' => (string) ($_ENV['JWT_ISS'] ?? 'booking-switchboard'),
            'aud' => 'access-token',
            'iat' => $now,
            'exp' => $exp,
            'office_id' => (int) ($user->unit_id ?? 0),
            'user_id' => (int) ($user->user_id ?? 0),
        ];

        return [
            'token' => self::encode($payload),
            'exp' => $exp,
            'expires_in' => $ttl,
            'payload' => $payload,
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function encode(array $payload): string
    {
        $privatePem = self::privateKeyPem();
        if ($privatePem === '') {
            throw new \RuntimeException('JWT_PEM_PRIVATE_KEY is not configured');
        }

        return JWT::encode($payload, $privatePem, 'RS256');
    }

    /**
     * @return array{ok: true, claims: array<string, mixed>}|array{ok: false, error: string, status: int}
     */
    public static function verify(?string $token = null): array
    {
        $token = $token ?? self::resolveTokenFromRequest();
        if ($token === null || $token === '') {
            return ['ok' => false, 'error' => 'missing_bearer_token', 'status' => 401];
        }

        JWT::$leeway = (int) ($_ENV['JWT_LEEWAY'] ?? 0);

        try {
            $decoded = JWT::decode($token, self::verificationKey());
        } catch (\RuntimeException $e) {
            return ['ok' => false, 'error' => 'jwt_not_configured', 'status' => 500];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => 'invalid_token', 'status' => 401];
        }

        $claims = json_decode(json_encode($decoded), true);
        if (!is_array($claims)) {
            return ['ok' => false, 'error' => 'invalid_token', 'status' => 401];
        }

        $iss = (string) ($_ENV['JWT_ISS'] ?? '');
        if ($iss !== '' && ($claims['iss'] ?? null) !== $iss) {
            return ['ok' => false, 'error' => 'invalid_token', 'status' => 401];
        }

        return ['ok' => true, 'claims' => $claims];
    }

    public static function resolveTokenFromRequest(): ?string
    {
        $app = \Base::instance();
        $auth = (string) ($app->get('HEADERS.Authorization') ?? '');
        if (preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
            return trim($m[1]);
        }

        $cookie = (string) ($app->get('COOKIE.' . self::COOKIE_NAME) ?? '');
        if ($cookie !== '') {
            return $cookie;
        }

        return null;
    }

    /**
     * @param array<string, mixed> $claims
     * @return int|string|null
     */
    public static function userIdFromClaims(array $claims)
    {
        if (isset($claims['user_id'])) {
            return $claims['user_id'];
        }
        if (isset($claims['sub']) && $claims['sub'] !== '') {
            return $claims['sub'];
        }
        $user = $claims['user'] ?? null;
        if (is_array($user) && isset($user['user_id'])) {
            return $user['user_id'];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $claims
     */
    public static function officeIdFromClaims(array $claims): int
    {
        if (isset($claims['office_id'])) {
            return (int) $claims['office_id'];
        }
        $user = $claims['user'] ?? null;
        if (is_array($user)) {
            return (int) ($user['unit_id'] ?? $user['office_id'] ?? 0);
        }

        return 0;
    }

    public static function setAccessCookie(string $token, int $exp): void
    {
        $secure = self::isHttps();
        setcookie(self::COOKIE_NAME, $token, [
            'expires' => $exp,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    public static function clearAccessCookie(): void
    {
        $secure = self::isHttps();
        setcookie(self::COOKIE_NAME, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    private static function verificationKey(): Key
    {
        $publicPem = self::publicKeyFromPrivate();
        if ($publicPem === '') {
            throw new \RuntimeException('JWT_PEM_PRIVATE_KEY is not configured or invalid');
        }

        return new Key($publicPem, 'RS256');
    }

    private static function privateKeyPem(): string
    {
        return self::normalizePem((string) ($_ENV['JWT_PEM_PRIVATE_KEY'] ?? ''));
    }

    /** Verify in-app: derive public key from JWT_PEM_PRIVATE_KEY (not JWT_PEM_PUBLIC_KEY). */
    private static function publicKeyFromPrivate(): string
    {
        $privatePem = self::privateKeyPem();
        if ($privatePem === '') {
            return '';
        }

        return self::publicKeyFromPrivatePem($privatePem);
    }

    private static function publicKeyFromPrivatePem(string $privatePem): string
    {
        $key = openssl_pkey_get_private($privatePem);
        if ($key === false) {
            return '';
        }
        $details = openssl_pkey_get_details($key);
        if (!is_array($details) || empty($details['key'])) {
            return '';
        }

        return (string) $details['key'];
    }

    private static function normalizePem(string $pem): string
    {
        $pem = trim(str_replace('\\n', "\n", $pem));
        return $pem;
    }

    private static function isHttps(): bool
    {
        $app = \Base::instance();
        $https = (string) ($app->get('SERVER.HTTPS') ?? '');
        $port = (int) ($app->get('SERVER.SERVER_PORT') ?? 0);

        return ($https !== '' && $https !== 'off') || $port === 443;
    }
}
