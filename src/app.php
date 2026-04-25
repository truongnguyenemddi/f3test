<?php
/** @var \Base $app */

// Framework variables
$app->set('UI', 'src/Views/');

// Application variables
$app->set('SITE_NAME', 'F3 Skeleton');
$app->set('ASSET_VER', '1.0.0');

// Database configuration
require __DIR__ . '/config/databases.php';

// Load Routes
require __DIR__ . '/routes/asset-route.php';
require __DIR__ . '/routes/web-route.php';
require __DIR__ . '/routes/api-route.php';
