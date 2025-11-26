<?php
/**
 * Application Configuration
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');

// Application paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('ADMIN_PATH', ROOT_PATH . '/admin');

// URL Configuration - Update for your domain
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $protocol . $host);
define('ADMIN_URL', BASE_URL . '/admin');
define('ASSETS_URL', BASE_URL . '/public');
define('UPLOAD_URL', ASSETS_URL . '/uploads');

// Session timeout in minutes
define('SESSION_TIMEOUT', 30);

// CSRF Token name
define('CSRF_TOKEN_NAME', 'csrf_token');

// Upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Cache settings (for shared hosting without Redis)
define('CACHE_ENABLED', false);
define('CACHE_PATH', ROOT_PATH . '/cache');
define('CACHE_TTL', 3600); // 1 hour

// Language settings
define('DEFAULT_LANGUAGE', 'en');
define('AVAILABLE_LANGUAGES', ['en', 'ar']);

// Include required files
require_once CONFIG_PATH . '/db.php';
require_once APP_PATH . '/helpers/functions.php';
require_once APP_PATH . '/helpers/security.php';
