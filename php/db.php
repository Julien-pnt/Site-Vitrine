<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'elixir_du_temps';
$username = 'root';
$password = '';

// Attempt to connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>