<?php
/**
 * Category Model
 */

class Category {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get all categories
     */
    public function getAll($includeInactive = false) {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM coupons WHERE category_id = c.id AND status = 'active') as coupon_count,
                       (SELECT COUNT(*) FROM stores WHERE category_id = c.id AND status = 'active') as store_count
                FROM categories c";
        
        if (!$includeInactive) {
            $sql .= " WHERE c.status = 'active'";
        }
        
        $sql .= " ORDER BY c.sort_order ASC, c.name ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get parent categories only
     */
    public function getParents() {
        $stmt = $this->db->query(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM coupons WHERE category_id = c.id AND status = 'active') as coupon_count
             FROM categories c
             WHERE c.parent_id IS NULL AND c.status = 'active'
             ORDER BY c.sort_order ASC, c.name ASC"
        );
        return $stmt->fetchAll();
    }
    
    /**
     * Get child categories
     */
    public function getChildren($parentId) {
        $stmt = $this->db->prepare(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM coupons WHERE category_id = c.id AND status = 'active') as coupon_count
             FROM categories c
             WHERE c.parent_id = ? AND c.status = 'active'
             ORDER BY c.sort_order ASC, c.name ASC"
        );
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get category by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT c.*, p.name as parent_name, p.slug as parent_slug,
                    (SELECT COUNT(*) FROM coupons WHERE category_id = c.id AND status = 'active') as coupon_count,
                    (SELECT COUNT(*) FROM stores WHERE category_id = c.id AND status = 'active') as store_count
             FROM categories c
             LEFT JOIN categories p ON c.parent_id = p.id
             WHERE c.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get category by slug
     */
    public function getBySlug($slug) {
        $stmt = $this->db->prepare(
            "SELECT c.*, p.name as parent_name, p.slug as parent_slug,
                    (SELECT COUNT(*) FROM coupons WHERE category_id = c.id AND status = 'active') as coupon_count,
                    (SELECT COUNT(*) FROM stores WHERE category_id = c.id AND status = 'active') as store_count
             FROM categories c
             LEFT JOIN categories p ON c.parent_id = p.id
             WHERE c.slug = ?"
        );
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * Create category
     */
    public function create($data) {
        $sql = "INSERT INTO categories (name, name_ar, slug, description, description_ar, icon, image,
                parent_id, sort_order, status, seo_title, seo_description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['name_ar'] ?? null,
            $data['slug'],
            $data['description'] ?? null,
            $data['description_ar'] ?? null,
            $data['icon'] ?? null,
            $data['image'] ?? null,
            $data['parent_id'] ?? null,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $data['seo_title'] ?? null,
            $data['seo_description'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update category
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['name', 'name_ar', 'slug', 'description', 'description_ar', 'icon',
                          'image', 'parent_id', 'sort_order', 'status', 'seo_title', 'seo_description'];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE categories SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Delete category
     */
    public function delete($id) {
        // First, set category_id to NULL for related coupons and stores
        $this->db->prepare("UPDATE coupons SET category_id = NULL WHERE category_id = ?")->execute([$id]);
        $this->db->prepare("UPDATE stores SET category_id = NULL WHERE category_id = ?")->execute([$id]);
        $this->db->prepare("UPDATE categories SET parent_id = NULL WHERE parent_id = ?")->execute([$id]);
        
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get statistics
     */
    public function getStats() {
        $stmt = $this->db->query(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
             FROM categories"
        );
        return $stmt->fetch();
    }
}
