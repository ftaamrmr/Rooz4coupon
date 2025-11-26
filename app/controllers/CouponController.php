<?php
/**
 * Coupon Controller
 * Handles coupon listing and individual coupon pages
 */

require_once APP_PATH . '/controllers/BaseController.php';

class CouponController extends BaseController {
    
    /**
     * List all active coupons
     */
    public function index() {
        $page = max(1, (int)$this->get('page', 1));
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        
        $type = $this->get('type');
        $store = $this->get('store');
        
        $where = "c.is_active = 1 AND (c.expiry_date IS NULL OR c.expiry_date >= CURDATE())";
        $params = [];
        
        if ($type) {
            $where .= " AND c.discount_type = :type";
            $params['type'] = $type;
        }
        
        if ($store) {
            $where .= " AND s.slug = :store";
            $params['store'] = $store;
        }
        
        $totalCoupons = $this->db->fetch(
            "SELECT COUNT(*) as count FROM coupons c JOIN stores s ON c.store_id = s.id WHERE $where",
            $params
        )['count'];
        
        $totalPages = ceil($totalCoupons / $perPage);
        
        $coupons = $this->db->fetchAll(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo 
             FROM coupons c 
             JOIN stores s ON c.store_id = s.id 
             WHERE $where 
             ORDER BY c.is_featured DESC, c.created_at DESC 
             LIMIT $perPage OFFSET $offset",
            $params
        );
        
        $this->view('frontend/coupons', [
            'pageTitle' => __('All Coupons & Deals', 'جميع الكوبونات والعروض'),
            'coupons' => $coupons,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCoupons' => $totalCoupons,
            'selectedType' => $type
        ]);
    }
    
    /**
     * Show single coupon details
     */
    public function show($id) {
        $coupon = $this->db->fetch(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo, 
             s.website_url as store_url, s.description_en as store_description
             FROM coupons c 
             JOIN stores s ON c.store_id = s.id 
             WHERE c.id = :id AND c.is_active = 1",
            ['id' => $id]
        );
        
        if (!$coupon) {
            http_response_code(404);
            $this->view('frontend/404');
            return;
        }
        
        // Increment view count
        $this->db->query("UPDATE coupons SET views_count = views_count + 1 WHERE id = :id", ['id' => $id]);
        
        // Get related coupons from same store
        $relatedCoupons = $this->db->fetchAll(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo 
             FROM coupons c 
             JOIN stores s ON c.store_id = s.id 
             WHERE c.store_id = :store_id AND c.id != :id AND c.is_active = 1 
             AND (c.expiry_date IS NULL OR c.expiry_date >= CURDATE())
             ORDER BY c.is_featured DESC LIMIT 4",
            ['store_id' => $coupon['store_id'], 'id' => $id]
        );
        
        $this->view('frontend/coupon-single', [
            'pageTitle' => getLocalizedField($coupon, 'title'),
            'pageDescription' => getLocalizedField($coupon, 'description'),
            'coupon' => $coupon,
            'relatedCoupons' => $relatedCoupons
        ]);
    }
    
    /**
     * Show expired coupons archive
     */
    public function expired() {
        $page = max(1, (int)$this->get('page', 1));
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        
        $totalCoupons = $this->db->count('coupons', 'is_active = 1 AND expiry_date < CURDATE()');
        $totalPages = ceil($totalCoupons / $perPage);
        
        $coupons = $this->db->fetchAll(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo 
             FROM coupons c 
             JOIN stores s ON c.store_id = s.id 
             WHERE c.is_active = 1 AND c.expiry_date < CURDATE()
             ORDER BY c.expiry_date DESC 
             LIMIT $perPage OFFSET $offset"
        );
        
        $this->view('frontend/coupons-expired', [
            'pageTitle' => __('Expired Coupons Archive', 'أرشيف الكوبونات المنتهية'),
            'coupons' => $coupons,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCoupons' => $totalCoupons
        ]);
    }
    
    /**
     * Record coupon use (AJAX)
     */
    public function recordUse() {
        $this->validateCSRF();
        
        $id = (int)$this->post('id');
        if ($id) {
            $this->db->query("UPDATE coupons SET uses_count = uses_count + 1 WHERE id = :id", ['id' => $id]);
            $this->json(['success' => true]);
        }
        
        $this->json(['error' => 'Invalid coupon'], 400);
    }
}
