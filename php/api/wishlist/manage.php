<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour gérer vos favoris']);
    exit;
}

// Récupérer les données
$userId = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : null;
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;

// Connexion à la base de données
require_once '../../config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Vérifier que l'action est valide
if (!in_array($action, ['add', 'remove', 'clear', 'check'])) {
    echo json_encode(['success' => false, 'message' => 'Action non valide']);
    exit;
}

// Traiter les différentes actions
try {
    switch ($action) {
        case 'add':
            if (!$productId) {
                throw new Exception('ID produit manquant');
            }
            
            // Vérifier si le produit existe
            $stmt = $conn->prepare("SELECT id FROM produits WHERE id = ?");
            $stmt->execute([$productId]);
            if (!$stmt->fetch()) {
                throw new Exception('Produit introuvable');
            }
            
            // Vérifier si le produit est déjà en favoris
            $stmt = $conn->prepare("SELECT id FROM favoris WHERE utilisateur_id = ? AND produit_id = ?");
            $stmt->execute([$userId, $productId]);
            if ($stmt->fetch()) {
                // Déjà en favoris, on ne fait rien
                echo json_encode(['success' => true, 'message' => 'Produit déjà en favoris', 'status' => 'exists']);
                exit;
            }
            
            // Ajouter aux favoris
            $stmt = $conn->prepare("INSERT INTO favoris (utilisateur_id, produit_id) VALUES (?, ?)");
            $stmt->execute([$userId, $productId]);
            
            echo json_encode(['success' => true, 'message' => 'Produit ajouté aux favoris', 'status' => 'added']);
            break;
            
        case 'remove':
            if (!$productId) {
                throw new Exception('ID produit manquant');
            }
            
            // Supprimer des favoris
            $stmt = $conn->prepare("DELETE FROM favoris WHERE utilisateur_id = ? AND produit_id = ?");
            $stmt->execute([$userId, $productId]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Produit retiré des favoris', 'status' => 'removed']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Produit non trouvé dans les favoris', 'status' => 'not_found']);
            }
            break;
            
        case 'clear':
            // Vider tous les favoris
            $stmt = $conn->prepare("DELETE FROM favoris WHERE utilisateur_id = ?");
            $stmt->execute([$userId]);
            
            echo json_encode(['success' => true, 'message' => 'Liste de favoris vidée', 'status' => 'cleared']);
            break;
            
        case 'check':
            if (!$productId) {
                throw new Exception('ID produit manquant');
            }
            
            // Vérifier si le produit est en favoris
            $stmt = $conn->prepare("SELECT id FROM favoris WHERE utilisateur_id = ? AND produit_id = ?");
            $stmt->execute([$userId, $productId]);
            $isFavorite = $stmt->fetch() ? true : false;
            
            echo json_encode(['success' => true, 'is_favorite' => $isFavorite]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}