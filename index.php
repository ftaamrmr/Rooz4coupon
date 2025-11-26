<?php
/**
 * Main Entry Point
 * Coupons & Deals Website
 */

// Load configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/router.php';

// Load helpers
require_once __DIR__ . '/app/helpers/functions.php';

// Get the request URL and method
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Remove query string from URI
$uri = parse_url($requestUri, PHP_URL_PATH);

// Remove base path if needed (for subdirectory installations)
$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$uri = substr($uri, strlen($basePath));
$uri = trim($uri, '/');

// Initialize and dispatch router
$router = initRouter();
$router->dispatch($uri, $requestMethod);
