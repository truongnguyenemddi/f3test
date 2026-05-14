<?php
namespace App\Services;
use App\Utils\ClientHelper;

/**
 * Writes rows to system_log on the sub database (audit / diary).
 */
final class AuditLogService
{
    /**
     * @throws \Throwable on DB failure (caller may catch and ignore)
     */
    public static function writeDiary(
        string $content,
        ?int $userId = null,
        ?int $officeId = null,
        ?string $userName = null,
        ?string $fullName = null
    ): void {
        $app = \Base::instance();
        $subDb = DbService::get('subdb');
        if ($subDb === null) {
            return;
        }

        $userId = $userId ?? (int) $app->get('SESSION.userID');
        $officeId = $officeId ?? (int) $app->get('SESSION.unitID');
        $userName = $userName ?? (string) $app->get('SESSION.userName');
        $fullName = $fullName ?? (string) $app->get('SESSION.fullName');
        $ip = ClientHelper::clientIp();
        $browserName = ClientHelper::browserName((string) ($app->get('SERVER.HTTP_USER_AGENT') ?? ''));

        $subDb->exec(
            'INSERT INTO system_log (user_id, office_id, content, client_ip, browser_name, user_name, full_name) VALUES (?,?,?,?,?,?,?)',
            [1 => $userId, 2 => $officeId, 3 => $content, 4 => $ip, 5 => $browserName, 6 => $userName, 7 => $fullName]
        );
    }
}
