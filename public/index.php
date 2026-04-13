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
$f3 = require('../fatfree-core/base.php');

/**
 * Load configuration
 */
$f3->config('../config.ini');
if ((float)PCRE_VERSION < 8.0) trigger_error('PCRE out of date');
if (!is_writable($f3->get('TEMP'))) trigger_error('TEMP not writable');

/**
 * Define Routes 
 * Call Class with Namespace
 */
$f3->route('GET /', 'App\Controllers\MainController->render');

/**
 * Run the application
 */
$f3->run();