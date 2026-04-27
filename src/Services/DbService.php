<?php
namespace App\Services;

final class DbService
{
    /**
     * Get (and lazily initialize) a DB connection stored in the F3 hive.
     *
     * Expected hive values:
     * - \DB\SQL instance (already initialized), or
     * - callable that returns \DB\SQL (lazy initializer)
     */
    public static function get(string $key): \DB\SQL
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
}
