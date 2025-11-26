<?php
/**
 * Category Controller
 * Handles category listing and individual category pages
 */

require_once APP_PATH . '/controllers/BaseController.php';

class CategoryController extends BaseController {
    
    /**
     * List all categories
     */
    public function index() {
        $categories = $this->db->fetchAll(
            "SELECT c.*, 
             (SELECT COUNT(*) FROM stores WHERE category_id = c.id AND is_active = 1) as stores_count,
             (SELECT COUNT(*) FROM coupons cp JOIN stores s ON cp.store_id = s.id WHERE s.category_id = c.id AND cp.is_active = 1) as coupons_count
             FROM categories c 
             WHERE c.is_active = 1 AND c.parent_id IS NULL 
             ORDER BY c.order_position, c.name_en"
        );
        
        $this->view('frontend/categories', [
            'pageTitle' => __('All Categories', 'جميع الفئات'),
            'categories' => $categories
        ]);
    }
    
    /**
     * Show category with its stores and coupons
     */
    public function show($slug) {
        $category = $this->db->fetch(
            "SELECT * FROM categories WHERE slug = :slug AND is_active = 1",
            ['slug' => $slug]
        );
        
        if (!$category) {
            http_response_code(404);
            $this->view('frontend/404');
            return;
        }
        
        $page = max(1, (int)$this->get('page', 1));
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        
        // Get stores in this category
        $stores = $this->db->fetchAll(
            "SELECT s.*, 
             (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND is_active = 1) as active_coupons
             FROM stores s 
             WHERE s.category_id = :category_id AND s.is_active = 1 
             ORDER BY s.is_featured DESC, s.name",
            ['category_id' => $category['id']]
        );
        
        // Get coupons in this category
        $totalCoupons = $this->db->fetch(
            "SELECT COUNT(*) as count FROM coupons c 
             JOIN stores s ON c.store_id = s.id 
             WHERE s.category_id = :category_id AND c.is_active = 1 
             AND (c.expiry_date IS NULL OR c.expiry_date >= CURDATE())",
            ['category_id' => $category['id']]
        )['count'];
        
        $totalPages = ceil($totalCoupons / $perPage);
        
        $coupons = $this->db->fetchAll(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo 
             FROM coupons c 
             JOIN stores s ON c.store_id = s.id 
             WHERE s.category_id = :category_id AND c.is_active = 1 
             AND (c.expiry_date IS NULL OR c.expiry_date >= CURDATE())
             ORDER BY c.is_featured DESC, c.created_at DESC 
             LIMIT $perPage OFFSET $offset",
            ['category_id' => $category['id']]
        );
        
        // Get subcategories
        $subcategories = $this->db->fetchAll(
            "SELECT * FROM categories WHERE parent_id = :parent_id AND is_active = 1 ORDER BY order_position",
            ['parent_id' => $category['id']]
        );
        
        $this->view('frontend/category-single', [
            'pageTitle' => getLocalizedField($category, 'name') . ' ' . __('Coupons & Deals', 'كوبونات وعروض'),
            'pageDescription' => getLocalizedField($category, 'description'),
            'category' => $category,
            'stores' => $stores,
            'coupons' => $coupons,
            'subcategories' => $subcategories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCoupons' => $totalCoupons
        ]);
    }
}
