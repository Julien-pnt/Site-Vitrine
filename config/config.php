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
 */
ini_set('display_errors', 1); // À mettre à 0 en production
ini_set('display_startup_errors', 1); // À mettre à 0 en production
error_reporting(E_ALL);

// Fuseau horaire
date_default_timezone_set('Europe/Paris');