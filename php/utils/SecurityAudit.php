<?php
/**
 * Classe d'audit et monitoring de sécurité
 */

class SecurityAudit {
    
    private $pdo;
    private $logFile;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->logFile = LOGS_PATH . '/audit.log';
        $this->ensureAuditTable();
    }
    
    /**
     * Crée la table d'audit si elle n'existe pas
     */
    private function ensureAuditTable() {
        $sql = "CREATE TABLE IF NOT EXISTS security_audit (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_type VARCHAR(100) NOT NULL,
            severity ENUM('LOW', 'MEDIUM', 'HIGH', 'CRITICAL') DEFAULT 'MEDIUM',
            user_id INT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            request_uri TEXT,
            event_data JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_event_type (event_type),
            INDEX idx_severity (severity),
            INDEX idx_created_at (created_at),
            INDEX idx_ip_address (ip_address)
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erreur création table audit: " . $e->getMessage());
        }
    }
    
    /**
     * Enregistre un événement de sécurité
     */
    public function logEvent($eventType, $severity = 'MEDIUM', $eventData = []) {
        // Log en base de données
        $this->logToDatabase($eventType, $severity, $eventData);
        
        // Log en fichier
        $this->logToFile($eventType, $severity, $eventData);
        
        // Alertes pour les événements critiques
        if ($severity === 'CRITICAL') {
            $this->sendCriticalAlert($eventType, $eventData);
        }
    }
    
    /**
     * Enregistre en base de données
     */
    private function logToDatabase($eventType, $severity, $eventData) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO security_audit 
                (event_type, severity, user_id, ip_address, user_agent, request_uri, event_data) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $eventType,
                $severity,
                $_SESSION['user_id'] ?? null,
                $this->getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $_SERVER['REQUEST_URI'] ?? '',
                json_encode($eventData)
            ]);
        } catch (PDOException $e) {
            error_log("Erreur log audit DB: " . $e->getMessage());
        }
    }
    
    /**
     * Enregistre dans un fichier
     */
    private function logToFile($eventType, $severity, $eventData) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event_type' => $eventType,
            'severity' => $severity,
            'user_id' => $_SESSION['user_id'] ?? null,
            'ip' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'data' => $eventData
        ];
        
        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Envoie une alerte critique
     */
    private function sendCriticalAlert($eventType, $eventData) {
        // Ici on pourrait envoyer un email, webhook, SMS, etc.
        $alertMessage = "ALERTE SÉCURITÉ CRITIQUE\n";
        $alertMessage .= "Type: $eventType\n";
        $alertMessage .= "IP: " . $this->getClientIP() . "\n";
        $alertMessage .= "Heure: " . date('Y-m-d H:i:s') . "\n";
        $alertMessage .= "Données: " . json_encode($eventData, JSON_PRETTY_PRINT);
        
        // Log l'alerte
        error_log("CRITICAL SECURITY ALERT: " . $alertMessage);
        
        // TODO: Implémenter notification email/SMS/Slack
    }
    
    /**
     * Analyse les tentatives de connexion suspectes
     */
    public function analyzeLoginAttempts() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT ip_address, COUNT(*) as attempts, MAX(created_at) as last_attempt
                FROM security_audit 
                WHERE event_type = 'LOGIN_FAILED' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
                GROUP BY ip_address
                HAVING attempts >= 5
                ORDER BY attempts DESC
            ");
            
            $stmt->execute();
            $suspiciousIPs = $stmt->fetchAll();
            
            foreach ($suspiciousIPs as $ip) {
                $this->logEvent('BRUTEFORCE_DETECTED', 'HIGH', [
                    'ip' => $ip['ip_address'],
                    'attempts' => $ip['attempts'],
                    'last_attempt' => $ip['last_attempt']
                ]);
            }
            
            return $suspiciousIPs;
        } catch (PDOException $e) {
            error_log("Erreur analyse tentatives: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Vérifie les patterns d'attaque
     */
    public function detectAttackPatterns() {
        $patterns = [
            'SQL_INJECTION' => [
                'union.*select',
                'drop.*table',
                '1.*=.*1',
                'or.*1.*=.*1'
            ],
            'XSS_ATTEMPT' => [
                '<script',
                'javascript:',
                'onerror=',
                'onload='
            ],
            'PATH_TRAVERSAL' => [
                '\.\./\.\./\.\.',
                '\.\.\\\\\.\.\\\\',
                '/etc/passwd',
                'boot\.ini'
            ]
        ];
        
        $requestData = $_SERVER['QUERY_STRING'] . ' ' . $_SERVER['REQUEST_URI'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requestData .= ' ' . file_get_contents('php://input');
        }
        
        foreach ($patterns as $attackType => $patternList) {
            foreach ($patternList as $pattern) {
                if (preg_match('/' . $pattern . '/i', $requestData)) {
                    $this->logEvent($attackType, 'HIGH', [
                        'pattern' => $pattern,
                        'request_data' => substr($requestData, 0, 1000) // Limiter la taille
                    ]);
                    return $attackType;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Génère un rapport de sécurité
     */
    public function generateSecurityReport($days = 7) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    event_type,
                    severity,
                    COUNT(*) as count,
                    DATE(created_at) as date
                FROM security_audit 
                WHERE created_at > DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY event_type, severity, DATE(created_at)
                ORDER BY date DESC, severity DESC, count DESC
            ");
            
            $stmt->execute([$days]);
            $results = $stmt->fetchAll();
            
            $report = [
                'period' => $days . ' derniers jours',
                'generated_at' => date('Y-m-d H:i:s'),
                'events' => $results,
                'summary' => []
            ];
            
            // Résumé par sévérité
            $severityCounts = [];
            foreach ($results as $row) {
                $severityCounts[$row['severity']] = ($severityCounts[$row['severity']] ?? 0) + $row['count'];
            }
            $report['summary']['by_severity'] = $severityCounts;
            
            // Top 10 des types d'événements
            $eventCounts = [];
            foreach ($results as $row) {
                $eventCounts[$row['event_type']] = ($eventCounts[$row['event_type']] ?? 0) + $row['count'];
            }
            arsort($eventCounts);
            $report['summary']['top_events'] = array_slice($eventCounts, 0, 10, true);
            
            return $report;
            
        } catch (PDOException $e) {
            error_log("Erreur génération rapport: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Nettoie les anciens logs
     */
    public function cleanupOldLogs($days = 90) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM security_audit 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            $stmt->execute([$days]);
            
            $deletedCount = $stmt->rowCount();
            
            $this->logEvent('LOG_CLEANUP', 'LOW', [
                'deleted_records' => $deletedCount,
                'older_than_days' => $days
            ]);
            
            return $deletedCount;
            
        } catch (PDOException $e) {
            error_log("Erreur nettoyage logs: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtient l'IP du client
     */
    private function getClientIP() {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR', 
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                return trim($ips[0]);
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
