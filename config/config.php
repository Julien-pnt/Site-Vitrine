<?php
/**
 * CONFIGURATION SÉCURISÉE - ELIXIR DU TEMPS
 * Version de production avec sécurité renforcée
 */

// ====================================================
// 1. SÉCURITÉ DE SESSION
// ====================================================

// Démarrage sécurisé de session
if (session_status() === PHP_SESSION_NONE) {
    // Configuration sécurisée des sessions
    ini_set('session.cookie_httponly', 1);        // Empêche l'accès JavaScript aux cookies
    ini_set('session.cookie_secure', 1);          // HTTPS uniquement  
    ini_set('session.use_strict_mode', 1);        // Mode strict
    ini_set('session.cookie_samesite', 'Strict'); // Protection CSRF
    ini_set('session.gc_maxlifetime', 1800);      // 30 minutes max
    ini_set('session.gc_probability', 1);         // Nettoyage automatique
    ini_set('session.gc_divisor', 100);
    
    session_start();
    
    // Régénération périodique de l'ID de session (toutes les 30 minutes)
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// ====================================================
// 2. CONFIGURATION DE L'ENVIRONNEMENT
// ====================================================

// Détection automatique de l'environnement
$isProduction = !in_array($_SERVER['HTTP_HOST'] ?? 'localhost', [
    'localhost', 
    '127.0.0.1', 
    '::1',
    'localhost:8000',
    'localhost:3000'
]);

// Configuration selon l'environnement
if ($isProduction) {
    // === PRODUCTION - SÉCURITÉ MAXIMALE ===
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(0);
    
    // Base de données production (à adapter)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'elixir_du_temps_prod');
    define('DB_USER', 'elixir_user');
    define('DB_PASSWORD', 'CHANGEZ_MOI_EN_PRODUCTION'); // ⚠️ À changer!
    
    define('APP_URL', 'https://votre-domaine.com');
    define('APP_ENV', 'production');
    
} else {
    // === DÉVELOPPEMENT - SÉCURISÉ MAIS DÉBUGABLE ===
    ini_set('display_errors', 0);     // Masqué pour éviter les fuites d'info
    ini_set('display_startup_errors', 1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    
    // Base de données développement
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'elixir_du_temps');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    
    define('APP_URL', 'http://localhost/Site-Vitrine');
    define('APP_ENV', 'development');
}

// ====================================================
// 3. CONSTANTES DE SÉCURITÉ
// ====================================================

// Clés de chiffrement (IMPORTANT: générer de nouvelles clés en production)
define('ENCRYPTION_KEY', 'elixir_2024_secure_key_' . hash('sha256', 'elixir_du_temps'));
define('JWT_SECRET', hash('sha256', 'jwt_secret_elixir_du_temps_2024'));

// Configuration des mots de passe
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_REQUIRE_SPECIAL', true);
define('PASSWORD_REQUIRE_NUMBERS', true);
define('PASSWORD_REQUIRE_UPPERCASE', true);

// Limitation des tentatives de connexion
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Configuration des tokens
define('CSRF_TOKEN_LIFETIME', 3600); // 1 heure
define('REMEMBER_TOKEN_LIFETIME', 2592000); // 30 jours
define('PASSWORD_RESET_TOKEN_LIFETIME', 3600); // 1 heure
define('SESSION_TIMEOUT', 1800); // 30 minutes

// ====================================================
// 4. CONFIGURATION DES CHEMINS
// ====================================================

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('ASSETS_PATH', PUBLIC_PATH . '/assets');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');
define('LOGS_PATH', ROOT_PATH . '/logs');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PHP_PATH', ROOT_PATH . '/php');

// Création des répertoires s'ils n'existent pas
$requiredDirs = [LOGS_PATH, UPLOADS_PATH . '/users', UPLOADS_PATH . '/products'];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ====================================================
// 5. CONFIGURATION DES LOGS SÉCURISÉS
// ====================================================

// Fichiers de logs séparés par type
define('ERROR_LOG_FILE', LOGS_PATH . '/php_errors.log');
define('SECURITY_LOG_FILE', LOGS_PATH . '/security.log');
define('ACCESS_LOG_FILE', LOGS_PATH . '/access.log');
define('ADMIN_LOG_FILE', LOGS_PATH . '/admin_actions.log');

// Configuration des logs PHP
ini_set('log_errors', 1);
ini_set('error_log', ERROR_LOG_FILE);

// ====================================================
// 6. CONFIGURATION DE SÉCURITÉ AVANCÉE
// ====================================================

// Headers de sécurité (backup au niveau PHP si .htaccess échoue)
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    
    // CSP adapté au site
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; " .
           "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; " .
           "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
           "img-src 'self' data: https:; " .
           "connect-src 'self'; " .
           "frame-ancestors 'none';";
    
    header("Content-Security-Policy: $csp");
    
    // HSTS en production
    if ($isProduction) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}

