<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Récupérer les données envoyées
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id']) || !isset($data['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$userId = $data['user_id'];
$productId = $data['product_id'];

// Connexion à la base de données
require_once '../../config/database.php';
$db = new Database();
$conn = $db->getConnection();

try {
    // Supprimer le produit des favoris
    $stmt = $conn->prepare("DELETE FROM favoris WHERE utilisateur_id = ? AND produit_id = ?");
    $stmt->execute([$userId, $productId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucun favori trouvé']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>