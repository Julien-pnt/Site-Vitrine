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

if (!isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$userId = $_SESSION['user_id'];
$productId = $data['product_id'];
$quantity = $data['quantity'];

// Connexion à la base de données
require_once '../../config/database.php';
$db = new Database();
$conn = $db->getConnection();

try {
    // Vérifier si le produit existe et est en stock
    $stmt = $conn->prepare("SELECT stock FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Produit introuvable']);
        exit;
    }
    
    if ($product['stock'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
        exit;
    }
    
    // Vérifier si le produit est déjà dans le panier
    $stmt = $conn->prepare("SELECT quantite FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
    $stmt->execute([$userId, $productId]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cartItem) {
        // Mettre à jour la quantité
        $newQuantity = $cartItem['quantite'] + $quantity;
        $stmt = $conn->prepare("UPDATE panier SET quantite = ? WHERE utilisateur_id = ? AND produit_id = ?");
        $stmt->execute([$newQuantity, $userId, $productId]);
    } else {
        // Ajouter le produit au panier
        $stmt = $conn->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $productId, $quantity]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>