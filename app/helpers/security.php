<?php
/**
 * Security Helper Functions
 */

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Get CSRF Token Input Field
 */
function csrfField() {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . generateCSRFToken() . '">';
}

/**
 * Validate CSRF on POST requests
 */
function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!verifyCSRFToken($token)) {
            http_response_code(403);
            die('Invalid CSRF token. Please refresh the page and try again.');
        }
    }
}

/**
 * Hash password using bcrypt
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify password against hash
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user has specific role
 */
function hasRole($roles) {
    if (!isLoggedIn()) return false;
    
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    
    return in_array($_SESSION['user_role'] ?? '', $roles);
}

/**
 * Require authentication
 */
function requireAuth() {
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to continue.');
        redirect(ADMIN_URL . '/login.php');
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity'])) {
        $timeout = (getSetting('session_timeout', SESSION_TIMEOUT)) * 60;
        if (time() - $_SESSION['last_activity'] > $timeout) {
            logout();
            setFlash('error', 'Your session has expired. Please log in again.');
            redirect(ADMIN_URL . '/login.php');
        }
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Require specific role(s)
 */
function requireRole($roles) {
    requireAuth();
    
    if (!hasRole($roles)) {
        setFlash('error', 'You do not have permission to access this page.');
        redirect(ADMIN_URL . '/dashboard.php');
    }
}

/**
 * Login user
 */
function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['full_name'] ?? $user['username'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['last_activity'] = time();
    
    // Update last login
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    // Log activity
    logActivity('login', 'User logged in');
}

/**
 * Logout user
 */
function logout() {
    logActivity('logout', 'User logged out');
    
    session_unset();
    session_destroy();
    
    // Start new session for flash message
    session_start();
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND status = 'active'");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Log activity
 */
function logActivity($action, $description = '') {
    if (!isLoggedIn()) return;
    
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO activity_log (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'] ?? null,
        $action,
        $description,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}

/**
 * Sanitize input for XSS prevention
 */
function xssSanitize($input) {
    if (is_array($input)) {
        return array_map('xssSanitize', $input);
    }
    return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Clean HTML content (for rich text editor)
 */
function cleanHtml($html) {
    // Define allowed tags for rich text editor
    $allowedTags = '<p><br><strong><b><em><i><u><s><strike><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><pre><code><table><thead><tbody><tr><th><td><hr><span><div><figure><figcaption>';
    
    // Strip disallowed tags
    $html = strip_tags($html, $allowedTags);
    
    // Remove javascript: and data: URLs
    $html = preg_replace('/javascript:/i', '', $html);
    $html = preg_replace('/data:/i', '', $html);
    
    // Remove event handlers
    $html = preg_replace('/\bon\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
    
    return $html;
}

/**
 * Rate limiting (simple implementation for shared hosting)
 */
function checkRateLimit($key, $maxAttempts = 5, $windowSeconds = 300) {
    $rateLimitKey = 'rate_limit_' . md5($key);
    
    if (!isset($_SESSION[$rateLimitKey])) {
        $_SESSION[$rateLimitKey] = [
            'attempts' => 0,
            'window_start' => time()
        ];
    }
    
    $rateLimit = &$_SESSION[$rateLimitKey];
    
    // Reset window if expired
    if (time() - $rateLimit['window_start'] > $windowSeconds) {
        $rateLimit['attempts'] = 0;
        $rateLimit['window_start'] = time();
    }
    
    // Check if limit exceeded
    if ($rateLimit['attempts'] >= $maxAttempts) {
        return false;
    }
    
    $rateLimit['attempts']++;
    return true;
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL format
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Secure redirect (only to allowed domains)
 */
function secureRedirect($url, $allowedDomains = []) {
    $parsedUrl = parse_url($url);
    
    // Allow relative URLs
    if (!isset($parsedUrl['host'])) {
        redirect($url);
    }
    
    // Check if domain is allowed
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $allowedDomains[] = $host;
    
    if (in_array($parsedUrl['host'], $allowedDomains)) {
        redirect($url);
    }
    
    // Redirect to home if domain not allowed
    redirect(BASE_URL);
}

/**
 * Check if request is AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Validate honeypot field (anti-spam)
 */
function validateHoneypot($fieldName = 'website_url') {
    return empty($_POST[$fieldName]);
}
