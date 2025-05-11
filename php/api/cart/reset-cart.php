<?php
session_start();
require_once '../../config/database.php';
header('Content-Type: application/json');

// Protection - n'autoriser que le localhost
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', 'localhost'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Cette opération n\'est autorisée qu\'en développement local'
    ]);
    exit;
}

try {
    // Option 1: Supprimer uniquement les articles du panier de la session actuelle
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    if ($userId) {
        $stmt = $pdo->prepare("DELETE FROM panier WHERE utilisateur_id = ?");
        $stmt->execute([$userId]);
        $deleted = $stmt->rowCount();
    } else {
        $stmt = $pdo->prepare("DELETE FROM panier WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $deleted = $stmt->rowCount();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Panier réinitialisé',
        'deleted_items' => $deleted,
        'session' => [
            'id' => session_id(),
            'user_id' => $userId
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}