<?php
/** @var \Base $app */

/**
*	Public routes
**/
$app->route('GET /login', 'App\Controllers\Web\GuestController->renderLogin');
$app->route('GET /login-jwt', 'App\Controllers\Web\GuestController->renderLoginJwt');
$app->route('POST /login', 'App\Controllers\Web\AuthController->login');
$app->route('GET /logout', 'App\Controllers\Web\AuthController->logout');
$app->route('GET /about', 'App\Controllers\Web\PublicController->renderAbout');
$app->route('GET /contact', 'App\Controllers\Web\PublicController->renderContact');


/**
*	Admin protected routes
**/
$app->route('GET /', 'App\Controllers\Web\AdminController->renderHome');
