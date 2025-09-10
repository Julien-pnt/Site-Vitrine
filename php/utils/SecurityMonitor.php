<?php
/**
 * SYSTÈME DE MONITORING DE SÉCURITÉ
 * Surveillance en temps réel des menaces de sécurité
 */

class SecurityMonitor {
    
    private $pdo;
    private $alertThresholds;
    private $alertHandlers = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->initializeAlertThresholds();
        $this->createMonitoringTables();
    }
    
    /**
     * Initialise les seuils d'alerte
     */
    private function initializeAlertThresholds() {
        $this->alertThresholds = [
            'failed_logins_per_hour' => 10,
            'failed_logins_per_ip_per_hour' => 5,
            'suspicious_requests_per_hour' => 20,
            'blocked_requests_per_hour' => 30,
            'different_user_agents_per_ip' => 10,
            'requests_per_minute_per_ip' => 30,
            'admin_actions_per_hour' => 50
        ];
    }
    
    /**
     * Crée les tables de monitoring
     */
    private function createMonitoringTables() {
        $tables = [
            'security_events' => "
                CREATE TABLE IF NOT EXISTS security_events (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    event_type VARCHAR(50) NOT NULL,
                    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
                    ip_address VARCHAR(45),
                    user_id INT NULL,
                    event_data JSON,
                    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    processed BOOLEAN DEFAULT FALSE,
                    INDEX idx_type_time (event_type, timestamp),
                    INDEX idx_ip_time (ip_address, timestamp),
                    INDEX idx_severity (severity, processed)
                ) ENGINE=InnoDB
            ",
            'security_alerts' => "
                CREATE TABLE IF NOT EXISTS security_alerts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    alert_type VARCHAR(50) NOT NULL,
                    message TEXT,
                    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
                    triggered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    resolved_at TIMESTAMP NULL,
                    resolved_by INT NULL,
                    alert_data JSON,
                    auto_resolved BOOLEAN DEFAULT FALSE,
                    INDEX idx_type_time (alert_type, triggered_at),
                    INDEX idx_severity (severity, resolved_at)
                ) ENGINE=InnoDB
            ",
            'ip_reputation' => "
                CREATE TABLE IF NOT EXISTS ip_reputation (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    ip_address VARCHAR(45) UNIQUE,
                    reputation_score INT DEFAULT 100,
                    total_requests INT DEFAULT 0,
                    failed_requests INT DEFAULT 0,
                    blocked_requests INT DEFAULT 0,
                    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    is_blacklisted BOOLEAN DEFAULT FALSE,
                    blacklist_reason TEXT NULL,
                    whitelisted BOOLEAN DEFAULT FALSE,
                    INDEX idx_reputation (reputation_score),
                    INDEX idx_blacklisted (is_blacklisted, last_seen)
                ) ENGINE=InnoDB
            "
        ];
        
        foreach ($tables as $name => $sql) {
            try {
                $this->pdo->exec($sql);
            } catch (PDOException $e) {
                error_log("Erreur création table $name: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Enregistre un événement de sécurité
     */
    public function logEvent($eventType, $severity, $ipAddress, $userId = null, $eventData = []) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO security_events 
                (event_type, severity, ip_address, user_id, event_data) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $eventType,
                $severity,
                $ipAddress,
                $userId,
                json_encode($eventData, JSON_UNESCAPED_UNICODE)
            ]);
            
            // Vérifier si cela déclenche une alerte
            $this->checkAlerts($eventType, $ipAddress, $userId);
            
            // Mettre à jour la réputation IP
            $this->updateIPReputation($ipAddress, $eventType);
            
        } catch (PDOException $e) {
            error_log("Erreur log security event: " . $e->getMessage());
        }
    }
    
    /**
     * Vérifie si des alertes doivent être déclenchées
     */
    private function checkAlerts($eventType, $ipAddress, $userId) {
        $now = time();
        $oneHour = $now - 3600;
        $oneMinute = $now - 60;
        
        // Vérifier les différents types d'alertes
        $this->checkFailedLoginAlerts($ipAddress, $oneHour);
        $this->checkSuspiciousRequestAlerts($ipAddress, $oneHour);
        $this->checkRateLimitAlerts($ipAddress, $oneMinute);
        $this->checkUserAgentAlerts($ipAddress, $oneHour);
        
        if ($userId) {
            $this->checkAdminActionAlerts($userId, $oneHour);
        }
    }
    
    /**
     * Vérifie les alertes de tentatives de connexion
     */
    private function checkFailedLoginAlerts($ipAddress, $oneHour) {
        // Alertes globales
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM security_events 
            WHERE event_type = 'failed_login' 
            AND timestamp > FROM_UNIXTIME(?)
        ");
        $stmt->execute([$oneHour]);
        $totalFailed = $stmt->fetchColumn();
        
        if ($totalFailed >= $this->alertThresholds['failed_logins_per_hour']) {
            $this->triggerAlert('mass_failed_logins', 'high', [
                'count' => $totalFailed,
                'threshold' => $this->alertThresholds['failed_logins_per_hour']
            ]);
        }
        
        // Alertes par IP
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM security_events 
            WHERE event_type = 'failed_login' 
            AND ip_address = ?
            AND timestamp > FROM_UNIXTIME(?)
        ");
        $stmt->execute([$ipAddress, $oneHour]);
        $ipFailed = $stmt->fetchColumn();
        
        if ($ipFailed >= $this->alertThresholds['failed_logins_per_ip_per_hour']) {
            $this->triggerAlert('ip_brute_force', 'high', [
                'ip' => $ipAddress,
                'count' => $ipFailed,
                'threshold' => $this->alertThresholds['failed_logins_per_ip_per_hour']
            ]);
        }
    }
    
    /**
     * Vérifie les alertes de requêtes suspectes
     */
    private function checkSuspiciousRequestAlerts($ipAddress, $oneHour) {
        $suspiciousEvents = [
            'xss_attempt', 'sql_injection', 'directory_traversal', 
            'code_injection', 'request_blocked'
        ];
        
        $placeholders = str_repeat('?,', count($suspiciousEvents) - 1) . '?';
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM security_events 
            WHERE event_type IN ($placeholders)
            AND ip_address = ?
            AND timestamp > FROM_UNIXTIME(?)
        ");
        
        $params = array_merge($suspiciousEvents, [$ipAddress, $oneHour]);
        $stmt->execute($params);
        $suspiciousCount = $stmt->fetchColumn();
        
        if ($suspiciousCount >= $this->alertThresholds['suspicious_requests_per_hour']) {
            $this->triggerAlert('suspicious_activity', 'high', [
                'ip' => $ipAddress,
                'count' => $suspiciousCount,
                'threshold' => $this->alertThresholds['suspicious_requests_per_hour']
            ]);
        }
    }
    
    /**
     * Vérifie les alertes de limitation de taux
     */
    private function checkRateLimitAlerts($ipAddress, $oneMinute) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM security_events 
            WHERE ip_address = ?
            AND timestamp > FROM_UNIXTIME(?)
        ");
        $stmt->execute([$ipAddress, $oneMinute]);
        $requestCount = $stmt->fetchColumn();
        
        if ($requestCount >= $this->alertThresholds['requests_per_minute_per_ip']) {
            $this->triggerAlert('rate_limit_exceeded', 'medium', [
                'ip' => $ipAddress,
                'count' => $requestCount,
                'threshold' => $this->alertThresholds['requests_per_minute_per_ip']
            ]);
        }
    }
    
    /**
     * Vérifie les alertes d'User-Agent suspects
     */
    private function checkUserAgentAlerts($ipAddress, $oneHour) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT JSON_EXTRACT(event_data, '$.user_agent')) 
            FROM security_events 
            WHERE ip_address = ?
            AND timestamp > FROM_UNIXTIME(?)
            AND JSON_EXTRACT(event_data, '$.user_agent') IS NOT NULL
        ");
        $stmt->execute([$ipAddress, $oneHour]);
        $userAgentCount = $stmt->fetchColumn();
        
        if ($userAgentCount >= $this->alertThresholds['different_user_agents_per_ip']) {
            $this->triggerAlert('multiple_user_agents', 'medium', [
                'ip' => $ipAddress,
                'count' => $userAgentCount,
                'threshold' => $this->alertThresholds['different_user_agents_per_ip']
            ]);
        }
    }
    
    /**
     * Vérifie les alertes d'actions administratives
     */
    private function checkAdminActionAlerts($userId, $oneHour) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM security_events 
            WHERE user_id = ?
            AND event_type LIKE 'admin_%'
            AND timestamp > FROM_UNIXTIME(?)
        ");
        $stmt->execute([$userId, $oneHour]);
        $adminActions = $stmt->fetchColumn();
        
        if ($adminActions >= $this->alertThresholds['admin_actions_per_hour']) {
            $this->triggerAlert('excessive_admin_activity', 'medium', [
                'user_id' => $userId,
                'count' => $adminActions,
                'threshold' => $this->alertThresholds['admin_actions_per_hour']
            ]);
        }
    }
    
    /**
     * Déclenche une alerte
     */
    private function triggerAlert($alertType, $severity, $alertData = []) {
        try {
            // Vérifier si une alerte similaire existe déjà (non résolue)
            $stmt = $this->pdo->prepare("
                SELECT id FROM security_alerts 
                WHERE alert_type = ? 
                AND resolved_at IS NULL 
                AND triggered_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([$alertType]);
            
            if ($stmt->fetchColumn()) {
                return; // Alerte déjà active
            }
            
            // Créer la nouvelle alerte
            $message = $this->generateAlertMessage($alertType, $alertData);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO security_alerts 
                (alert_type, message, severity, alert_data) 
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $alertType,
                $message,
                $severity,
                json_encode($alertData, JSON_UNESCAPED_UNICODE)
            ]);
            
            $alertId = $this->pdo->lastInsertId();
            
            // Exécuter les handlers d'alerte
            $this->executeAlertHandlers($alertType, $severity, $alertData, $alertId);
            
        } catch (PDOException $e) {
            error_log("Erreur trigger alert: " . $e->getMessage());
        }
    }
    
    /**
     * Génère un message d'alerte
     */
    private function generateAlertMessage($alertType, $alertData) {
        $messages = [
            'mass_failed_logins' => "Pic de tentatives de connexion échouées détecté: {$alertData['count']} tentatives en 1 heure",
            'ip_brute_force' => "Attaque par force brute détectée depuis {$alertData['ip']}: {$alertData['count']} tentatives",
            'suspicious_activity' => "Activité suspecte détectée depuis {$alertData['ip']}: {$alertData['count']} requêtes suspectes",
            'rate_limit_exceeded' => "Limite de taux dépassée par {$alertData['ip']}: {$alertData['count']} requêtes/minute",
            'multiple_user_agents' => "IP {$alertData['ip']} utilise {$alertData['count']} User-Agents différents",
            'excessive_admin_activity' => "Activité administrative excessive pour l'utilisateur {$alertData['user_id']}: {$alertData['count']} actions"
        ];
        
        return $messages[$alertType] ?? "Alerte de sécurité: $alertType";
    }
    
    /**
     * Met à jour la réputation d'une IP
     */
    private function updateIPReputation($ipAddress, $eventType) {
        try {
            // Insérer ou mettre à jour la réputation
            $stmt = $this->pdo->prepare("
                INSERT INTO ip_reputation (ip_address, total_requests) 
                VALUES (?, 1)
                ON DUPLICATE KEY UPDATE 
                total_requests = total_requests + 1,
                last_seen = CURRENT_TIMESTAMP
            ");
            $stmt->execute([$ipAddress]);
            
            // Ajuster le score selon le type d'événement
            $scoreChange = $this->getReputationScoreChange($eventType);
            
            if ($scoreChange !== 0) {
                $stmt = $this->pdo->prepare("
                    UPDATE ip_reputation 
                    SET reputation_score = GREATEST(0, LEAST(100, reputation_score + ?)),
                        failed_requests = failed_requests + IF(? < 0, 1, 0),
                        blocked_requests = blocked_requests + IF(? = 'request_blocked', 1, 0)
                    WHERE ip_address = ?
                ");
                $stmt->execute([$scoreChange, $scoreChange, $eventType, $ipAddress]);
            }
            
            // Blacklister automatiquement si le score est trop bas
            $stmt = $this->pdo->prepare("
                SELECT reputation_score FROM ip_reputation WHERE ip_address = ?
            ");
            $stmt->execute([$ipAddress]);
            $score = $stmt->fetchColumn();
            
            if ($score !== false && $score <= 20) {
                $this->blacklistIP($ipAddress, "Score de réputation trop bas: $score");
            }
            
        } catch (PDOException $e) {
            error_log("Erreur update IP reputation: " . $e->getMessage());
        }
    }
    
    /**
     * Calcule le changement de score de réputation
     */
    private function getReputationScoreChange($eventType) {
        $scores = [
            'failed_login' => -2,
            'xss_attempt' => -10,
            'sql_injection' => -15,
            'directory_traversal' => -10,
            'code_injection' => -20,
            'request_blocked' => -5,
            'suspicious_activity' => -3,
            'successful_login' => +1,
            'normal_request' => 0
        ];
        
        return $scores[$eventType] ?? 0;
    }
    
    /**
     * Blackliste une IP
     */
    public function blacklistIP($ipAddress, $reason = '') {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE ip_reputation 
                SET is_blacklisted = TRUE, blacklist_reason = ?
                WHERE ip_address = ?
            ");
            $stmt->execute([$reason, $ipAddress]);
            
            $this->triggerAlert('ip_blacklisted', 'high', [
                'ip' => $ipAddress,
                'reason' => $reason
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur blacklist IP: " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un handler d'alerte
     */
    public function addAlertHandler($alertType, $handler) {
        if (!isset($this->alertHandlers[$alertType])) {
            $this->alertHandlers[$alertType] = [];
        }
        $this->alertHandlers[$alertType][] = $handler;
    }
    
    /**
     * Exécute les handlers d'alerte
     */
    private function executeAlertHandlers($alertType, $severity, $alertData, $alertId) {
        // Handlers génériques (pour tous les types)
        if (isset($this->alertHandlers['*'])) {
            foreach ($this->alertHandlers['*'] as $handler) {
                $this->executeHandler($handler, $alertType, $severity, $alertData, $alertId);
            }
        }
        
        // Handlers spécifiques
        if (isset($this->alertHandlers[$alertType])) {
            foreach ($this->alertHandlers[$alertType] as $handler) {
                $this->executeHandler($handler, $alertType, $severity, $alertData, $alertId);
            }
        }
    }
    
    /**
     * Exécute un handler individuel
     */
    private function executeHandler($handler, $alertType, $severity, $alertData, $alertId) {
        try {
            if (is_callable($handler)) {
                $handler($alertType, $severity, $alertData, $alertId);
            }
        } catch (Exception $e) {
            error_log("Erreur handler alerte: " . $e->getMessage());
        }
    }
    
    /**
     * Obtient les alertes actives
     */
    public function getActiveAlerts($limit = 50) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM security_alerts 
                WHERE resolved_at IS NULL 
                ORDER BY triggered_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur get active alerts: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Résout une alerte
     */
    public function resolveAlert($alertId, $resolvedBy = null) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE security_alerts 
                SET resolved_at = CURRENT_TIMESTAMP, resolved_by = ?
                WHERE id = ?
            ");
            return $stmt->execute([$resolvedBy, $alertId]);
        } catch (PDOException $e) {
            error_log("Erreur resolve alert: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtient les statistiques de sécurité
     */
    public function getSecurityStats($hours = 24) {
        try {
            $cutoff = date('Y-m-d H:i:s', time() - ($hours * 3600));
            
            $stmt = $this->pdo->prepare("
                SELECT 
                    event_type,
                    severity,
                    COUNT(*) as count
                FROM security_events 
                WHERE timestamp > ?
                GROUP BY event_type, severity
                ORDER BY count DESC
            ");
            $stmt->execute([$cutoff]);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as active_alerts
                FROM security_alerts 
                WHERE resolved_at IS NULL
            ");
            $stmt->execute();
            $activeAlerts = $stmt->fetchColumn();
            
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as blacklisted_ips
                FROM ip_reputation 
                WHERE is_blacklisted = TRUE
            ");
            $stmt->execute();
            $blacklistedIPs = $stmt->fetchColumn();
            
            return [
                'events' => $events,
                'active_alerts' => $activeAlerts,
                'blacklisted_ips' => $blacklistedIPs,
                'period_hours' => $hours
            ];
            
        } catch (PDOException $e) {
            error_log("Erreur get security stats: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Nettoie les anciens événements
     */
    public function cleanupOldEvents($days = 30) {
        try {
            $cutoff = date('Y-m-d H:i:s', time() - ($days * 24 * 3600));
            
            $stmt = $this->pdo->prepare("DELETE FROM security_events WHERE timestamp < ?");
            $deletedEvents = $stmt->execute([$cutoff]);
            
            $stmt = $this->pdo->prepare("DELETE FROM security_alerts WHERE resolved_at < ?");
            $deletedAlerts = $stmt->execute([$cutoff]);
            
            return [
                'events_deleted' => $deletedEvents,
                'alerts_deleted' => $deletedAlerts
            ];
            
        } catch (PDOException $e) {
            error_log("Erreur cleanup old events: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Handler d'alerte par email (exemple)
 */
function emailAlertHandler($alertType, $severity, $alertData, $alertId) {
    // Implémentation de l'envoi d'email
    $to = 'admin@elixir-du-temps.com';
    $subject = "Alerte de sécurité: $alertType";
    $message = "Une alerte de sécurité de niveau $severity a été déclenchée.\n\n";
    $message .= "Type: $alertType\n";
    $message .= "Données: " . json_encode($alertData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    // mail($to, $subject, $message); // Décommentez pour activer
}

/**
 * Handler d'alerte par log (exemple)
 */
function logAlertHandler($alertType, $severity, $alertData, $alertId) {
    if (function_exists('secureLog')) {
        secureLog('security', "Alert triggered: $alertType", [
            'alert_id' => $alertId,
            'severity' => $severity,
            'data' => $alertData
        ]);
    }
}
