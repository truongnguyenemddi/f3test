<?php
/** @var \Base $app */

$API_PREFIX = '/api/v1';

/**
*	Auth
**/
$app->route("POST {$API_PREFIX}/auth/login", 'App\Controllers\Api\AuthController->login');
$app->route("POST {$API_PREFIX}/auth/logout", 'App\Controllers\Api\AuthController->logout');
$app->route("GET {$API_PREFIX}/auth/health", 'App\Controllers\Api\AuthHealthController->health');
