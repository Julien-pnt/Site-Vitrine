<?php
/**
 * Collection Model
 * Handles all database operations related to collections
 */
class Collection {
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
     * Get total number of collections (for pagination)
     * 
     * @param string $search Optional search term
     * @return int Total count
     */
    public function getTotalCount($search = '') {
        $sql = "SELECT COUNT(*) FROM collections";
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
     * Get all collections with pagination and search functionality
     * 
     * @param int $limit Number of items per page
     * @param int $offset Starting position
     * @param string $search Optional search term
     * @return array Collections
     */
    public function getAllCollections($limit = 10, $offset = 0, $search = '') {
        $sql = "SELECT * FROM collections";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " WHERE nom LIKE ? OR description LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY date_debut DESC, nom ASC";
        
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
     * Get a collection by ID
     * 
     * @param int $id Collection ID
     * @return array|false Collection data or false if not found
     */
    public function getCollectionById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM collections WHERE id = ?");
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
        $sql = "SELECT 1 FROM collections WHERE slug = ?";
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
     * Add a new collection
     * 
     * @param array $data Collection data
     * @return int|false New collection ID or false on failure
     */
    public function addCollection($data) {
        try {
            $columns = [];
            $placeholders = [];
            $values = [];
            
            foreach ($data as $column => $value) {
                $columns[] = $column;
                $placeholders[] = "?";
                $values[] = $value;
            }
            
            $sql = "INSERT INTO collections (" . implode(", ", $columns) . ") 
                    VALUES (" . implode(", ", $placeholders) . ")";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error adding collection: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing collection
     * 
     * @param int $id Collection ID
     * @param array $data Updated data
     * @return bool Success
     */
    public function updateCollection($id, $data) {
        try {
            $setStatements = [];
            $values = [];
            
            foreach ($data as $column => $value) {
                $setStatements[] = "$column = ?";
                $values[] = $value;
            }
            
            $values[] = $id;
            
            $sql = "UPDATE collections SET " . implode(", ", $setStatements) . " WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($values);
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error updating collection: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a collection
     * 
     * @param int $id Collection ID
     * @return bool Success
     */
    public function deleteCollection($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM collections WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting collection: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count products in a collection (for safe deletion)
     * 
     * @param int $id Collection ID
     * @return int Number of products
     */
    public function getProductsCount($id) {
        // Si la table produits existe avec une référence aux collections
        try {
            $sql = "SELECT COUNT(*) FROM produits WHERE collection_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            // La table produits n'existe peut-être pas encore ou n'a pas de référence collection_id
            error_log("Error counting products (table may not exist): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get active collections for display on the front-end
     * 
     * @return array Active collections
     */
    public function getActiveCollections() {
        $now = date('Y-m-d');
        
        $sql = "SELECT * FROM collections 
                WHERE active = 1 
                AND (date_debut IS NULL OR date_debut <= ?) 
                AND (date_fin IS NULL OR date_fin >= ?)
                ORDER BY date_debut DESC, nom ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$now, $now]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get upcoming collections
     * 
     * @return array Upcoming collections
     */
    public function getUpcomingCollections() {
        $now = date('Y-m-d');
        
        $sql = "SELECT * FROM collections 
                WHERE active = 1 
                AND date_debut > ? 
                ORDER BY date_debut ASC, nom ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$now]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>