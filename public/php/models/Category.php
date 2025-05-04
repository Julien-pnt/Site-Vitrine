<?php
/**
 * Category Model
 * Handles all database operations related to categories
 */
class Category {
    private $pdo;
    
    /**
     * Constructor
     * 
     * @param PDO $pdo Database connection
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get total number of categories (for pagination)
     * 
     * @param string $search Optional search term
     * @return int Total count
     */
    public function getTotalCount($search = '') {
        $sql = "SELECT COUNT(*) FROM categories";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " WHERE nom LIKE ? OR description LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Get all categories with pagination and search functionality
     * 
     * @param int $limit Number of items per page
     * @param int $offset Starting position
     * @param string $search Optional search term
     * @return array Categories
     */
    public function getAllCategories($limit = 10, $offset = 0, $search = '') {
        $sql = "SELECT c.*, p.nom as parent_nom 
                FROM categories c 
                LEFT JOIN categories p ON c.parent_id = p.id";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " WHERE c.nom LIKE ? OR c.description LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY c.position ASC, c.nom ASC";
        
        if ($limit > 0) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all parent categories (for dropdown)
     * 
     * @return array Parent categories
     */
    public function getParentCategories() {
        $stmt = $this->pdo->prepare("SELECT id, nom FROM categories ORDER BY position ASC, nom ASC");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a category by ID
     * 
     * @param int $id Category ID
     * @return array|false Category data or false if not found
     */
    public function getCategoryById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if a slug exists (for validation)
     * 
     * @param string $slug Slug to check
     * @param int|null $excludeId Exclude this ID from check (for updates)
     * @return bool True if exists
     */
    public function slugExists($slug, $excludeId = null) {
        $sql = "SELECT 1 FROM categories WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return (bool)$stmt->fetchColumn();
    }
    
    /**
     * Add a new category
     * 
     * @param array $data Category data
     * @return int|false New category ID or false on failure
     */
    public function addCategory($data) {
        try {
            $columns = [];
            $placeholders = [];
            $values = [];
            
            foreach ($data as $column => $value) {
                $columns[] = $column;
                $placeholders[] = "?";
                $values[] = $value;
            }
            
            $sql = "INSERT INTO categories (" . implode(", ", $columns) . ") 
                    VALUES (" . implode(", ", $placeholders) . ")";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error adding category: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing category
     * 
     * @param int $id Category ID
     * @param array $data Updated data
     * @return bool Success
     */
    public function updateCategory($id, $data) {
        try {
            $setStatements = [];
            $values = [];
            
            foreach ($data as $column => $value) {
                $setStatements[] = "$column = ?";
                $values[] = $value;
            }
            
            $values[] = $id;
            
            $sql = "UPDATE categories SET " . implode(", ", $setStatements) . " WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($values);
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a category
     * 
     * @param int $id Category ID
     * @return bool Success
     */
    public function deleteCategory($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count products in a category (for safe deletion)
     * 
     * @param int $id Category ID
     * @return int Number of products
     */
    public function getProductsCount($id) {
        // If you have a products table with category_id field, use this
        // Otherwise modify to match your database structure
        $sql = "SELECT COUNT(*) FROM produits WHERE categorie_id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            // Handle case where products table might not exist yet
            error_log("Error counting products (table may not exist): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Count subcategories in a category (for safe deletion)
     * 
     * @param int $id Category ID
     * @return int Number of subcategories
     */
    public function getSubcategoriesCount($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }
}
?>