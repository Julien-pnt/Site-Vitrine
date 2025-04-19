<?php
/**
 * Configuration de la connexion à la base de données
 */

// Classe pour la connexion à la base de données
class Database {
    // Variables de connexion
    private $host = 'localhost';
    private $db = 'elixir_du_temps';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    private $pdo;

    public function getConnection() {
        if ($this->pdo) {
            return $this->pdo;
        }

        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
            return $this->pdo;
        } catch (PDOException $e) {
            error_log('Erreur de connexion: ' . $e->getMessage());
            
            // En mode développement, afficher l'erreur
            echo '<!DOCTYPE html>
            <html>
            <head>
                <title>Erreur de connexion</title>
                <style>
                    body { font-family: Arial, sans-serif; background: #f3f3f3; margin: 0; padding: 20px; }
                    .error-box { max-width: 800px; margin: 50px auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                    h1 { color: #d4af37; }
                    pre { background: #f8f8f8; padding: 15px; border-left: 5px solid #d4af37; overflow: auto; }
                </style>
            </head>
            <body>
                <div class="error-box">
                    <h1>Erreur de connexion à la base de données</h1>
                    <p>Impossible de se connecter à la base de données. Vérifiez que:</p>
                    <ul>
                        <li>Le serveur MySQL est en cours d\'exécution</li>
                        <li>La base de données "' . htmlspecialchars($this->db) . '" existe</li>
                        <li>Les informations de connexion sont correctes</li>
                    </ul>
                    <pre>' . htmlspecialchars($e->getMessage()) . '</pre>
                </div>
            </body>
            </html>';
            exit;
        }
    }
}

// Pour maintenir la compatibilité avec le code existant qui utilise $pdo directement
$host = 'localhost';
$db   = 'elixir_du_temps';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log('Erreur de connexion: ' . $e->getMessage());
    
    // En mode développement, afficher l'erreur
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Erreur de connexion</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f3f3f3; margin: 0; padding: 20px; }
            .error-box { max-width: 800px; margin: 50px auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            h1 { color: #d4af37; }
            pre { background: #f8f8f8; padding: 15px; border-left: 5px solid #d4af37; overflow: auto; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>Erreur de connexion à la base de données</h1>
            <p>Impossible de se connecter à la base de données. Vérifiez que:</p>
            <ul>
                <li>Le serveur MySQL est en cours d\'exécution</li>
                <li>La base de données "' . htmlspecialchars($db) . '" existe</li>
                <li>Les informations de connexion sont correctes</li>
            </ul>
            <pre>' . htmlspecialchars($e->getMessage()) . '</pre>
        </div>
    </body>
    </html>';
    exit;
}