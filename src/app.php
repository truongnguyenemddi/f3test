<?php
/** @var \Base $app */

// Framework variables
$app->set('UI', 'src/Views/');

// Main database connection
$db = new \DB\SQL(
    $_ENV['DB_DSN'] ?? '',
    $_ENV['DB_USER'] ?? '',
    $_ENV['DB_PASS'] ?? ''
);
$app->set('DB', $db);

// Sub database connection
$subDb = new \DB\SQL(
    $_ENV['DB_SUB_DSN'] ?? '',
    $_ENV['DB_SUB_USER'] ?? '',
    $_ENV['DB_SUB_PASS'] ?? ''
);
$app->set('DB_SUB', $subDb);

// Emddi database connection
$emddiDb = new \DB\SQL(
    $_ENV['DB_EMDDI_DSN'] ?? '',
    $_ENV['DB_EMDDI_USER'] ?? '',
    $_ENV['DB_EMDDI_PASS'] ?? ''
);
$app->set('DB_EMDDI', $emddiDb);

// Application variables
$app->set('SITE_NAME', 'Emddi | Quản lý hệ thống đặt và điều vận xe | NAT 2.0.0');
$app->set('ASSET_VER', '1.0.0');

// Load Routes
require 'routes/asset-route.php';
require 'routes/view-route.php';
require 'routes/api-route.php';
