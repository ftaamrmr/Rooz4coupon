<?php
/**
 * Search Controller
 * Handles search functionality
 */

require_once APP_PATH . '/controllers/BaseController.php';

class SearchController extends BaseController {
    
    /**
     * Search page with results
     */
    public function index() {
        $query = $this->get('q', '');
        $type = $this->get('type', 'all'); // all, coupons, stores, articles
        
        $results = [
            'coupons' => [],
            'stores' => [],
            'articles' => []
        ];
        
        if (!empty($query)) {
            $searchTerm = "%$query%";
            
            // Search coupons
            if ($type === 'all' || $type === 'coupons') {
                $results['coupons'] = $this->db->fetchAll(
                    "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo 
                     FROM coupons c 
                     JOIN stores s ON c.store_id = s.id 
                     WHERE c.is_active = 1 AND (c.expiry_date IS NULL OR c.expiry_date >= CURDATE())
                     AND (c.title_en LIKE :q1 OR c.title_ar LIKE :q2 OR c.description_en LIKE :q3 OR c.code LIKE :q4)
                     ORDER BY c.is_featured DESC, c.created_at DESC LIMIT 20",
                    ['q1' => $searchTerm, 'q2' => $searchTerm, 'q3' => $searchTerm, 'q4' => $searchTerm]
                );
            }
            
            // Search stores
            if ($type === 'all' || $type === 'stores') {
                $results['stores'] = $this->db->fetchAll(
                    "SELECT s.*, 
                     (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND is_active = 1) as active_coupons
                     FROM stores s 
                     WHERE s.is_active = 1 AND (s.name LIKE :q1 OR s.description_en LIKE :q2 OR s.description_ar LIKE :q3)
                     ORDER BY s.is_featured DESC, s.name LIMIT 20",
                    ['q1' => $searchTerm, 'q2' => $searchTerm, 'q3' => $searchTerm]
                );
            }
            
            // Search articles
            if ($type === 'all' || $type === 'articles') {
                $results['articles'] = $this->db->fetchAll(
                    "SELECT a.*, u.full_name as author_name 
                     FROM articles a 
                     LEFT JOIN users u ON a.author_id = u.id 
                     WHERE a.status = 'published' 
                     AND (a.title_en LIKE :q1 OR a.title_ar LIKE :q2 OR a.content_en LIKE :q3)
                     ORDER BY a.published_at DESC LIMIT 20",
                    ['q1' => $searchTerm, 'q2' => $searchTerm, 'q3' => $searchTerm]
                );
            }
        }
        
        $this->view('frontend/search', [
            'pageTitle' => __('Search Results', 'نتائج البحث') . ($query ? ': ' . $query : ''),
            'query' => $query,
            'type' => $type,
            'results' => $results,
            'totalResults' => count($results['coupons']) + count($results['stores']) + count($results['articles'])
        ]);
    }
    
    /**
     * AJAX search suggestions
     */
    public function suggestions() {
        $query = $this->get('q', '');
        $suggestions = [];
        
        if (strlen($query) >= 2) {
            $searchTerm = "$query%";
            
            // Get store name suggestions
            $stores = $this->db->fetchAll(
                "SELECT name, slug, 'store' as type FROM stores 
                 WHERE is_active = 1 AND name LIKE :q 
                 ORDER BY views_count DESC LIMIT 5",
                ['q' => $searchTerm]
            );
            
            // Get coupon title suggestions
            $coupons = $this->db->fetchAll(
                "SELECT c.title_en as name, c.id, 'coupon' as type, s.name as store_name 
                 FROM coupons c 
                 JOIN stores s ON c.store_id = s.id 
                 WHERE c.is_active = 1 AND c.title_en LIKE :q 
                 ORDER BY c.views_count DESC LIMIT 5",
                ['q' => $searchTerm]
            );
            
            $suggestions = array_merge($stores, $coupons);
        }
        
        $this->json($suggestions);
    }
}
