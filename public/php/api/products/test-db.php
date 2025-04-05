<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Test de connexion à la base de données</h1>";

try {
    // Chemin direct à la base de données
    $host = 'localhost';
    $db   = 'elixir_du_temps'; // Vérifiez que c'est le bon nom de la base
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
    
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    echo "<p>Tentative de connexion...</p>";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<p style='color:green'>Connexion réussie!</p>";
    
    echo "<p>Tentative de requête simple...</p>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "<p style='color:green'>Requête réussie! Tables dans la base de données:</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . reset($table) . "</li>";
    }
    echo "</ul>";
    
    echo "<p>Tentative de requête sur la table produits...</p>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM produits");
    $count = $stmt->fetch();
    echo "<p style='color:green'>Nombre de produits: " . $count['count'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur: " . $e->getMessage() . "</p>";
}