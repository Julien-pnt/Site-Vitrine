<?php
// Charger les variables d'environnement depuis un fichier .env
if (file_exists(__DIR__ . '/../.env')) {
    // Lire le fichier .env et stocker les variables dans un tableau associatif
    $env = parse_ini_file(__DIR__ . '/../.env');
    // Parcourir chaque variable et la définir dans l'environnement
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
}

// Paramètres de connexion à la base de données
$host = getenv('DB_HOST') ?: 'localhost'; // Hôte de la base de données
$dbname = getenv('DB_NAME') ?: 'elixir_du_temps'; // Nom de la base de données
$username = getenv('DB_USER') ?: 'root'; // Nom d'utilisateur de la base de données
$password = getenv('DB_PASSWORD') ?: ''; // Mot de passe de la base de données

try {
    // Créer une nouvelle instance de PDO pour la connexion à la base de données
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $username, 
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Activer les exceptions pour les erreurs PDO
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Définir le mode de récupération par défaut sur FETCH_ASSOC
            PDO::ATTR_EMULATE_PREPARES => false // Désactiver l'émulation des requêtes préparées
        ]
    );
} catch (PDOException $e) {
    // Logger l'erreur de connexion à la base de données
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    
    // Afficher un message d'erreur générique pour l'utilisateur
    die("Une erreur est survenue lors de la connexion à la base de données.");
}

// Fonction utilitaire pour vérifier la connexion
function checkConnection() {
    global $pdo;
    try {
        // Exécuter une requête simple pour vérifier la connexion
        $pdo->query('SELECT 1');
        return true; // La connexion est valide
    } catch (PDOException $e) {
        return false; // La connexion a échoué
    }
}