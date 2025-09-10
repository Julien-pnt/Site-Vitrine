<?php
/**
 * SYSTÈME DE PROTECTION CONTRE LA FORCE BRUTE
 * Protège les tentatives de connexion répétées
 */

class BruteForceProtection {
    private $pdo;
    private $maxAttempts;
    private $lockoutTime;
    
    public function __construct($pdo, $maxAttempts = 5, $lockoutTime = 900) {
        $this->pdo = $pdo;
        $this->maxAttempts = $maxAttempts;
        $this->lockoutTime = $lockoutTime; // 15 minutes par défaut
        $this->createTableIfNotExists();
    }
    
    /**
     * Crée la table des tentatives de connexion si elle n'existe pas
     */
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            email VARCHAR(255),
            attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            success BOOLEAN DEFAULT FALSE,
            user_agent TEXT,
            INDEX idx_ip_time (ip_address, attempt_time),
            INDEX idx_email_time (email, attempt_time)
        ) ENGINE=InnoDB";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erreur création table login_attempts: " . $e->getMessage());
        }
    }
    
    /**
     * Vérifie si une IP est bloquée
     */
    public function isBlocked($ipAddress, $email = null) {
        $now = time();
        $cutoffTime = date('Y-m-d H:i:s', $now - $this->lockoutTime);
        
        // Vérifier les tentatives par IP
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as attempts 
            FROM login_attempts 
            WHERE ip_address = ? 
            AND attempt_time > ? 
            AND success = FALSE
        ");
        $stmt->execute([$ipAddress, $cutoffTime]);
        $ipAttempts = $stmt->fetchColumn();
        
        if ($ipAttempts >= $this->maxAttempts) {
            $this->logSecurityEvent('IP_BLOCKED', $ipAddress, $email);
            return true;
        }
        
        // Vérifier les tentatives par email si fourni
        if ($email) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as attempts 
                FROM login_attempts 
                WHERE email = ? 
                AND attempt_time > ? 
                AND success = FALSE
            ");
            $stmt->execute([$email, $cutoffTime]);
            $emailAttempts = $stmt->fetchColumn();
            
            if ($emailAttempts >= $this->maxAttempts) {
                $this->logSecurityEvent('EMAIL_BLOCKED', $ipAddress, $email);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Enregistre une tentative de connexion
     */
    public function recordAttempt($ipAddress, $email, $success = false) {
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO login_attempts (ip_address, email, success, user_agent) 
            VALUES (?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([$ipAddress, $email, $success, $userAgent]);
        
        if ($success) {
            // Si connexion réussie, nettoyer les anciennes tentatives
            $this->cleanOldAttempts($ipAddress, $email);
        } else {
            // Log de la tentative échouée
            $this->logSecurityEvent('FAILED_LOGIN', $ipAddress, $email);
        }
        
        return $result;
    }
    
    /**
     * Nettoie les anciennes tentatives après une connexion réussie
     */
    private function cleanOldAttempts($ipAddress, $email) {
        // Supprimer les tentatives échouées pour cette IP/email
        $stmt = $this->pdo->prepare("
            DELETE FROM login_attempts 
            WHERE (ip_address = ? OR email = ?) 
            AND success = FALSE
        ");
        $stmt->execute([$ipAddress, $email]);
    }
    
    /**
     * Obtient le temps restant avant déblocage
     */
    public function getTimeUntilUnblock($ipAddress, $email = null) {
        $cutoffTime = date('Y-m-d H:i:s', time() - $this->lockoutTime);
        
        $sql = "SELECT MAX(attempt_time) as last_attempt 
                FROM login_attempts 
                WHERE success = FALSE 
                AND attempt_time > ? 
                AND (ip_address = ?";
        
        $params = [$cutoffTime, $ipAddress];
        
        if ($email) {
            $sql .= " OR email = ?";
            $params[] = $email;
        }
        
        $sql .= ")";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['last_attempt']) {
            $lastAttempt = strtotime($result['last_attempt']);
            $unlockTime = $lastAttempt + $this->lockoutTime;
            $remaining = $unlockTime - time();
            
            return max(0, $remaining);
        }
        
        return 0;
    }
    
    /**
     * Nettoie les anciennes tentatives (plus de 24h)
     */
    public function cleanupOldAttempts() {
        $cutoffTime = date('Y-m-d H:i:s', time() - (24 * 60 * 60));
        
        $stmt = $this->pdo->prepare("
            DELETE FROM login_attempts 
            WHERE attempt_time < ?
        ");
        
        return $stmt->execute([$cutoffTime]);
    }
    
    /**
     * Obtient les statistiques d'attaques
     */
    public function getAttackStats($hours = 24) {
        $cutoffTime = date('Y-m-d H:i:s', time() - ($hours * 60 * 60));
        
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_attempts,
                COUNT(DISTINCT ip_address) as unique_ips,
                COUNT(DISTINCT email) as unique_emails,
                SUM(CASE WHEN success = FALSE THEN 1 ELSE 0 END) as failed_attempts
            FROM login_attempts 
            WHERE attempt_time > ?
        ");
        
        $stmt->execute([$cutoffTime]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtient la liste des IPs suspectes
     */
    public function getSuspiciousIPs($limit = 10) {
        $cutoffTime = date('Y-m-d H:i:s', time() - (60 * 60)); // Dernière heure
        
        $stmt = $this->pdo->prepare("
            SELECT 
                ip_address,
                COUNT(*) as attempts,
                MAX(attempt_time) as last_attempt,
                COUNT(DISTINCT email) as different_emails
            FROM login_attempts 
            WHERE attempt_time > ? 
            AND success = FALSE
            GROUP BY ip_address 
            HAVING attempts >= ?
            ORDER BY attempts DESC, last_attempt DESC
            LIMIT ?
        ");
        
        $stmt->execute([$cutoffTime, $this->maxAttempts / 2, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Log des événements de sécurité
     */
    private function logSecurityEvent($event, $ipAddress, $email = null) {
        if (function_exists('secureLog')) {
            secureLog('security', "Brute force protection: $event", [
                'ip' => $ipAddress,
                'email' => $email,
                'max_attempts' => $this->maxAttempts,
                'lockout_time' => $this->lockoutTime
            ]);
        }
    }
    
    /**
     * Vérifie si une IP est dans une whitelist
     */
    public function isWhitelisted($ipAddress) {
        $whitelist = [
            '127.0.0.1',
            '::1',
            // Ajoutez ici vos IPs de confiance
        ];
        
        return in_array($ipAddress, $whitelist);
    }
    
    /**
     * Bloque temporairement une IP spécifique
     */
    public function blockIP($ipAddress, $reason = 'Manual block') {
        // Ajouter plusieurs tentatives fictives pour déclencher le blocage
        for ($i = 0; $i < $this->maxAttempts; $i++) {
            $this->recordAttempt($ipAddress, 'blocked@system.local', false);
        }
        
        $this->logSecurityEvent('IP_MANUALLY_BLOCKED', $ipAddress, $reason);
    }
    
    /**
     * Débloquer une IP
     */
    public function unblockIP($ipAddress) {
        $stmt = $this->pdo->prepare("
            DELETE FROM login_attempts 
            WHERE ip_address = ? AND success = FALSE
        ");
        
        $result = $stmt->execute([$ipAddress]);
        $this->logSecurityEvent('IP_UNBLOCKED', $ipAddress);
        
        return $result;
    }
}

/**
 * Fonction utilitaire pour utiliser la protection facilement
 */
function checkBruteForceProtection($email = null) {
    global $pdo;
    
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $bruteForce = new BruteForceProtection($pdo);
    
    // Vérifier si l'IP est en whitelist
    if ($bruteForce->isWhitelisted($ipAddress)) {
        return ['allowed' => true, 'message' => ''];
    }
    
    // Vérifier si bloqué
    if ($bruteForce->isBlocked($ipAddress, $email)) {
        $timeRemaining = $bruteForce->getTimeUntilUnblock($ipAddress, $email);
        $minutes = ceil($timeRemaining / 60);
        
        return [
            'allowed' => false,
            'message' => "Trop de tentatives de connexion. Réessayez dans $minutes minute(s).",
            'time_remaining' => $timeRemaining
        ];
    }
    
    return ['allowed' => true, 'message' => '', 'protection' => $bruteForce];
}
