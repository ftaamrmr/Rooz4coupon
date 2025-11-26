<?php
/**
 * CouponHub - Main Entry Point
 * A Professional Coupons & Deals Website
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Load models
require_once APP_PATH . '/models/Coupon.php';
require_once APP_PATH . '/models/Store.php';
require_once APP_PATH . '/models/Category.php';
require_once APP_PATH . '/models/Article.php';
require_once APP_PATH . '/models/User.php';

// Load router
require_once APP_PATH . '/router.php';

// Auto-expire coupons (runs once per day with simple check)
if (!isset($_SESSION['last_expire_check']) || $_SESSION['last_expire_check'] < strtotime('today')) {
    $couponModel = new Coupon();
    $couponModel->autoExpire();
    $_SESSION['last_expire_check'] = time();
}

// Create router instance
$router = new Router();

// Define routes

// Homepage
$router->get('/', function() {
    $couponModel = new Coupon();
    $storeModel = new Store();
    $categoryModel = new Category();
    $articleModel = new Article();
    
    $data = [
        'pageTitle' => getSetting('meta_title', 'CouponHub - Best Coupons & Deals'),
        'metaDescription' => getSetting('meta_description'),
        'featuredCoupons' => $couponModel->getFeatured(8),
        'latestCoupons' => $couponModel->getLatest(12),
        'popularCoupons' => $couponModel->getPopular(8),
        'featuredStores' => $storeModel->getFeatured(12),
        'categories' => $categoryModel->getAll(),
        'latestArticles' => $articleModel->getLatest(4)
    ];
    
    view('frontend/home', $data);
});

// Store page
$router->get('/store/{slug}', function($slug) {
    $storeModel = new Store();
    $couponModel = new Coupon();
    
    $store = $storeModel->getBySlug($slug);
    
    if (!$store) {
        http_response_code(404);
        view('frontend/404');
        return;
    }
    
    // Increment views
    $storeModel->incrementViews($store['id']);
    
    $coupons = $couponModel->getByStore($store['id']);
    $expiredCoupons = $couponModel->getByStore($store['id'], 10, false);
    
    $data = [
        'pageTitle' => ($store['seo_title'] ?? $store['name'] . ' Coupons & Deals'),
        'metaDescription' => $store['seo_description'] ?? $store['description'],
        'store' => $store,
        'coupons' => $coupons,
        'expiredCoupons' => array_filter($expiredCoupons, function($c) { return $c['status'] === 'expired'; })
    ];
    
    view('frontend/store', $data);
});

// Coupon details page
$router->get('/coupon/{slug}', function($slug) {
    $couponModel = new Coupon();
    
    $coupon = $couponModel->getBySlug($slug);
    
    if (!$coupon) {
        http_response_code(404);
        view('frontend/404');
        return;
    }
    
    // Increment views
    $couponModel->incrementViews($coupon['id']);
    
    $relatedCoupons = $couponModel->getRelated($coupon['id'], $coupon['store_id'], $coupon['category_id']);
    
    $data = [
        'pageTitle' => ($coupon['seo_title'] ?? $coupon['title']),
        'metaDescription' => $coupon['seo_description'] ?? $coupon['description'],
        'coupon' => $coupon,
        'relatedCoupons' => $relatedCoupons
    ];
    
    view('frontend/coupon', $data);
});

// Category page
$router->get('/category/{slug}', function($slug) {
    $categoryModel = new Category();
    $couponModel = new Coupon();
    $storeModel = new Store();
    
    $category = $categoryModel->getBySlug($slug);
    
    if (!$category) {
        http_response_code(404);
        view('frontend/404');
        return;
    }
    
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $coupons = $couponModel->getByCategory($category['id'], $page);
    $stores = $storeModel->getByCategory($category['id']);
    $totalCoupons = $couponModel->getCount(['category_id' => $category['id'], 'status' => 'active']);
    $pagination = paginate($totalCoupons, ITEMS_PER_PAGE, $page, '/category/' . $slug);
    
    $data = [
        'pageTitle' => ($category['seo_title'] ?? $category['name'] . ' Deals & Coupons'),
        'metaDescription' => $category['seo_description'] ?? $category['description'],
        'category' => $category,
        'coupons' => $coupons,
        'stores' => $stores,
        'pagination' => $pagination
    ];
    
    view('frontend/category', $data);
});

// All stores page
$router->get('/stores', function() {
    $storeModel = new Store();
    $categoryModel = new Category();
    
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : null;
    
    $filters = ['status' => 'active'];
    if ($categoryFilter) {
        $filters['category_id'] = $categoryFilter;
    }
    
    $stores = $storeModel->getAll($page, ITEMS_PER_PAGE, $filters);
    $totalStores = $storeModel->getCount($filters);
    $pagination = paginate($totalStores, ITEMS_PER_PAGE, $page, '/stores');
    $categories = $categoryModel->getAll();
    
    $data = [
        'pageTitle' => 'All Stores - CouponHub',
        'metaDescription' => 'Browse all stores and find the best coupons and deals',
        'stores' => $stores,
        'categories' => $categories,
        'pagination' => $pagination,
        'selectedCategory' => $categoryFilter
    ];
    
    view('frontend/stores', $data);
});

// All coupons page
$router->get('/coupons', function() {
    $couponModel = new Coupon();
    $categoryModel = new Category();
    
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    
    $filters = ['status' => 'active'];
    $coupons = $couponModel->getAll($page, ITEMS_PER_PAGE, $filters);
    $totalCoupons = $couponModel->getCount($filters);
    $pagination = paginate($totalCoupons, ITEMS_PER_PAGE, $page, '/coupons');
    $categories = $categoryModel->getAll();
    
    $data = [
        'pageTitle' => 'All Coupons - CouponHub',
        'metaDescription' => 'Browse all available coupons and promo codes',
        'coupons' => $coupons,
        'categories' => $categories,
        'pagination' => $pagination
    ];
    
    view('frontend/coupons', $data);
});

// Expired coupons archive
$router->get('/expired', function() {
    $couponModel = new Coupon();
    
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $coupons = $couponModel->getAll($page, ITEMS_PER_PAGE, ['status' => 'expired']);
    $totalCoupons = $couponModel->getCount(['status' => 'expired']);
    $pagination = paginate($totalCoupons, ITEMS_PER_PAGE, $page, '/expired');
    
    $data = [
        'pageTitle' => 'Expired Coupons Archive - CouponHub',
        'metaDescription' => 'Browse expired coupons that may still work',
        'coupons' => $coupons,
        'pagination' => $pagination
    ];
    
    view('frontend/expired', $data);
});

// Blog/Articles listing
$router->get('/blog', function() {
    $articleModel = new Article();
    
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $articles = $articleModel->getPublished($page, ITEMS_PER_PAGE);
    $totalArticles = $articleModel->getPublishedCount();
    $pagination = paginate($totalArticles, ITEMS_PER_PAGE, $page, '/blog');
    
    $data = [
        'pageTitle' => 'Blog - CouponHub',
        'metaDescription' => 'Read our latest articles about saving money, deals, and shopping tips',
        'articles' => $articles,
        'pagination' => $pagination
    ];
    
    view('frontend/blog', $data);
});

// Single article page
$router->get('/blog/{slug}', function($slug) {
    $articleModel = new Article();
    
    $article = $articleModel->getBySlug($slug);
    
    if (!$article) {
        http_response_code(404);
        view('frontend/404');
        return;
    }
    
    // Increment views
    $articleModel->incrementViews($article['id']);
    
    $tags = $articleModel->getTags($article['id']);
    $relatedArticles = $articleModel->getRelated($article['id'], $article['category_id']);
    
    $data = [
        'pageTitle' => ($article['seo_title'] ?? $article['title']),
        'metaDescription' => $article['seo_description'] ?? $article['excerpt'],
        'article' => $article,
        'tags' => $tags,
        'relatedArticles' => $relatedArticles
    ];
    
    view('frontend/article', $data);
});

// Search page
$router->get('/search', function() {
    $query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
    
    $couponModel = new Coupon();
    $storeModel = new Store();
    $articleModel = new Article();
    
    $coupons = [];
    $stores = [];
    $articles = [];
    
    if (!empty($query)) {
        $coupons = $couponModel->search($query, 20);
        $stores = $storeModel->search($query, 10);
        $articles = $articleModel->search($query, 10);
    }
    
    $data = [
        'pageTitle' => 'Search Results for "' . htmlspecialchars($query) . '" - CouponHub',
        'query' => $query,
        'coupons' => $coupons,
        'stores' => $stores,
        'articles' => $articles
    ];
    
    view('frontend/search', $data);
});

// AJAX Search suggestions
$router->get('/api/search', function() {
    $query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
    
    if (strlen($query) < 2) {
        jsonResponse(['results' => []]);
    }
    
    $couponModel = new Coupon();
    $storeModel = new Store();
    
    $coupons = $couponModel->search($query, 5);
    $stores = $storeModel->search($query, 5);
    
    $results = [];
    
    foreach ($stores as $store) {
        $results[] = [
            'type' => 'store',
            'title' => $store['name'],
            'url' => '/store/' . $store['slug'],
            'image' => $store['logo'] ? UPLOAD_URL . '/' . $store['logo'] : null,
            'count' => $store['coupon_count'] . ' coupons'
        ];
    }
    
    foreach ($coupons as $coupon) {
        $results[] = [
            'type' => 'coupon',
            'title' => $coupon['title'],
            'url' => '/coupon/' . $coupon['slug'],
            'store' => $coupon['store_name'],
            'discount' => formatDiscount($coupon['discount_type'], $coupon['discount_value'])
        ];
    }
    
    jsonResponse(['results' => $results]);
});

// Track coupon click (AJAX)
$router->post('/api/coupon/click/{id}', function($id) {
    $couponModel = new Coupon();
    $couponModel->incrementUsed((int)$id);
    jsonResponse(['success' => true]);
});

// Newsletter subscription
$router->post('/api/subscribe', function() {
    validateCSRF();
    
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    
    if (!isValidEmail($email)) {
        jsonResponse(['success' => false, 'error' => 'Invalid email address'], 400);
    }
    
    $db = getDB();
    
    try {
        $stmt = $db->prepare("INSERT INTO subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE email = email");
        $stmt->execute([$email]);
        jsonResponse(['success' => true, 'message' => 'Thank you for subscribing!']);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'error' => 'Subscription failed'], 500);
    }
});

// Language switch
$router->get('/lang/{lang}', function($lang) {
    if (in_array($lang, AVAILABLE_LANGUAGES)) {
        $_SESSION['language'] = $lang;
    }
    
    $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
    redirect($referer);
});

// Sitemap
$router->get('/sitemap.xml', function() {
    require_once ROOT_PATH . '/sitemap-generator.php';
});

// Robots.txt
$router->get('/robots.txt', function() {
    header('Content-Type: text/plain');
    echo "User-agent: *\n";
    echo "Allow: /\n";
    echo "Disallow: /admin/\n";
    echo "Disallow: /api/\n";
    echo "Sitemap: " . BASE_URL . "/sitemap.xml\n";
    exit;
});

// 404 handler
$router->notFound(function() {
    http_response_code(404);
    view('frontend/404', ['pageTitle' => 'Page Not Found - CouponHub']);
});

// Dispatch the router
$router->dispatch();
