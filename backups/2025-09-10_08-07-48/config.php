<?php
/**
 * Configuration de la base de données
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'elixir_du_temps');
define('DB_USER', 'root'); // à remplacer par votre utilisateur MySQL
define('DB_PASSWORD', ''); // à remplacer par votre mot de passe

/**
 * Configuration de l'application
 */
define('APP_URL', 'http://localhost/julien-pnt-site-vitrine'); // à adapter selon votre configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes en secondes

/**
 * Configuration des chemins
 */
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/assets/uploads');

/**
 * Initialisation de l'environnement
 * ATTENTION: Configuration non sécurisée - utiliser config_secure.php en production
 */
$isProduction = !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', 'localhost:8000']);

if ($isProduction) {
    // Production - Sécurité activée
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(0);
} else {
    // Développement
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Fuseau horaire
date_default_timezone_set('Europe/Paris');