// Timezone
date_default_timezone_set('Europe/Paris');

// ====================================================
// 7. FONCTIONS DE SÉCURITÉ UTILITAIRES
// ====================================================

/**
 * Génère un token CSRF sécurisé
 */
function generateSecureCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) || 
        time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_LIFETIME) {
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valide un token CSRF
 */
function validateSecureCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && 
           isset($_SESSION['csrf_token_time']) &&
           time() - $_SESSION['csrf_token_time'] <= CSRF_TOKEN_LIFETIME &&
           hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log sécurisé des événements
 */
function secureLog($level, $message, $context = []) {
    if (!defined('LOGS_PATH')) return;
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 200);
    $userId = $_SESSION['user_id'] ?? 'anonymous';
    
    $logEntry = sprintf(
        "[%s] %s - User:%s - IP:%s - %s - Context:%s\n",
        $timestamp,
        strtoupper($level),
        $userId,
        $ip,
        $message,
        json_encode($context, JSON_UNESCAPED_UNICODE)
    );
    
    // Déterminer le fichier de log selon le niveau
    $logFile = match($level) {
        'security', 'warning', 'error' => SECURITY_LOG_FILE,
        'admin' => ADMIN_LOG_FILE,
        'access' => ACCESS_LOG_FILE,
        default => ERROR_LOG_FILE
    };
    
    if (is_writable(dirname($logFile))) {
        error_log($logEntry, 3, $logFile);
    }
}

/**
 * Nettoyage sécurisé des données d'entrée
 */
function secureInput($input, $type = 'string') {
    if (is_array($input)) {
        return array_map(function($item) use ($type) {
            return secureInput($item, $type);
        }, $input);
    }
    
    $input = trim($input);
    
    return match($type) {
        'email' => filter_var($input, FILTER_SANITIZE_EMAIL),
        'int' => filter_var($input, FILTER_SANITIZE_NUMBER_INT),
        'float' => filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        'url' => filter_var($input, FILTER_SANITIZE_URL),
        'html' => htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        default => htmlspecialchars(strip_tags($input), ENT_QUOTES | ENT_HTML5, 'UTF-8')
    };
}

/**
 * Détection d'attaques basiques
 */
function detectAttackPatterns($input) {
    $suspiciousPatterns = [
        '/(\<script|\<\/script\>)/i',        // XSS
        '/(union|select|insert|delete|update|drop)/i', // SQL Injection
        '/(\.\.\/)/',                         // Directory traversal
        '/(eval\(|base64_decode)/i',         // Code injection
        '/(<iframe|<object|<embed)/i'        // Malicious embeds
    ];
    
    foreach ($suspiciousPatterns as $pattern) {
        if (preg_match($pattern, $input)) {
            secureLog('security', 'Attack pattern detected', [
                'input' => substr($input, 0, 100),
                'pattern' => $pattern,
                'url' => $_SERVER['REQUEST_URI'] ?? ''
            ]);
            return true;
        }
    }
    return false;
}

// ====================================================
// 8. INITIALISATION ET VÉRIFICATIONS
// ====================================================

// Génération automatique du token CSRF
generateSecureCSRFToken();

// Vérifications de sécurité au chargement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier la taille de la requête
    $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
    if ($contentLength > 10485760) { // 10MB
        secureLog('security', 'Request too large', ['size' => $contentLength]);
        http_response_code(413);
        exit('Request too large');
    }
    
    // Vérifier les patterns d'attaque dans les données POST
    foreach ($_POST as $key => $value) {
        if (detectAttackPatterns($key . $value)) {
            http_response_code(400);
            exit('Invalid request');
        }
    }
}

// Configuration finale
define('SECURITY_CONFIG_LOADED', true);