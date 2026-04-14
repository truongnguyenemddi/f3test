<?php
/** @var \Base $app */

// Get user list
$app->route('GET /api/v1/users', 'App\Controllers\Api\UserController->list');

// Get product details
$app->route('GET /api/v1/product/@id', 'App\Controllers\Api\ProductController->get');

// Handle send form to AJAX (POST)
$app->route('POST /api/v1/contact', 'App\Controllers\Api\ContactController->submit');