<?php
/**
 * Configuration sécurisée pour la production
 * Remplace le fichier config.php actuel
 */

// Détection automatique de l'environnement
$isProduction = !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', 'localhost:8000']);

/**
 * Configuration de la base de données
 */
if ($isProduction) {
    // Production - utiliser les variables d'environnement
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_NAME', getenv('DB_NAME') ?: 'elixir_du_temps');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
} else {
    // Développement
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'elixir_du_temps');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
}

/**
 * Configuration de l'application
 */
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
define('APP_URL', $protocol . '://' . $host . '/Site-Vitrine');
define('SESSION_TIMEOUT', 1800); // 30 minutes

/**
 * Clés de sécurité
 */
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'your-secret-encryption-key-change-this');
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'your-jwt-secret-key-change-this');

/**
 * Configuration des chemins
 */
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');
define('LOGS_PATH', ROOT_PATH . '/logs');

/**
 * Configuration de sécurité
 */
if ($isProduction) {
    // Production - Sécurité maximale
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . '/php_errors.log');
    error_reporting(0);
    
    // Configuration de session sécurisée
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_lifetime', 0);
    
} else {
    // Développement - Affichage d'erreurs pour debug
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // Session moins stricte pour le dev
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
}

/**
 * Configuration des uploads
 */
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

/**
 * Configuration du cache
 */
define('CACHE_ENABLED', $isProduction);
define('CACHE_DURATION', 3600); // 1 heure
define('CACHE_PATH', ROOT_PATH . '/cache');

/**
 * Configuration des logs
 */
define('LOG_LEVEL', $isProduction ? 'ERROR' : 'DEBUG');
define('LOG_MAX_SIZE', 10 * 1024 * 1024); // 10MB

/**
 * Fuseau horaire
 */
date_default_timezone_set('Europe/Paris');

/**
 * Création des répertoires nécessaires
 */
$requiredDirs = [UPLOADS_PATH, LOGS_PATH, CACHE_PATH];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

/**
 * Fonction utilitaire pour vérifier si on est en production
 */
function isProduction() {
    global $isProduction;
    return $isProduction;
}

/**
 * Fonction de sécurité pour les en-têtes HTTP
 */
function setSecurityHeaders() {
    if (!headers_sent()) {
        // Protection XSS
        header('X-XSS-Protection: 1; mode=block');
        
        // Empêcher le MIME sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Protection clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // Politique de référent
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Si HTTPS, forcer HTTPS pour toutes les requêtes futures
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        // Content Security Policy basique
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; ";
        $csp .= "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com; ";
        $csp .= "font-src 'self' fonts.gstatic.com; ";
        $csp .= "img-src 'self' data:; ";
        header("Content-Security-Policy: " . $csp);
    }
}

// Appliquer les en-têtes de sécurité
setSecurityHeaders();
