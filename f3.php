<?php
/**
 * Autoloader
 */
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

/**
 * Fat-Free Core Framework
 */
$f3 = require('fatfree-core/base.php');

/**
 * Load configuration
 */
$f3->set('DEBUG', 3); // 0-production 1-low 2-medium 3-high
$f3->set('CACHE', true);
$f3->set('ROOT', __DIR__);
$f3->set('AUTOLOAD', 'src/');

/**
 * Trigger outdated error
 */
if ((float)PCRE_VERSION < 8.0) trigger_error('PCRE out of date');

/**
 * Load the application
 */
(function($app) {
    require 'src/app.php';
})($f3);

/**
 * Run the application
 */
$f3->run();
