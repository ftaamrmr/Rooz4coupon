<?php
/**
 * User Model
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get all users
     */
    public function getAll($filters = []) {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }
        
        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT id, username, email, role, full_name, avatar, status, last_login, created_at
                FROM users WHERE {$whereClause} ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT id, username, email, role, full_name, avatar, status, last_login, created_at
             FROM users WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get user by username
     */
    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Authenticate user
     */
    public function authenticate($username, $password) {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'"
        );
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Create user
     */
    public function create($data) {
        $sql = "INSERT INTO users (username, email, password, role, full_name, avatar, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['username'],
            $data['email'],
            hashPassword($data['password']),
            $data['role'] ?? 'writer',
            $data['full_name'] ?? null,
            $data['avatar'] ?? null,
            $data['status'] ?? 'active'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['username', 'email', 'role', 'full_name', 'avatar', 'status'];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Update password
     */
    public function updatePassword($id, $newPassword) {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([hashPassword($newPassword), $id]);
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Get user statistics
     */
    public function getStats() {
        $stmt = $this->db->query(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN role = 'editor' THEN 1 ELSE 0 END) as editors,
                SUM(CASE WHEN role = 'writer' THEN 1 ELSE 0 END) as writers,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
             FROM users"
        );
        return $stmt->fetch();
    }
}
