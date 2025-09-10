<?php
/**
 * Middleware de sécurité avancé
 * À inclure au début de chaque page/API
 */

class SecurityMiddleware {
    
    private static $rateLimitStore = [];
    private static $maxAttempts = 5;
    private static $timeWindow = 300; // 5 minutes
    
    /**
     * Initialise toutes les protections de sécurité
     */
    public static function init() {
        self::startSecureSession();
        self::validateCSRF();
        self::checkRateLimit();
        self::validateRequest();
    }
    
    /**
     * Démarre une session sécurisée
     */
    private static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            
            // Régénérer l'ID de session périodiquement
            if (!isset($_SESSION['last_regeneration'])) {
                $_SESSION['last_regeneration'] = time();
            } elseif (time() - $_SESSION['last_regeneration'] > 300) {
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }
    }
    
    /**
     * Valide le token CSRF pour les requêtes POST
     */
    private static function validateCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (empty($token) || !isset($_SESSION['csrf_token']) || 
                !hash_equals($_SESSION['csrf_token'], $token)) {
                self::logSecurityEvent('CSRF_ATTACK_ATTEMPT');
                http_response_code(403);
                die('CSRF validation failed');
            }
        }
    }
    
    /**
     * Vérifie le rate limiting par IP
     */
    private static function checkRateLimit() {
        $ip = self::getClientIP();
        $currentTime = time();
        
        // Nettoyer les anciennes entrées
        self::$rateLimitStore = array_filter(self::$rateLimitStore, 
            function($data) use ($currentTime) {
                return ($currentTime - $data['time']) < self::$timeWindow;
            }
        );
        
        // Compter les requêtes de cette IP
        $attempts = array_filter(self::$rateLimitStore, 
            function($data) use ($ip) {
                return $data['ip'] === $ip;
            }
        );
        
        if (count($attempts) >= self::$maxAttempts) {
            self::logSecurityEvent('RATE_LIMIT_EXCEEDED', ['ip' => $ip]);
            http_response_code(429);
            die('Rate limit exceeded');
        }
        
        // Enregistrer cette requête
        self::$rateLimitStore[] = ['ip' => $ip, 'time' => $currentTime];
    }
    
    /**
     * Valide la requête contre les attaques communes
     */
    private static function validateRequest() {
        $suspicious = false;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Détection de patterns malveillants
        $maliciousPatterns = [
            '/(\<|%3C).*script.*(\>|%3E)/i',
            '/union.*select/i',
            '/drop.*table/i',
            '/\.\./.*\.\./.*\.\./i',
            '/exec\s*\(/i',
            '/eval\s*\(/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $queryString . $requestUri)) {
                $suspicious = true;
                break;
            }
        }
        
        // Vérifier les en-têtes suspects
        if (empty($userAgent) || strlen($userAgent) > 500) {
            $suspicious = true;
        }
        
        if ($suspicious) {
            self::logSecurityEvent('MALICIOUS_REQUEST_DETECTED', [
                'ip' => self::getClientIP(),
                'user_agent' => $userAgent,
                'query_string' => $queryString,
                'request_uri' => $requestUri
            ]);
            
            http_response_code(403);
            die('Suspicious request detected');
        }
    }
    
    /**
     * Génère un token CSRF
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valide un fichier uploadé
     */
    public static function validateUpload($file, $allowedTypes = ALLOWED_IMAGE_TYPES) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'Fichier non valide'];
        }
        
        // Vérifier la taille
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['valid' => false, 'error' => 'Fichier trop volumineux'];
        }
        
        // Vérifier le type MIME réel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['valid' => false, 'error' => 'Type de fichier non autorisé'];
        }
        
        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
            return ['valid' => false, 'error' => 'Extension non autorisée'];
        }
        
        // Vérifier que c'est vraiment une image
        if (strpos($mimeType, 'image/') === 0) {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                return ['valid' => false, 'error' => 'Fichier image corrompu'];
            }
        }
        
        return ['valid' => true];
    }
    
    /**
     * Obtient l'IP réelle du client
     */
    private static function getClientIP() {
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
    
    /**
     * Enregistre un événement de sécurité
     */
    private static function logSecurityEvent($event, $details = []) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => self::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'user_id' => $_SESSION['user_id'] ?? null,
            'details' => $details
        ];
        
        $logFile = LOGS_PATH . '/security.log';
        $logLine = json_encode($logData) . "\n";
        
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        // Si le fichier devient trop gros, le faire tourner
        if (file_exists($logFile) && filesize($logFile) > LOG_MAX_SIZE) {
            rename($logFile, $logFile . '.' . date('Ymd'));
        }
    }
    
    /**
     * Nettoie et valide une chaîne
     */
    public static function sanitizeString($input, $maxLength = 255) {
        $cleaned = trim($input);
        $cleaned = htmlspecialchars($cleaned, ENT_QUOTES, 'UTF-8');
        
        if (strlen($cleaned) > $maxLength) {
            $cleaned = substr($cleaned, 0, $maxLength);
        }
        
        return $cleaned;
    }
    
    /**
     * Valide un email
     */
    public static function validateEmail($email) {
        $cleaned = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($cleaned, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Valide un numéro de téléphone
     */
    public static function validatePhone($phone) {
        $cleaned = preg_replace('/[^+\d\s.-]/', '', $phone);
        return strlen($cleaned) >= 10 && strlen($cleaned) <= 20 ? $cleaned : false;
    }
    
    /**
     * Hache un mot de passe de manière sécurisée
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
}

// Auto-initialisation si cette classe est incluse
if (!defined('SECURITY_MIDDLEWARE_SKIP_INIT')) {
    SecurityMiddleware::init();
}
