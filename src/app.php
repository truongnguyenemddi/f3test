<?php
/** @var \Base $app */

/**
 * Framework variables
 */
$app->set('UI', 'src/Views/');

/**
 * Application variables
 */
$app->set('SITE_NAME', 'F3 Skeleton');
$app->set('ASSET_VER', '1.0.0');

/**
 * Load Routes
 */
require 'routes/asset-route.php';
require 'routes/view-route.php';
require 'routes/api-route.php';
