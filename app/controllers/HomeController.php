<?php
/**
 * Home Controller
 * Handles homepage and language switching
 */

require_once APP_PATH . '/controllers/BaseController.php';

class HomeController extends BaseController {
    
    /**
     * Display homepage
     */
    public function index() {
        // Get featured stores
        $featuredStores = $this->db->fetchAll(
            "SELECT * FROM stores WHERE is_featured = 1 AND is_active = 1 ORDER BY name LIMIT 8"
        );
        
        // Get latest coupons
        $latestCoupons = $this->db->fetchAll(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo 
             FROM coupons c 
             JOIN stores s ON c.store_id = s.id 
             WHERE c.is_active = 1 AND (c.expiry_date IS NULL OR c.expiry_date >= CURDATE())
             ORDER BY c.created_at DESC LIMIT 12"
        );
        
        // Get featured coupons
        $featuredCoupons = $this->db->fetchAll(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo 
             FROM coupons c 
             JOIN stores s ON c.store_id = s.id 
             WHERE c.is_featured = 1 AND c.is_active = 1 AND (c.expiry_date IS NULL OR c.expiry_date >= CURDATE())
             ORDER BY c.created_at DESC LIMIT 6"
        );
        
        // Get categories
        $categories = $this->db->fetchAll(
            "SELECT * FROM categories WHERE is_active = 1 ORDER BY order_position, name_en LIMIT 12"
        );
        
        // Get popular stores (by views)
        $popularStores = $this->db->fetchAll(
            "SELECT * FROM stores WHERE is_active = 1 ORDER BY views_count DESC LIMIT 8"
        );
        
        // Get latest articles
        $latestArticles = $this->db->fetchAll(
            "SELECT a.*, u.full_name as author_name 
             FROM articles a 
             LEFT JOIN users u ON a.author_id = u.id 
             WHERE a.status = 'published' 
             ORDER BY a.published_at DESC LIMIT 4"
        );
        
        // Stats
        $stats = [
            'total_coupons' => $this->db->count('coupons', 'is_active = 1'),
            'total_stores' => $this->db->count('stores', 'is_active = 1'),
            'active_deals' => $this->db->count('coupons', 'is_active = 1 AND (expiry_date IS NULL OR expiry_date >= CURDATE())')
        ];
        
        $this->view('frontend/home', [
            'pageTitle' => getSetting('hero_title_' . getCurrentLang(), 'Find the Best Deals & Coupons'),
            'featuredStores' => $featuredStores,
            'latestCoupons' => $latestCoupons,
            'featuredCoupons' => $featuredCoupons,
            'categories' => $categories,
            'popularStores' => $popularStores,
            'latestArticles' => $latestArticles,
            'stats' => $stats
        ]);
    }
    
    /**
     * Set language
     */
    public function setLanguage($code) {
        if (in_array($code, ['en', 'ar'])) {
            $_SESSION['lang'] = $code;
        }
        
        // Redirect back or to homepage
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        redirect($referer);
    }
}
