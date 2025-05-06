<?php
// Initialisation de la session et vérification d'authentification admin
session_start();
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';

// Vérifier si l'utilisateur est authentifié et admin
if (!isLoggedIn() || !isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer et décoder les données JSON
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Vérifier si l'ID du produit et le statut sont présents
if (!isset($data['id']) || !is_numeric($data['id']) || !isset($data['visible'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$productId = (int)$data['id'];
$visible = (int)$data['visible'] ? 1 : 0;

// Connexion à la base de données
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit;
}

try {
    // Récupérer d'abord le nom du produit pour le log
    $stmt = $pdo->prepare("SELECT nom FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        exit;
    }
    
    // Mettre à jour le statut du produit
    $stmt = $pdo->prepare("UPDATE produits SET visible = ? WHERE id = ?");
    $result = $stmt->execute([$visible, $productId]);
    
    if ($result) {
        // Journaliser l'action
        $action = $visible ? 'activate_product' : 'deactivate_product';
        $details = ($visible ? 'Activation' : 'Désactivation') . ' du produit: ' . $product['nom'] . ' (ID: ' . $productId . ')';
        
        $logStmt = $pdo->prepare("INSERT INTO admin_logs (utilisateur_id, action, ip_address, details) VALUES (?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'], 
            $action, 
            $_SERVER['REMOTE_ADDR'],
            $details
        ]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => $visible ? 'Produit activé avec succès' : 'Produit désactivé avec succès']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification du statut']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>