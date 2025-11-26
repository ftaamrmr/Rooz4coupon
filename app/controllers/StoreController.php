<?php
/**
 * Store Controller
 * Handles store listing and individual store pages
 */

require_once APP_PATH . '/controllers/BaseController.php';

class StoreController extends BaseController {
    
    /**
     * List all stores
     */
    public function index() {
        $page = max(1, (int)$this->get('page', 1));
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        
        $category = $this->get('category');
        $search = $this->get('q');
        
        $where = "is_active = 1";
        $params = [];
        
        if ($category) {
            $where .= " AND category_id = :category";
            $params['category'] = $category;
        }
        
        if ($search) {
            $where .= " AND (name LIKE :search OR description_en LIKE :search OR description_ar LIKE :search)";
            $params['search'] = "%$search%";
        }
        
        $totalStores = $this->db->count('stores', $where, $params);
        $totalPages = ceil($totalStores / $perPage);
        
        $stores = $this->db->fetchAll(
            "SELECT s.*, c.name_en as category_name, 
             (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND is_active = 1) as active_coupons
             FROM stores s 
             LEFT JOIN categories c ON s.category_id = c.id 
             WHERE s.$where 
             ORDER BY s.is_featured DESC, s.name 
             LIMIT $perPage OFFSET $offset",
            $params
        );
        
        $categories = $this->db->fetchAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY name_en");
        
        $this->view('frontend/stores', [
            'pageTitle' => __('All Stores', 'جميع المتاجر'),
            'stores' => $stores,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalStores' => $totalStores,
            'selectedCategory' => $category,
            'searchQuery' => $search
        ]);
    }
    
    /**
     * Show single store with its coupons
     */
    public function show($slug) {
        $store = $this->db->fetch(
            "SELECT s.*, c.name_en as category_name, c.slug as category_slug 
             FROM stores s 
             LEFT JOIN categories c ON s.category_id = c.id 
             WHERE s.slug = :slug AND s.is_active = 1",
            ['slug' => $slug]
        );
        
        if (!$store) {
            http_response_code(404);
            $this->view('frontend/404');
            return;
        }
        
        // Increment view count
        $this->db->query("UPDATE stores SET views_count = views_count + 1 WHERE id = :id", ['id' => $store['id']]);
        
        // Get active coupons for this store
        $activeCoupons = $this->db->fetchAll(
            "SELECT * FROM coupons 
             WHERE store_id = :store_id AND is_active = 1 AND (expiry_date IS NULL OR expiry_date >= CURDATE())
             ORDER BY is_featured DESC, created_at DESC",
            ['store_id' => $store['id']]
        );
        
        // Get expired coupons (limited)
        $expiredCoupons = $this->db->fetchAll(
            "SELECT * FROM coupons 
             WHERE store_id = :store_id AND is_active = 1 AND expiry_date < CURDATE()
             ORDER BY expiry_date DESC LIMIT 5",
            ['store_id' => $store['id']]
        );
        
        // Get related stores (same category)
        $relatedStores = $this->db->fetchAll(
            "SELECT * FROM stores 
             WHERE category_id = :category_id AND id != :store_id AND is_active = 1 
             ORDER BY views_count DESC LIMIT 4",
            ['category_id' => $store['category_id'], 'store_id' => $store['id']]
        );
        
        $this->view('frontend/store-single', [
            'pageTitle' => $store['seo_title'] ?: $store['name'] . ' ' . __('Coupons & Deals', 'كوبونات وعروض'),
            'pageDescription' => $store['seo_description'] ?: getLocalizedField($store, 'description'),
            'store' => $store,
            'activeCoupons' => $activeCoupons,
            'expiredCoupons' => $expiredCoupons,
            'relatedStores' => $relatedStores
        ]);
    }
}
