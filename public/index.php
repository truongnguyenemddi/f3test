<?php
/**
 * Autoloader
 */
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
}

/**
 * Fat-Free Core Framework
 */
$app = require('../fatfree-core/base.php');

/**
 * Load configuration
 */
$app->config('../config.ini');
if ((float)PCRE_VERSION < 8.0) trigger_error('PCRE out of date');
if (!is_writable($app->get('TEMP'))) trigger_error('TEMP not writable');

/**
 * Load Routes
 */
require_once '../src/routes.php';
require_once '../src/api-routes.php';

/**
 * Run the application
 */
$app->run();