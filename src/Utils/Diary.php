<?php
namespace App\Utils;

use App\Models\SystemLog;

class Diary {
    private static function browserName(string $agent): string {
        $a = strtolower($agent);
        if (strpos($a, 'edg/') !== false || strpos($a, 'edge/') !== false) return 'Edge';
        if (strpos($a, 'opr/') !== false || strpos($a, 'opera') !== false) return 'Opera';
        if (strpos($a, 'chrome/') !== false && strpos($a, 'chromium') === false) return 'Chrome';
        if (strpos($a, 'firefox/') !== false) return 'Firefox';
        if (strpos($a, 'safari/') !== false && strpos($a, 'chrome/') === false) return 'Safari';
        if (strpos($a, 'msie') !== false || strpos($a, 'trident/') !== false) return 'IE';
        return 'Other';
    }

    public static function log(string $content, array $context = []): void {
        // Prefer DB logging (like legacy project). If the table doesn't exist, fall back to file.
        try {
            $app = \Base::instance();
            /** @var \DB\SQL|null $dbSub */
            $dbSub = $app->get('DB_SUB');
            if ($dbSub) {
                $userId = (string)$app->get('SESSION.userID');
                $unitId = (string)$app->get('SESSION.unitID');
                $userName = (string)$app->get('SESSION.userName');
                $fullName = (string)$app->get('SESSION.fullName');
                $ip = (string)$app->get('IP');
                $browser = self::browserName((string)$app->get('AGENT'));

                (new SystemLog($dbSub))->insertLoginLog([
                    'user_id' => $userId,
                    'office_id' => $unitId,
                    'content' => $content,
                    'client_ip' => $ip,
                    'browser_name' => $browser,
                    'user_name' => $userName,
                    'full_name' => $fullName,
                ]);
                return;
            }
        } catch (\Throwable $e) {
            // fall through
        }

        $line = '[' . date('Y-m-d H:i:s') . '] ' . $content;
        if (!empty($context)) $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $line .= PHP_EOL;

        $path = dirname(__DIR__, 2) . '/tmp/diary.log';
        @file_put_contents($path, $line, FILE_APPEND);
    }
}

