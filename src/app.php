<?php
/** @var \Base $app */

// Framework variables
$app->set('UI', 'src/Views/');

// Application variables
$app->set('SITE_NAME', 'Emddi | Quản lý hệ thống đặt và điều vận xe | NAT 2.0.0');
$app->set('ASSET_VER', '1.0.0');

// Database configuration
require __DIR__ . '/config/databases.php';

// Load Routes
require __DIR__ . '/routes/asset-route.php';
require __DIR__ . '/routes/view-route.php';
require __DIR__ . '/routes/api-route.php';
