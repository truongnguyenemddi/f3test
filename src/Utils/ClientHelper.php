<?php
namespace App\Utils;

/**
 * HTTP request helpers (IP, User-Agent) shared by controllers and logging.
 */
final class ClientHelper
{
    /**
     * Client IP from REMOTE_ADDR only (trusted proxy should set this correctly).
     */
    public static function clientIp(): string
    {
        $app = \Base::instance();
        $direct = (string) ($app->get('SERVER.REMOTE_ADDR') ?? '');
        if ($direct !== '' && filter_var($direct, \FILTER_VALIDATE_IP)) {
            return $direct;
        }

        return '0.0.0.0';
    }

    public static function browserName(?string $userAgent): string
    {
        $ua = trim((string) $userAgent);
        if ($ua === '') {
            return 'Unknown';
        }
        if (preg_match('/Edg\//i', $ua)) {
            return 'Microsoft Edge';
        }
        if (preg_match('/OPR\//i', $ua) || preg_match('/Opera/i', $ua)) {
            return 'Opera';
        }
        if (preg_match('/Firefox/i', $ua)) {
            return 'Mozilla Firefox';
        }
        if (preg_match('/Chrome/i', $ua)) {
            return 'Google Chrome';
        }
        if (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) {
            return 'Apple Safari';
        }
        if (preg_match('/MSIE|Trident/i', $ua)) {
            return 'Internet Explorer';
        }

        return 'Unknown';
    }
}
