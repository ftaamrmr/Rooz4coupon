<?php
/**
 * Simple Router for SEO-friendly URLs
 */

class Router {
    private $routes = [];
    private $notFoundCallback;
    
    /**
     * Add a GET route
     */
    public function get($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
    }
    
    /**
     * Add a POST route
     */
    public function post($pattern, $callback) {
        $this->addRoute('POST', $pattern, $callback);
    }
    
    /**
     * Add route with any method
     */
    public function any($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
        $this->addRoute('POST', $pattern, $callback);
    }
    
    /**
     * Add a route
     */
    private function addRoute($method, $pattern, $callback) {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }
    
    /**
     * Set 404 handler
     */
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
    }
    
    /**
     * Dispatch the router
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Call the callback
                if (is_callable($route['callback'])) {
                    call_user_func_array($route['callback'], $params);
                } elseif (is_string($route['callback'])) {
                    // Controller@method format
                    $this->callController($route['callback'], $params);
                }
                return;
            }
        }
        
        // No route matched
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            $this->default404();
        }
    }
    
    /**
     * Get cleaned URI
     */
    private function getUri() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove trailing slash
        $uri = rtrim($uri, '/');
        
        // Default to home
        if (empty($uri)) {
            $uri = '/';
        }
        
        return $uri;
    }
    
    /**
     * Call controller method
     */
    private function callController($callback, $params) {
        list($controller, $method) = explode('@', $callback);
        
        $controllerFile = APP_PATH . '/controllers/' . $controller . '.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            
            $controllerInstance = new $controller();
            if (method_exists($controllerInstance, $method)) {
                call_user_func_array([$controllerInstance, $method], $params);
                return;
            }
        }
        
        $this->default404();
    }
    
    /**
     * Default 404 response
     */
    private function default404() {
        http_response_code(404);
        include APP_PATH . '/views/frontend/404.php';
    }
}

/**
 * Simple View loader
 */
function view($name, $data = []) {
    extract($data);
    
    $viewFile = APP_PATH . '/views/' . $name . '.php';
    
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        echo "View not found: " . htmlspecialchars($name);
    }
}

/**
 * Load partial view
 */
function partial($name, $data = []) {
    view('partials/' . $name, $data);
}
