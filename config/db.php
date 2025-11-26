<?php
/**
 * Database Configuration
 * Update these settings to match your Hostinger shared hosting database credentials
 */

// Database Host - Usually 'localhost' for shared hosting
define('DB_HOST', 'localhost');

// Database Name - Create this in Hostinger hPanel
define('DB_NAME', 'coupon_website');

// Database Username - Created in Hostinger hPanel
define('DB_USER', 'root');

// Database Password - Set in Hostinger hPanel
define('DB_PASS', '');

// Database Charset
define('DB_CHARSET', 'utf8mb4');

/**
 * PDO Database Connection
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error in production, show generic message
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    return $pdo;
}
