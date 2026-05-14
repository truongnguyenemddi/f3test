<?php
namespace App\Services;

use App\Models\User;
use App\Utils\ClientHelper;

/**
 * Shared login validation (web session + API JWT).
 */
final class AuthService
{
    public const ERR_EMPTY = 'empty_credentials';
    public const ERR_DB = 'db_not_configured';
    public const ERR_INVALID = 'invalid_credentials';
    public const ERR_DENIED = 'denied';

    /**
     * @return array{
     *   ok: bool,
     *   error: string,
     *   user: ?User,
     *   permission: string,
     *   modules: string,
     *   office: ?array{list_role: string, name: string}
     * }
     */
    public static function authenticate(string $identity, string $password): array
    {
        $identity = trim($identity);
        if ($identity === '' || $password === '') {
            return self::fail(self::ERR_EMPTY);
        }

        $db = DbService::get('maindb');
        if ($db === null) {
            return self::fail(self::ERR_DB);
        }

        $user = (new User($db))->findByIdentity($identity);
        if (!$user || !$user->verifyPassword($password)) {
            return self::fail(self::ERR_INVALID);
        }

        if ((int) ($user->locked ?? 0) !== 0) {
            return self::fail(self::ERR_INVALID);
        }

        $unitId = (int) ($user->unit_id ?? 0);
        if ($unitId <= 0) {
            return self::fail(self::ERR_DENIED);
        }

        $permission = self::computePermission($db, (string) $user->user_id, $unitId);
        if ($permission === '') {
            return self::fail(self::ERR_DENIED);
        }

        if (!self::passesMacCheck($db, $user)) {
            return self::fail(self::ERR_INVALID);
        }

        return [
            'ok' => true,
            'error' => '',
            'user' => $user,
            'permission' => $permission,
            'modules' => self::computeModules($db, (string) $user->user_id, $unitId),
            'office' => self::fetchOfficeByUnit($db, $unitId),
        ];
    }

    /**
     * @param int|string $userId
     */
    public static function setOnline($userId, bool $online): void
    {
        $db = DbService::get('maindb');
        if ($db === null) {
            return;
        }
        $db->exec(
            'UPDATE user SET is_online = ? WHERE user_id = ?',
            [1 => $online ? 1 : 0, 2 => $userId]
        );
    }

    /**
     * Populate F3 session after successful authenticate().
     */
    public static function applyWebSession(array $auth): void
    {
        if (!$auth['ok'] || !($auth['user'] instanceof User)) {
            return;
        }

        $user = $auth['user'];
        $app = \Base::instance();

        $app->set('SESSION.userID', $user->user_id);
        $app->set('SESSION.officeID', $user->office_id);
        $app->set('SESSION.unitID', $user->unit_id);
        $app->set('SESSION.fullName', $user->full_name);
        $app->set('SESSION.avatar', $user->avatar);
        $app->set('SESSION.userName', $user->user_name);
        $app->set('SESSION.limitDate', $user->limit_date);
        $app->set('SESSION.permission', $auth['permission']);
        $app->set('SESSION.listModule', $auth['modules']);
        $app->set('SESSION.csrf_token', bin2hex(random_bytes(16)));

        $office = $auth['office'];
        if ($office !== null) {
            $app->set('SESSION.listRole', $office['list_role']);
            $app->set('SESSION.officeName', $office['name']);
        } else {
            $app->clear('SESSION.listRole');
            $app->clear('SESSION.officeName');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function userPayloadForToken(User $user, array $auth): array
    {
        return [
            'user_id' => $user->user_id ?? null,
            'user_name' => $user->user_name ?? null,
            'full_name' => $user->full_name ?? null,
            'email' => $user->email ?? null,
            'role' => $user->role ?? null,
            'office_id' => $user->office_id ?? null,
            'unit_id' => $user->unit_id ?? null,
            'permission' => $auth['permission'],
            'list_module' => $auth['modules'],
        ];
    }

    private static function passesMacCheck(\DB\SQL $db, User $user): bool
    {
        if (empty($user->mac_address)) {
            return true;
        }

        $ip = ClientHelper::clientIp();
        $needle = '%|' . $ip . '|%';
        $rows = $db->exec(
            'SELECT COUNT(*) AS c FROM user WHERE user_id = ? AND user_name = ? AND locked = 0 AND mac_address LIKE ?',
            [1 => $user->user_id, 2 => $user->user_name, 3 => $needle]
        );
        $count = (int) (($rows[0]['c'] ?? $rows[0]['C'] ?? 0));

        return $count > 0;
    }

    /**
     * @return array{list_role: string, name: string}|null
     */
    private static function fetchOfficeByUnit(\DB\SQL $db, int $unitId): ?array
    {
        $rows = $db->exec(
            'SELECT list_role, name FROM office WHERE office_id = ? AND type = 1 LIMIT 1',
            [1 => $unitId]
        );
        if (!is_array($rows) || $rows === []) {
            return null;
        }
        $row = $rows[0];

        return [
            'list_role' => (string) ($row['list_role'] ?? ''),
            'name' => (string) ($row['name'] ?? ''),
        ];
    }

    private static function computePermission(\DB\SQL $db, string $userId, int $officeId): string
    {
        $rows = $db->exec(
            'SELECT list_role FROM office WHERE office_id = ? LIMIT 1',
            [1 => $officeId]
        );
        $chuoiQuyen = (string) (($rows[0]['list_role'] ?? $rows[0]['LIST_ROLE'] ?? '') ?? '');

        $stringRole = '';
        $needle = '%|' . $userId . '|%';
        $rgRows = $db->exec(
            'SELECT list_role FROM role_group WHERE list_user LIKE ? AND office_id = ?',
            [1 => $needle, 2 => $officeId]
        );
        if (!is_array($rgRows)) {
            return '';
        }
        foreach ($rgRows as $row) {
            $listRole = (string) ($row['list_role'] ?? $row['LIST_ROLE'] ?? '');
            foreach (explode('|', $listRole) as $id) {
                $id = trim($id);
                if ($id === '') {
                    continue;
                }
                if (self::roleAllowedInOffice($id, $chuoiQuyen)) {
                    $stringRole .= '|' . $id . '|';
                }
            }
        }

        return str_replace('||', '|', $stringRole);
    }

    private static function computeModules(\DB\SQL $db, string $userId, int $officeId): string
    {
        $stringModule = '';
        $needle = '%|' . $userId . '|%';
        $rows = $db->exec(
            'SELECT list_module FROM role_group WHERE list_user LIKE ? AND office_id = ?',
            [1 => $needle, 2 => $officeId]
        );
        if (!is_array($rows)) {
            return '';
        }
        foreach ($rows as $row) {
            $stringModule .= (string) ($row['list_module'] ?? $row['LIST_MODULE'] ?? '');
        }

        return str_replace('||', '|', $stringModule);
    }

    private static function roleAllowedInOffice(string $roleId, string $officeRolesPipe): bool
    {
        return strpos($officeRolesPipe, '|' . $roleId . '|') !== false;
    }

    /**
     * @return array{ok: bool, error: string, user: ?User, permission: string, modules: string, office: ?array}
     */
    private static function fail(string $error): array
    {
        return [
            'ok' => false,
            'error' => $error,
            'user' => null,
            'permission' => '',
            'modules' => '',
            'office' => null,
        ];
    }
}
