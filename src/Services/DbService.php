<?php
namespace App\Services;

final class DbService
{
    /**
     * Connect (and lazily initialize) a DB connection stored in the F3 hive.
     *
     * Expected hive values:
     * - \DB\SQL instance (already initialized), or
     * - callable that returns \DB\SQL (lazy initializer)
     */
    public static function connect(string $key): \DB\SQL
    {
        $app = \Base::instance();
        $val = $app->get($key);

        if ($val instanceof \DB\SQL)
            return $val;

        if (is_callable($val)) {
            $db = $app->call($val);
            if (!$db instanceof \DB\SQL)
                throw new \RuntimeException("Hive key '{$key}' did not resolve to a DB\\SQL instance.");

            // Cache the resolved connection for the rest of the request
            $app->set($key, $db);
            return $db;
        }

        throw new \RuntimeException("Hive key '{$key}' is not configured as a DB connection.");
    }

    /**
     * Try to resolve a DB connection like connect(); returns null if the key is missing,
     * misconfigured, or connection fails (instead of throwing).
     */
    public static function get(string $key): ?\DB\SQL
    {
        try {
            return self::connect($key);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
