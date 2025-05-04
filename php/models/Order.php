<?php
/**
 * Order Model
 * Handles all database operations related to orders
 */
class Order {
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
     * Get total number of orders (for pagination)
     * 
     * @param string $search Optional search term
     * @param string $status Optional status filter
     * @param string $dateFrom Optional start date
     * @param string $dateTo Optional end date
     * @param string $filter Optional quick filter
     * @return int Total count
     */
    public function getTotalCount($search = '', $status = '', $dateFrom = '', $dateTo = '', $filter = '') {
        $sql = "SELECT COUNT(*) FROM commandes c 
                JOIN utilisateurs u ON c.utilisateur_id = u.id";
        $params = [];
        $conditions = [];
        
        // Apply search filter
        if (!empty($search)) {
            $conditions[] = "(c.reference LIKE ? OR CONCAT(u.prenom, ' ', u.nom) LIKE ? OR u.email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        // Apply status filter
        if (!empty($status)) {
            $conditions[] = "c.statut = ?";
            $params[] = $status;
        }
        
        // Apply date range filter
        if (!empty($dateFrom)) {
            $conditions[] = "DATE(c.date_commande) >= ?";
            $params[] = $dateFrom;
        }
        
        if (!empty($dateTo)) {
            $conditions[] = "DATE(c.date_commande) <= ?";
            $params[] = $dateTo;
        }
        
        // Apply quick filters
        if (!empty($filter)) {
            switch ($filter) {
                case 'today':
                    $conditions[] = "DATE(c.date_commande) = CURDATE()";
                    break;
                case 'yesterday':
                    $conditions[] = "DATE(c.date_commande) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                    break;
                case 'this-week':
                    $conditions[] = "YEARWEEK(c.date_commande, 1) = YEARWEEK(CURDATE(), 1)";
                    break;
                case 'this-month':
                    $conditions[] = "MONTH(c.date_commande) = MONTH(CURDATE()) AND YEAR(c.date_commande) = YEAR(CURDATE())";
                    break;
                case 'delayed':
                    $conditions[] = "c.statut IN ('payee', 'en_preparation') AND DATEDIFF(CURDATE(), c.date_commande) > 3";
                    break;
            }
        }
        
        // Add conditions to SQL
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Get all orders with pagination and filtering
     * 
     * @param int $limit Number of items per page
     * @param int $offset Starting position
     * @param string $search Optional search term
     * @param string $status Optional status filter
     * @param string $dateFrom Optional start date
     * @param string $dateTo Optional end date
     * @param string $filter Optional quick filter
     * @return array Orders
     */
    public function getAllOrders($limit = 15, $offset = 0, $search = '', $status = '', $dateFrom = '', $dateTo = '', $filter = '') {
        $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) as client_nom, u.email as client_email 
                FROM commandes c 
                JOIN utilisateurs u ON c.utilisateur_id = u.id";
        
        $params = [];
        $conditions = [];
        
        // Apply search filter
        if (!empty($search)) {
            $conditions[] = "(c.reference LIKE ? OR CONCAT(u.prenom, ' ', u.nom) LIKE ? OR u.email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        // Apply status filter
        if (!empty($status)) {
            $conditions[] = "c.statut = ?";
            $params[] = $status;
        }
        
        // Apply date range filter
        if (!empty($dateFrom)) {
            $conditions[] = "DATE(c.date_commande) >= ?";
            $params[] = $dateFrom;
        }
        
        if (!empty($dateTo)) {
            $conditions[] = "DATE(c.date_commande) <= ?";
            $params[] = $dateTo;
        }
        
        // Apply quick filters
        if (!empty($filter)) {
            switch ($filter) {
                case 'today':
                    $conditions[] = "DATE(c.date_commande) = CURDATE()";
                    break;
                case 'yesterday':
                    $conditions[] = "DATE(c.date_commande) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                    break;
                case 'this-week':
                    $conditions[] = "YEARWEEK(c.date_commande, 1) = YEARWEEK(CURDATE(), 1)";
                    break;
                case 'this-month':
                    $conditions[] = "MONTH(c.date_commande) = MONTH(CURDATE()) AND YEAR(c.date_commande) = YEAR(CURDATE())";
                    break;
                case 'delayed':
                    $conditions[] = "c.statut IN ('payee', 'en_preparation') AND DATEDIFF(CURDATE(), c.date_commande) > 3";
                    break;
            }
        }
        
        // Add conditions to SQL
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Add ordering
        $sql .= " ORDER BY c.date_commande DESC";
        
        // Add pagination
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
     * Get an order by ID
     * 
     * @param int $id Order ID
     * @return array|false Order data or false if not found
     */
    public function getOrderById($id) {
        $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) as client_nom, u.email as client_email,
                u.telephone as client_telephone
                FROM commandes c 
                JOIN utilisateurs u ON c.utilisateur_id = u.id
                WHERE c.id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get an order by reference
     * 
     * @param string $reference Order reference
     * @return array|false Order data or false if not found
     */
    public function getOrderByReference($reference) {
        $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) as client_nom, u.email as client_email 
                FROM commandes c 
                JOIN utilisateurs u ON c.utilisateur_id = u.id
                WHERE c.reference = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$reference]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get order items
     * 
     * @param int $orderId Order ID
     * @return array Order items
     */
    public function getOrderItems($orderId) {
        $sql = "SELECT ac.*, p.image 
                FROM articles_commande ac
                LEFT JOIN produits p ON ac.produit_id = p.id
                WHERE ac.commande_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get complete order details with items for the detail view
     * 
     * @param int $orderId Order ID
     * @return array|false Complete order details or false if not found
     */
    public function getOrderDetails($orderId) {
        $order = $this->getOrderById($orderId);
        
        if (!$order) {
            return false;
        }
        
        $order['items'] = $this->getOrderItems($orderId);
        return $order;
    }
    
    /**
     * Update order status
     * 
     * @param int $orderId Order ID
     * @param string $status New status
     * @return bool Success
     */
    public function updateOrderStatus($orderId, $status) {
        try {
            $sql = "UPDATE commandes SET statut = ?, date_modification = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$status, $orderId]);
        } catch (PDOException $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add a note to an order
     * 
     * @param int $orderId Order ID
     * @param string $note Note text
     * @return bool Success
     */
    public function addOrderNote($orderId, $note) {
        try {
            $sql = "UPDATE commandes SET notes = CONCAT(IFNULL(notes, ''), ?, '\n'), date_modification = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $datePrefix = '[' . date('d/m/Y H:i') . '] ';
            return $stmt->execute([$datePrefix . $note, $orderId]);
        } catch (PDOException $e) {
            error_log("Error adding order note: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get order reference by ID (for logging)
     * 
     * @param int $orderId Order ID
     * @return string|null Order reference or null if not found
     */
    public function getOrderReference($orderId) {
        $stmt = $this->pdo->prepare("SELECT reference FROM commandes WHERE id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Get order statistics
     * 
     * @return array Statistics
     */
    public function getOrderStats() {
        $stats = [
            'total' => 0,
            'today' => 0,
            'week' => 0,
            'month' => 0,
            'pending' => 0,
            'processing' => 0,
            'revenue_today' => 0,
            'revenue_week' => 0,
            'revenue_month' => 0,
            'revenue_total' => 0
        ];
        
        // Total orders
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM commandes");
        $stats['total'] = (int)$stmt->fetchColumn();
        
        // Today's orders
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM commandes WHERE DATE(date_commande) = CURDATE()");
        $stats['today'] = (int)$stmt->fetchColumn();
        
        // This week's orders
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM commandes WHERE YEARWEEK(date_commande, 1) = YEARWEEK(CURDATE(), 1)");
        $stats['week'] = (int)$stmt->fetchColumn();
        
        // This month's orders
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM commandes WHERE MONTH(date_commande) = MONTH(CURDATE()) AND YEAR(date_commande) = YEAR(CURDATE())");
        $stats['month'] = (int)$stmt->fetchColumn();
        
        // Pending orders
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM commandes WHERE statut = 'en_attente'");
        $stats['pending'] = (int)$stmt->fetchColumn();
        
        // Processing orders
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM commandes WHERE statut IN ('payee', 'en_preparation')");
        $stats['processing'] = (int)$stmt->fetchColumn();
        
        // Revenue today
        $stmt = $this->pdo->query("SELECT SUM(total) FROM commandes WHERE DATE(date_commande) = CURDATE() AND statut NOT IN ('annulee', 'remboursee')");
        $stats['revenue_today'] = (float)$stmt->fetchColumn() ?: 0;
        
        // Revenue this week
        $stmt = $this->pdo->query("SELECT SUM(total) FROM commandes WHERE YEARWEEK(date_commande, 1) = YEARWEEK(CURDATE(), 1) AND statut NOT IN ('annulee', 'remboursee')");
        $stats['revenue_week'] = (float)$stmt->fetchColumn() ?: 0;
        
        // Revenue this month
        $stmt = $this->pdo->query("SELECT SUM(total) FROM commandes WHERE MONTH(date_commande) = MONTH(CURDATE()) AND YEAR(date_commande) = YEAR(CURDATE()) AND statut NOT IN ('annulee', 'remboursee')");
        $stats['revenue_month'] = (float)$stmt->fetchColumn() ?: 0;
        
        // Total revenue
        $stmt = $this->pdo->query("SELECT SUM(total) FROM commandes WHERE statut NOT IN ('annulee', 'remboursee')");
        $stats['revenue_total'] = (float)$stmt->fetchColumn() ?: 0;
        
        return $stats;
    }
    
    /**
     * Get monthly revenue for last 12 months (for charts)
     * 
     * @return array Monthly revenue data
     */
    public function getMonthlyRevenue() {
        $sql = "SELECT 
                    DATE_FORMAT(date_commande, '%Y-%m') as month,
                    SUM(total) as revenue,
                    COUNT(*) as orders
                FROM commandes
                WHERE 
                    date_commande >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    AND statut NOT IN ('annulee', 'remboursee')
                GROUP BY DATE_FORMAT(date_commande, '%Y-%m')
                ORDER BY month ASC";
                
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get the status distribution of orders (for charts)
     * 
     * @return array Status distribution data
     */
    public function getStatusDistribution() {
        $sql = "SELECT 
                    statut,
                    COUNT(*) as count
                FROM commandes
                GROUP BY statut";
                
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>