<?php
/**
 * Classe Logger avancée pour Site-Vitrine
 */
class Logger {
    private $pdo;
    private $sessionId;
    
    /**
     * Constructeur
     * @param PDO $pdo Instance PDO pour l'accès à la base de données
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->sessionId = session_id() ?: null;
    }
    
    /**
     * Log une action
     * 
     * @param string $level Niveau de log (debug, info, warning, error, etc.)
     * @param string $category Catégorie du log (auth, product, order, etc.)
     * @param string $action Action effectuée (login, create, update, delete, etc.)
     * @param array $params Paramètres additionnels
     * @return bool
     */
    public function log($level, $category, $action, array $params = []) {
        $startTime = microtime(true);
        
        // Paramètres par défaut
        $defaults = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_type' => isset($_SESSION['user_id']) ? (isAdmin() ? 'admin' : 'customer') : 'guest',
            'entity_type' => null,
            'entity_id' => null,
            'details' => null,
            'before_state' => null,
            'after_state' => null,
            'context' => null,
            'ip_address' => $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'http_method' => $_SERVER['REQUEST_METHOD'] ?? null,
            'request_url' => $_SERVER['REQUEST_URI'] ?? null
        ];
        
        // Fusionner les paramètres par défaut avec ceux fournis
        $params = array_merge($defaults, $params);
        
        // Convertir les tableaux en JSON pour les champs appropriés
        foreach (['context', 'before_state', 'after_state'] as $field) {
            if (isset($params[$field]) && is_array($params[$field])) {
                $params[$field] = json_encode($params[$field]);
            }
        }
        
        // Préparation de la requête SQL
        $sql = "INSERT INTO system_logs (
            level, user_id, user_type, category, action, entity_type, entity_id, 
            details, before_state, after_state, ip_address, user_agent, context, 
            http_method, request_url, session_id, execution_time
        ) VALUES (
            :level, :user_id, :user_type, :category, :action, :entity_type, :entity_id,
            :details, :before_state, :after_state, :ip_address, :user_agent, :context,
            :http_method, :request_url, :session_id, :execution_time
        )";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Calculer le temps d'exécution
            $params['execution_time'] = microtime(true) - $startTime;
            $params['session_id'] = $this->sessionId;
            $params['level'] = $level;
            $params['category'] = $category;
            $params['action'] = $action;
            
            // Exécution de la requête
            return $stmt->execute($params);
        } catch (PDOException $e) {
            // En cas d'erreur, logger dans le fichier d'erreur PHP
            error_log("Erreur de log: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log les modifications d'une entité
     * 
     * @param string $entityType Type d'entité (product, user, order, etc.)
     * @param int $entityId ID de l'entité
     * @param array $oldData Anciennes données
     * @param array $newData Nouvelles données
     * @param string $action Action effectuée (update, create, delete)
     * @param string $details Détails additionnels
     * @return bool
     */
    public function logChanges($entityType, $entityId, $oldData, $newData, $action = 'update', $details = null) {
        // Détecter les champs modifiés
        $changes = [];
        foreach ($newData as $key => $value) {
            if (!isset($oldData[$key]) || $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value
                ];
            }
        }
        
        // S'il n'y a pas de changements et que c'est une mise à jour, ne pas logger
        if (empty($changes) && $action === 'update') {
            return true;
        }
        
        // Créer un résumé des modifications pour le champ details
        if (!$details) {
            if ($action === 'create') {
                $details = "Création de {$entityType} #{$entityId}";
            } elseif ($action === 'delete') {
                $details = "Suppression de {$entityType} #{$entityId}";
            } else {
                $changedFields = array_keys($changes);
                $details = "Mise à jour de " . count($changedFields) . " champs: " . implode(', ', $changedFields);
            }
        }
        
        // Logger les modifications
        return $this->log(
            'info',
            $entityType,
            $action,
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'details' => $details,
                'before_state' => $oldData,
                'after_state' => $newData,
                'context' => ['changes' => $changes]
            ]
        );
    }
    
    /**
     * Méthodes de raccourci pour différents niveaux de log
     */
    public function debug($category, $action, array $params = []) {
        return $this->log('debug', $category, $action, $params);
    }
    
    public function info($category, $action, array $params = []) {
        return $this->log('info', $category, $action, $params);
    }
    
    public function notice($category, $action, array $params = []) {
        return $this->log('notice', $category, $action, $params);
    }
    
    public function warning($category, $action, array $params = []) {
        return $this->log('warning', $category, $action, $params);
    }
    
    public function error($category, $action, array $params = []) {
        return $this->log('error', $category, $action, $params);
    }
    
    public function critical($category, $action, array $params = []) {
        return $this->log('critical', $category, $action, $params);
    }
    
    public function alert($category, $action, array $params = []) {
        return $this->log('alert', $category, $action, $params);
    }
    
    public function emergency($category, $action, array $params = []) {
        return $this->log('emergency', $category, $action, $params);
    }
    
    /**
     * Récupère l'adresse IP du client
     * @return string|null
     */
    private function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? null;
        }
    }
}