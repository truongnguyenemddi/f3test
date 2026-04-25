<?php
/** @var \Base $app */

$pdoOptions = [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_PERSISTENT => true,
];

// Each key starts as a lazy initializer (callable). First actual use will
// replace the callable with the resolved \DB\SQL instance.
$app->set('maindb', function () use ($pdoOptions): \DB\SQL {
    return new \DB\SQL(
        $_ENV['DB_MAIN_DSN'] ?? '',
        $_ENV['DB_MAIN_USER'] ?? '',
        $_ENV['DB_MAIN_PASS'] ?? '',
        $pdoOptions
    );
});

$app->set('subdb', function () use ($pdoOptions): \DB\SQL {
    return new \DB\SQL(
        $_ENV['DB_SUB_DSN'] ?? '',
        $_ENV['DB_SUB_USER'] ?? '',
        $_ENV['DB_SUB_PASS'] ?? '',
        $pdoOptions
    );
});

$app->set('emddidb', function () use ($pdoOptions): \DB\SQL {
    return new \DB\SQL(
        $_ENV['DB_EMDDI_DSN'] ?? '',
        $_ENV['DB_EMDDI_USER'] ?? '',
        $_ENV['DB_EMDDI_PASS'] ?? '',
        $pdoOptions
    );
});
