<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Récupérer l'id de l'utilisateur
$userId = $_SESSION['user_id'];

// Connexion à la base de données
require_once '../../config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Compter les favoris
try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM favoris WHERE utilisateur_id = ?");
    $stmt->execute([$userId]);
    $count = $stmt->fetchColumn();
    
    echo json_encode(['success' => true, 'count' => $count]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération du nombre de favoris']);
}