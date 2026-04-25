<?php
/** @var \Base $app */

// Public routes
$app->route('GET /login', 'App\Controllers\GuestController->renderLogin');
$app->route('POST /login', 'App\Controllers\AuthController->login');
$app->route('GET /logout', 'App\Controllers\AuthController->logout');
$app->route('GET /about', 'App\Controllers\PublicController->renderAbout');
$app->route('GET /contact', 'App\Controllers\PublicController->renderContact');

// Admin protected routes
$app->route('GET /', 'App\Controllers\AdminController->renderHome');
