<?php
/** @var \Base $app */

// Framework variables
$app->set('UI', 'src/Views/');

// Database connection
$db = new \DB\SQL(
    $_ENV['DB_DSN'] ?? '',
    $_ENV['DB_USER'] ?? '',
    $_ENV['DB_PASS'] ?? ''
);
$app->set('DB', $db);

// Application variables
$app->set('SITE_NAME', 'F3 Skeleton');
$app->set('ASSET_VER', '1.0.0');

// Load Routes
require 'routes/asset-route.php';
require 'routes/view-route.php';
require 'routes/api-route.php';
