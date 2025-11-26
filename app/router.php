<?php
/**
 * Simple Router for SEO-Friendly URLs
 * Coupons & Deals Website
 */

class Router {
    private $routes = [];
    private $params = [];
    
    /**
     * Add a route to the router
     */
    public function add($pattern, $controller, $action, $method = 'GET') {
        $pattern = '#^' . preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[] = [
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action,
            'method' => $method
        ];
    }
    
    /**
     * Match URL to a route
     */
    public function match($url, $method = 'GET') {
        $url = trim($url, '/');
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') {
                continue;
            }
            
            if (preg_match($route['pattern'], $url, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                $this->params = $params;
                return [
                    'controller' => $route['controller'],
                    'action' => $route['action'],
                    'params' => $params
                ];
            }
        }
        
        return false;
    }
    
    /**
     * Get matched parameters
     */
    public function getParams() {
        return $this->params;
    }
    
    /**
     * Dispatch the route
     */
    public function dispatch($url, $method = 'GET') {
        $match = $this->match($url, $method);
        
        if ($match) {
            $controllerName = $match['controller'];
            $action = $match['action'];
            
            $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                $controllerClass = $controllerName;
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $action)) {
                        return call_user_func_array([$controller, $action], array_values($match['params']));
                    }
                }
            }
        }
        
        // 404 Not Found
        return $this->notFound();
    }
    
    /**
     * Handle 404 errors
     */
    private function notFound() {
        http_response_code(404);
        include APP_PATH . '/views/frontend/404.php';
        exit;
    }
}

// Initialize router with routes
function initRouter() {
    $router = new Router();
    
    // Homepage
    $router->add('', 'HomeController', 'index');
    
    // Stores
    $router->add('stores', 'StoreController', 'index');
    $router->add('store/{slug}', 'StoreController', 'show');
    
    // Categories
    $router->add('categories', 'CategoryController', 'index');
    $router->add('category/{slug}', 'CategoryController', 'show');
    
    // Coupons
    $router->add('coupons', 'CouponController', 'index');
    $router->add('coupon/{id}', 'CouponController', 'show');
    $router->add('expired-coupons', 'CouponController', 'expired');
    
    // Blog/Articles
    $router->add('blog', 'ArticleController', 'index');
    $router->add('blog/{slug}', 'ArticleController', 'show');
    $router->add('blog/category/{slug}', 'ArticleController', 'category');
    $router->add('blog/tag/{slug}', 'ArticleController', 'tag');
    
    // Static pages
    $router->add('page/{slug}', 'PageController', 'show');
    
    // Search
    $router->add('search', 'SearchController', 'index');
    $router->add('search/suggestions', 'SearchController', 'suggestions');
    
    // Language switch
    $router->add('lang/{code}', 'HomeController', 'setLanguage');
    
    // Subscribe
    $router->add('subscribe', 'SubscribeController', 'store', 'POST');
    
    // Sitemap & SEO
    $router->add('sitemap.xml', 'SeoController', 'sitemap');
    $router->add('robots.txt', 'SeoController', 'robots');
    
    return $router;
}
