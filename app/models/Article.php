<?php
/**
 * Article Model
 */

class Article {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get all articles with pagination
     */
    public function getAll($page = 1, $perPage = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['category_id'])) {
            $where[] = "a.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['author_id'])) {
            $where[] = "a.author_id = ?";
            $params[] = $filters['author_id'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(a.title LIKE ? OR a.content LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT a.*, u.username as author_name, u.full_name as author_full_name,
                       c.name as category_name, c.slug as category_slug
                FROM articles a
                LEFT JOIN users u ON a.author_id = u.id
                LEFT JOIN categories c ON a.category_id = c.id
                WHERE {$whereClause}
                ORDER BY a.is_featured DESC, a.publish_date DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get published articles for frontend
     */
    public function getPublished($page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare(
            "SELECT a.*, u.username as author_name, u.full_name as author_full_name,
                    c.name as category_name, c.slug as category_slug
             FROM articles a
             LEFT JOIN users u ON a.author_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.status = 'published' AND a.publish_date <= NOW()
             ORDER BY a.is_featured DESC, a.publish_date DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$perPage, $offset]);
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
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM articles WHERE {$whereClause}");
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Get published count
     */
    public function getPublishedCount() {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM articles WHERE status = 'published' AND publish_date <= NOW()"
        );
        return $stmt->fetchColumn();
    }
    
    /**
     * Get featured articles
     */
    public function getFeatured($limit = 5) {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.full_name as author_full_name, c.name as category_name, c.slug as category_slug
             FROM articles a
             LEFT JOIN users u ON a.author_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.status = 'published' AND a.is_featured = 1 AND a.publish_date <= NOW()
             ORDER BY a.publish_date DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get latest articles
     */
    public function getLatest($limit = 6) {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.full_name as author_full_name, c.name as category_name, c.slug as category_slug
             FROM articles a
             LEFT JOIN users u ON a.author_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.status = 'published' AND a.publish_date <= NOW()
             ORDER BY a.publish_date DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get article by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.username as author_name, u.full_name as author_full_name,
                    c.name as category_name, c.slug as category_slug
             FROM articles a
             LEFT JOIN users u ON a.author_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get article by slug
     */
    public function getBySlug($slug) {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.username as author_name, u.full_name as author_full_name,
                    c.name as category_name, c.slug as category_slug
             FROM articles a
             LEFT JOIN users u ON a.author_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.slug = ? AND a.status = 'published' AND a.publish_date <= NOW()"
        );
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * Get articles by category
     */
    public function getByCategory($categoryId, $page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare(
            "SELECT a.*, u.full_name as author_full_name, c.name as category_name, c.slug as category_slug
             FROM articles a
             LEFT JOIN users u ON a.author_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.category_id = ? AND a.status = 'published' AND a.publish_date <= NOW()
             ORDER BY a.publish_date DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$categoryId, $perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Search articles
     */
    public function search($query, $limit = 20) {
        $searchTerm = '%' . $query . '%';
        
        $stmt = $this->db->prepare(
            "SELECT a.*, u.full_name as author_full_name, c.name as category_name, c.slug as category_slug
             FROM articles a
             LEFT JOIN users u ON a.author_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.status = 'published' AND a.publish_date <= NOW()
                   AND (a.title LIKE ? OR a.content LIKE ? OR a.excerpt LIKE ?)
             ORDER BY a.publish_date DESC
             LIMIT ?"
        );
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Create article
     */
    public function create($data) {
        $sql = "INSERT INTO articles (title, title_ar, slug, excerpt, excerpt_ar, content, content_ar,
                cover_image, category_id, author_id, status, publish_date, is_featured,
                seo_title, seo_description, seo_keywords)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['title'],
            $data['title_ar'] ?? null,
            $data['slug'],
            $data['excerpt'] ?? null,
            $data['excerpt_ar'] ?? null,
            $data['content'],
            $data['content_ar'] ?? null,
            $data['cover_image'] ?? null,
            $data['category_id'] ?? null,
            $data['author_id'],
            $data['status'] ?? 'draft',
            $data['publish_date'] ?? date('Y-m-d H:i:s'),
            $data['is_featured'] ?? 0,
            $data['seo_title'] ?? null,
            $data['seo_description'] ?? null,
            $data['seo_keywords'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update article
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['title', 'title_ar', 'slug', 'excerpt', 'excerpt_ar', 'content', 'content_ar',
                          'cover_image', 'category_id', 'status', 'publish_date', 'is_featured',
                          'seo_title', 'seo_description', 'seo_keywords'];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE articles SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Delete article
     */
    public function delete($id) {
        // Delete tag relations
        $this->db->prepare("DELETE FROM article_tag_relations WHERE article_id = ?")->execute([$id]);
        
        $stmt = $this->db->prepare("DELETE FROM articles WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Increment view count
     */
    public function incrementViews($id) {
        $stmt = $this->db->prepare("UPDATE articles SET views_count = views_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get article tags
     */
    public function getTags($articleId) {
        $stmt = $this->db->prepare(
            "SELECT t.* FROM article_tags t
             INNER JOIN article_tag_relations r ON t.id = r.tag_id
             WHERE r.article_id = ?"
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Set article tags
     */
    public function setTags($articleId, $tagIds) {
        // Remove existing tags
        $this->db->prepare("DELETE FROM article_tag_relations WHERE article_id = ?")->execute([$articleId]);
        
        // Add new tags
        if (!empty($tagIds)) {
            $stmt = $this->db->prepare("INSERT INTO article_tag_relations (article_id, tag_id) VALUES (?, ?)");
            foreach ($tagIds as $tagId) {
                $stmt->execute([$articleId, $tagId]);
            }
        }
    }
    
    /**
     * Get related articles
     */
    public function getRelated($articleId, $categoryId, $limit = 4) {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.full_name as author_full_name
             FROM articles a
             LEFT JOIN users u ON a.author_id = u.id
             WHERE a.id != ? AND a.category_id = ? AND a.status = 'published' AND a.publish_date <= NOW()
             ORDER BY a.publish_date DESC
             LIMIT ?"
        );
        $stmt->execute([$articleId, $categoryId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Publish scheduled articles
     */
    public function publishScheduled() {
        $stmt = $this->db->prepare(
            "UPDATE articles SET status = 'published' 
             WHERE status = 'scheduled' AND publish_date <= NOW()"
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
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
                SUM(views_count) as total_views
             FROM articles"
        );
        return $stmt->fetch();
    }
}
