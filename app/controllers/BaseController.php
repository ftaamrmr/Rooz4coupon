<?php
/**
 * Base Controller
 * Provides common functionality for all controllers
 */

class BaseController {
    protected $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Load a view file
     */
    protected function view($template, $data = []) {
        extract($data);
        $viewPath = APP_PATH . '/views/' . $template . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View not found: $template";
        }
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Get POST data with sanitization
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return sanitize($_POST);
        }
        return isset($_POST[$key]) ? sanitize($_POST[$key]) : $default;
    }
    
    /**
     * Get GET data with sanitization
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return sanitize($_GET);
        }
        return isset($_GET[$key]) ? sanitize($_GET[$key]) : $default;
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCSRF() {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!verifyCSRFToken($token)) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        return true;
    }
}
