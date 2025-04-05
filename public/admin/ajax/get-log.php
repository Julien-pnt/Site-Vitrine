<?php
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';

// Vérification de l'authentification
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

// Vérification de l'ID du log
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID de log invalide']);
    exit;
}

$logId = intval($_GET['id']);

try {
    // Récupérer les détails du log
    $stmt = $pdo->prepare("
        SELECT l.*, 
               COALESCE(a.prenom, u.prenom, '') as user_firstname,
               COALESCE(a.nom, u.nom, '') as user_lastname,
               COALESCE(a.email, u.email, '') as user_email
        FROM system_logs l
        LEFT JOIN utilisateurs a ON l.user_id = a.id AND l.user_type = 'admin'
        LEFT JOIN utilisateurs u ON l.user_id = u.id AND l.user_type = 'customer'
        WHERE l.id = ?
    ");
    $stmt->execute([$logId]);
    
    $log = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$log) {
        echo json_encode(['error' => 'Log non trouvé']);
        exit;
    }
    
    // Retourner les données au format JSON
    echo json_encode($log);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
}