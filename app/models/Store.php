<?php
/**
 * Store Model
 */

class Store {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get all stores
     */
    public function getAll($page = 1, $perPage = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "s.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['category_id'])) {
            $where[] = "s.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['featured'])) {
            $where[] = "s.is_featured = 1";
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(s.name LIKE ? OR s.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT s.*, c.name as category_name,
                       (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND status = 'active') as coupon_count
                FROM stores s
                LEFT JOIN categories c ON s.category_id = c.id
                WHERE {$whereClause}
                ORDER BY s.is_featured DESC, s.sort_order ASC, s.name ASC
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get total count
     */
    public function getCount($filters = []) {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['category_id'])) {
            $where[] = "category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        $whereClause = implode(' AND ', $where);
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stores WHERE {$whereClause}");
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Get featured stores
     */
    public function getFeatured($limit = 12) {
        $stmt = $this->db->prepare(
            "SELECT s.*, 
                    (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND status = 'active') as coupon_count
             FROM stores s
             WHERE s.status = 'active' AND s.is_featured = 1
             ORDER BY s.sort_order ASC, s.name ASC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get popular stores
     */
    public function getPopular($limit = 12) {
        $stmt = $this->db->prepare(
            "SELECT s.*, 
                    (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND status = 'active') as coupon_count
             FROM stores s
             WHERE s.status = 'active' AND s.is_popular = 1
             ORDER BY s.views_count DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all active stores
     */
    public function getActive() {
        $stmt = $this->db->query(
            "SELECT s.*, 
                    (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND status = 'active') as coupon_count
             FROM stores s
             WHERE s.status = 'active'
             ORDER BY s.name ASC"
        );
        return $stmt->fetchAll();
    }
    
    /**
     * Get store by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT s.*, c.name as category_name, c.slug as category_slug,
                    (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND status = 'active') as coupon_count
             FROM stores s
             LEFT JOIN categories c ON s.category_id = c.id
             WHERE s.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get store by slug
     */
    public function getBySlug($slug) {
        $stmt = $this->db->prepare(
            "SELECT s.*, c.name as category_name, c.slug as category_slug,
                    (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND status = 'active') as coupon_count
             FROM stores s
             LEFT JOIN categories c ON s.category_id = c.id
             WHERE s.slug = ?"
        );
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * Get stores by category
     */
    public function getByCategory($categoryId, $limit = null) {
        $sql = "SELECT s.*, 
                       (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND status = 'active') as coupon_count
                FROM stores s
                WHERE s.category_id = ? AND s.status = 'active'
                ORDER BY s.is_featured DESC, s.name ASC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
        }
        
        $stmt = $this->db->prepare($sql);
        $params = [$categoryId];
        if ($limit) $params[] = $limit;
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Search stores
     */
    public function search($query, $limit = 20) {
        $searchTerm = '%' . $query . '%';
        
        $stmt = $this->db->prepare(
            "SELECT s.*, 
                    (SELECT COUNT(*) FROM coupons WHERE store_id = s.id AND status = 'active') as coupon_count
             FROM stores s
             WHERE s.status = 'active' AND (s.name LIKE ? OR s.description LIKE ?)
             ORDER BY s.is_featured DESC, s.name ASC
             LIMIT ?"
        );
        $stmt->execute([$searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Create store
     */
    public function create($data) {
        $sql = "INSERT INTO stores (name, name_ar, slug, description, description_ar, logo, website,
                affiliate_url, category_id, is_featured, is_popular, sort_order, status,
                seo_title, seo_description, seo_keywords)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['name_ar'] ?? null,
            $data['slug'],
            $data['description'] ?? null,
            $data['description_ar'] ?? null,
            $data['logo'] ?? null,
            $data['website'] ?? null,
            $data['affiliate_url'] ?? null,
            $data['category_id'] ?? null,
            $data['is_featured'] ?? 0,
            $data['is_popular'] ?? 0,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $data['seo_title'] ?? null,
            $data['seo_description'] ?? null,
            $data['seo_keywords'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update store
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['name', 'name_ar', 'slug', 'description', 'description_ar', 'logo',
                          'website', 'affiliate_url', 'category_id', 'is_featured', 'is_popular',
                          'sort_order', 'status', 'seo_title', 'seo_description', 'seo_keywords'];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE stores SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Delete store
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM stores WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Increment view count
     */
    public function incrementViews($id) {
        $stmt = $this->db->prepare("UPDATE stores SET views_count = views_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get statistics
     */
    public function getStats() {
        $stmt = $this->db->query(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured,
                SUM(views_count) as total_views
             FROM stores"
        );
        return $stmt->fetch();
    }
}
