<?php
/**
 * PROTECTION AUTOMATIQUE DE SÉCURITÉ
 * À inclure en haut de chaque page pour une protection complète
 */

// Démarrer la protection seulement si elle n'est pas déjà active
if (!defined('ELIXIR_SECURITY_ACTIVE')) {
    define('ELIXIR_SECURITY_ACTIVE', true);
    
    // Charger la configuration sécurisée
    if (!defined('SECURITY_CONFIG_LOADED')) {
        $configPath = __DIR__ . '/../config/config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
        }
    }
    
    // Charger les utilitaires de sécurité
    $securityUtilsPath = __DIR__ . '/../php/utils';
    if (is_dir($securityUtilsPath)) {
        // Charger les classes nécessaires
        $securityFiles = [
            'BruteForceProtection.php',
            'SecurityMiddleware.php',
            'SecureInputValidator.php'
        ];
        
        foreach ($securityFiles as $file) {
            $filePath = $securityUtilsPath . '/' . $file;
            if (file_exists($filePath)) {
                require_once $filePath;
            }
        }
    }
    
    /**
     * Classe de protection automatique
     */
    class ElixirSecurityGuard {
        
        private static $instance = null;
        private $isInitialized = false;
        
        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        /**
         * Initialise la protection complète
         */
        public function initialize() {
            if ($this->isInitialized) {
                return;
            }
            
            // 1. Protection de base
            $this->basicSecuritySetup();
            
            // 2. Protection contre la force brute
            $this->setupBruteForceProtection();
            
            // 3. Middleware de sécurité
            $this->setupSecurityMiddleware();
            
            // 4. Protection CSRF
            $this->setupCSRFProtection();
            
            // 5. Validation des entrées
            $this->setupInputValidation();
            
            // 6. Monitoring de sécurité
            $this->setupSecurityMonitoring();
            
            $this->isInitialized = true;
        }
        
        /**
         * Configuration de sécurité de base
         */
        private function basicSecuritySetup() {
            // Headers de sécurité de base
            if (!headers_sent()) {
                header('X-Frame-Options: SAMEORIGIN');
                header('X-XSS-Protection: 1; mode=block');
                header('X-Content-Type-Options: nosniff');
                header('Referrer-Policy: strict-origin-when-cross-origin');
                
                // CSP basique si pas déjà défini
                if (!$this->headerExists('Content-Security-Policy')) {
                    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");
                }
            }
            
            // Configuration PHP sécurisée
            ini_set('display_errors', 0);
            ini_set('log_errors', 1);
            
            // Session sécurisée
            if (session_status() === PHP_SESSION_NONE) {
                ini_set('session.cookie_httponly', 1);
                ini_set('session.cookie_secure', 1);
                ini_set('session.use_strict_mode', 1);
                session_start();
            }
        }
        
        /**
         * Protection contre la force brute
         */
        private function setupBruteForceProtection() {
            if (class_exists('BruteForceProtection') && function_exists('checkBruteForceProtection')) {
                // Vérifier seulement pour les pages de connexion
                $loginPages = ['login.php', 'admin-login.php'];
                $currentPage = basename($_SERVER['SCRIPT_NAME']);
                
                if (in_array($currentPage, $loginPages)) {
                    $protection = checkBruteForceProtection();
                    if (!$protection['allowed']) {
                        http_response_code(429);
                        die(json_encode([
                            'error' => 'Too Many Requests',
                            'message' => $protection['message']
                        ]));
                    }
                }
            }
        }
        
        /**
         * Middleware de sécurité
         */
        private function setupSecurityMiddleware() {
            if (class_exists('SecurityMiddleware')) {
                try {
                    // Initialiser sans auto-execution
                    define('SECURITY_MIDDLEWARE_MANUAL', true);
                    $middleware = SecurityMiddleware::getInstance();
                    $middleware->handle();
                } catch (Exception $e) {
                    // Log l'erreur mais ne pas interrompre l'exécution
                    error_log("Erreur SecurityMiddleware: " . $e->getMessage());
                }
            }
        }
        
        /**
         * Protection CSRF
         */
        private function setupCSRFProtection() {
            // Générer automatiquement un token CSRF
            if (function_exists('generateSecureCSRFToken')) {
                generateSecureCSRFToken();
            } else {
                // Fallback simple
                if (!isset($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
            }
            
            // Valider le token pour les requêtes POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRFToken();
            }
        }
        
        /**
         * Validation des entrées
         */
        private function setupInputValidation() {
            // Scanner automatiquement les entrées dangereuses
            $inputs = array_merge($_GET, $_POST, $_COOKIE);
            
            foreach ($inputs as $key => $value) {
                if (function_exists('detectAttackPatterns')) {
                    if (detectAttackPatterns($value)) {
                        $this->logSecurityThreat('input_attack', $key, $value);
                        http_response_code(400);
                        die('Invalid input detected');
                    }
                }
            }
        }
        
        /**
         * Monitoring de sécurité
         */
        private function setupSecurityMonitoring() {
            // Log de l'accès à la page
            if (function_exists('secureLog')) {
                secureLog('access', 'Page accessed', [
                    'page' => $_SERVER['REQUEST_URI'] ?? '',
                    'method' => $_SERVER['REQUEST_METHOD'] ?? '',
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]);
            }
        }
        
        /**
         * Valide le token CSRF
         */
        private function validateCSRFToken() {
            $token = $_POST['csrf_token'] ?? '';
            
            if (function_exists('validateSecureCSRFToken')) {
                if (!validateSecureCSRFToken($token)) {
                    $this->logSecurityThreat('csrf_attack', 'POST', $_POST);
                    http_response_code(403);
                    die('CSRF token validation failed');
                }
            } else {
                // Fallback simple
                if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                    http_response_code(403);
                    die('CSRF token validation failed');
                }
            }
        }
        
        /**
         * Vérifie si un header existe
         */
        private function headerExists($headerName) {
            foreach (headers_list() as $header) {
                if (str_starts_with($header, $headerName . ':')) {
                    return true;
                }
            }
            return false;
        }
        
        /**
         * Log des menaces de sécurité
         */
        private function logSecurityThreat($type, $context, $data) {
            if (function_exists('secureLog')) {
                secureLog('security', "Security threat detected: $type", [
                    'context' => $context,
                    'data_preview' => substr(json_encode($data), 0, 200),
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'url' => $_SERVER['REQUEST_URI'] ?? ''
                ]);
            } else {
                error_log("Security threat: $type in $context");
            }
        }
        
        /**
         * Obtient un token CSRF pour les formulaires
         */
        public function getCSRFToken() {
            if (function_exists('generateSecureCSRFToken')) {
                return generateSecureCSRFToken();
            }
            return $_SESSION['csrf_token'] ?? '';
        }
        
        /**
         * Génère un input hidden pour CSRF
         */
        public function getCSRFInput() {
            $token = $this->getCSRFToken();
            return "<input type=\"hidden\" name=\"csrf_token\" value=\"$token\">";
        }
        
        /**
         * Nettoie une entrée utilisateur
         */
        public function cleanInput($input, $type = 'string') {
            if (function_exists('sanitize')) {
                return sanitize($input);
            }
            
            // Fallback simple
            $input = trim($input);
            $input = stripslashes($input);
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            return $input;
        }
        
        /**
         * Valide un email
         */
        public function validateEmail($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }
        
        /**
         * Vérifie les permissions d'accès
         */
        public function checkAccess($requiredRole = 'user') {
            if (function_exists('checkAccess')) {
                return checkAccess($requiredRole);
            }
            
            // Vérification basique
            if ($requiredRole === 'admin') {
                return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
            }
            
            return isset($_SESSION['user_id']);
        }
        
        /**
         * Redirige vers une page sécurisée
         */
        public function redirectToLogin($message = '') {
            $loginPage = '/Site-Vitrine/public/pages/auth/login.php';
            if (!empty($message)) {
                $loginPage .= '?message=' . urlencode($message);
            }
            header("Location: $loginPage");
            exit;
        }
    }
    
    // Initialiser automatiquement la protection
    $securityGuard = ElixirSecurityGuard::getInstance();
    $securityGuard->initialize();
    
    /**
     * Fonctions utilitaires globales
     */
    if (!function_exists('csrf_token')) {
        function csrf_token() {
            return ElixirSecurityGuard::getInstance()->getCSRFToken();
        }
    }
    
    if (!function_exists('csrf_input')) {
        function csrf_input() {
            return ElixirSecurityGuard::getInstance()->getCSRFInput();
        }
    }
    
    if (!function_exists('secure_input')) {
        function secure_input($input, $type = 'string') {
            return ElixirSecurityGuard::getInstance()->cleanInput($input, $type);
        }
    }
    
    if (!function_exists('require_auth')) {
        function require_auth($role = 'user') {
            $guard = ElixirSecurityGuard::getInstance();
            if (!$guard->checkAccess($role)) {
                $guard->redirectToLogin('Accès non autorisé');
            }
        }
    }
    
    if (!function_exists('require_admin')) {
        function require_admin() {
            require_auth('admin');
        }
    }
}

// Exemple d'utilisation dans une page :
/*
<?php
// En haut de votre page PHP
require_once 'path/to/security_guard.php';

// Pour un formulaire avec protection CSRF
echo csrf_input();

// Pour nettoyer une entrée
$clean_name = secure_input($_POST['name']);

// Pour vérifier les droits admin
require_admin();

?>
*/
