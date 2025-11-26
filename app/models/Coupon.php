<?php
/**
 * Coupon Model
 */

class Coupon {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get all coupons with pagination
     */
    public function getAll($page = 1, $perPage = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $where = ["c.status != 'pending'"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "c.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['store_id'])) {
            $where[] = "c.store_id = ?";
            $params[] = $filters['store_id'];
        }
        
        if (!empty($filters['category_id'])) {
            $where[] = "c.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['featured'])) {
            $where[] = "c.is_featured = 1";
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(c.title LIKE ? OR c.code LIKE ? OR s.name LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo, 
                       cat.name as category_name, cat.slug as category_slug
                FROM coupons c
                LEFT JOIN stores s ON c.store_id = s.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE {$whereClause}
                ORDER BY c.is_featured DESC, c.created_at DESC
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
        $where = ["status != 'pending'"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['store_id'])) {
            $where[] = "store_id = ?";
            $params[] = $filters['store_id'];
        }
        
        if (!empty($filters['category_id'])) {
            $where[] = "category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        $whereClause = implode(' AND ', $where);
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM coupons WHERE {$whereClause}");
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Get featured coupons
     */
    public function getFeatured($limit = 8) {
        $stmt = $this->db->prepare(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo
             FROM coupons c
             LEFT JOIN stores s ON c.store_id = s.id
             WHERE c.status = 'active' AND c.is_featured = 1
             ORDER BY c.created_at DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get latest coupons
     */
    public function getLatest($limit = 12) {
        $stmt = $this->db->prepare(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo
             FROM coupons c
             LEFT JOIN stores s ON c.store_id = s.id
             WHERE c.status = 'active'
             ORDER BY c.created_at DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get popular coupons
     */
    public function getPopular($limit = 8) {
        $stmt = $this->db->prepare(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo
             FROM coupons c
             LEFT JOIN stores s ON c.store_id = s.id
             WHERE c.status = 'active'
             ORDER BY c.used_count DESC, c.views_count DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get expired coupons
     */
    public function getExpired($limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo
             FROM coupons c
             LEFT JOIN stores s ON c.store_id = s.id
             WHERE c.status = 'expired'
             ORDER BY c.expiry_date DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get coupon by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo,
                    cat.name as category_name, cat.slug as category_slug
             FROM coupons c
             LEFT JOIN stores s ON c.store_id = s.id
             LEFT JOIN categories cat ON c.category_id = cat.id
             WHERE c.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get coupon by slug
     */
    public function getBySlug($slug) {
        $stmt = $this->db->prepare(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo,
                    s.description as store_description, s.website as store_website,
                    cat.name as category_name, cat.slug as category_slug
             FROM coupons c
             LEFT JOIN stores s ON c.store_id = s.id
             LEFT JOIN categories cat ON c.category_id = cat.id
             WHERE c.slug = ?"
        );
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * Get coupons by store
     */
    public function getByStore($storeId, $limit = null, $excludeExpired = true) {
        $sql = "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo
                FROM coupons c
                LEFT JOIN stores s ON c.store_id = s.id
                WHERE c.store_id = ?";
        
        if ($excludeExpired) {
            $sql .= " AND c.status = 'active'";
        }
        
        $sql .= " ORDER BY c.is_featured DESC, c.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
        }
        
        $stmt = $this->db->prepare($sql);
        $params = [$storeId];
        if ($limit) $params[] = $limit;
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get coupons by category
     */
    public function getByCategory($categoryId, $page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo
             FROM coupons c
             LEFT JOIN stores s ON c.store_id = s.id
             WHERE c.category_id = ? AND c.status = 'active'
             ORDER BY c.is_featured DESC, c.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$categoryId, $perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Search coupons
     */
    public function search($query, $limit = 20) {
        $searchTerm = '%' . $query . '%';
        
        $stmt = $this->db->prepare(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo
             FROM coupons c
             LEFT JOIN stores s ON c.store_id = s.id
             WHERE c.status = 'active' AND (c.title LIKE ? OR c.code LIKE ? OR c.description LIKE ? OR s.name LIKE ?)
             ORDER BY c.is_featured DESC, c.created_at DESC
             LIMIT ?"
        );
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Create coupon
     */
    public function create($data) {
        $sql = "INSERT INTO coupons (title, title_ar, slug, description, description_ar, code, 
                discount_type, discount_value, store_id, category_id, affiliate_url, image,
                start_date, expiry_date, is_featured, is_verified, is_exclusive, status, 
                seo_title, seo_description, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['title'],
            $data['title_ar'] ?? null,
            $data['slug'],
            $data['description'] ?? null,
            $data['description_ar'] ?? null,
            $data['code'] ?? null,
            $data['discount_type'] ?? 'percentage',
            $data['discount_value'] ?? null,
            $data['store_id'],
            $data['category_id'] ?? null,
            $data['affiliate_url'] ?? null,
            $data['image'] ?? null,
            $data['start_date'] ?? null,
            $data['expiry_date'] ?? null,
            $data['is_featured'] ?? 0,
            $data['is_verified'] ?? 0,
            $data['is_exclusive'] ?? 0,
            $data['status'] ?? 'active',
            $data['seo_title'] ?? null,
            $data['seo_description'] ?? null,
            $data['created_by'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update coupon
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['title', 'title_ar', 'slug', 'description', 'description_ar', 'code',
                          'discount_type', 'discount_value', 'store_id', 'category_id', 'affiliate_url',
                          'image', 'start_date', 'expiry_date', 'is_featured', 'is_verified', 
                          'is_exclusive', 'status', 'seo_title', 'seo_description'];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE coupons SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Delete coupon
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM coupons WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Increment view count
     */
    public function incrementViews($id) {
        $stmt = $this->db->prepare("UPDATE coupons SET views_count = views_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Increment used count
     */
    public function incrementUsed($id) {
        $stmt = $this->db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get related coupons
     */
    public function getRelated($couponId, $storeId, $categoryId, $limit = 4) {
        $stmt = $this->db->prepare(
            "SELECT c.*, s.name as store_name, s.slug as store_slug, s.logo as store_logo
             FROM coupons c
             LEFT JOIN stores s ON c.store_id = s.id
             WHERE c.id != ? AND c.status = 'active' AND (c.store_id = ? OR c.category_id = ?)
             ORDER BY RAND()
             LIMIT ?"
        );
        $stmt->execute([$couponId, $storeId, $categoryId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Auto-expire coupons
     */
    public function autoExpire() {
        $stmt = $this->db->prepare(
            "UPDATE coupons SET status = 'expired' 
             WHERE expiry_date < CURDATE() AND status = 'active'"
        );
        return $stmt->execute();
    }
    
    /**
     * Get statistics
     */
    public function getStats() {
        $stmt = $this->db->query(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired,
                SUM(views_count) as total_views,
                SUM(used_count) as total_used
             FROM coupons"
        );
        return $stmt->fetch();
    }
}
