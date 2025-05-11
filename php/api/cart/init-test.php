<?php
session_start();
require_once '../../config/database.php';

// Cette page est uniquement pour le test/développement
header('Content-Type: application/json');

// Produit test à ajouter au panier
$productId = 602; // Remplacez par un ID de produit existant dans votre table produits
$quantity = 1;
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$sessionId = session_id();

try {
    // Vérifie d'abord si le produit existe
    $stmt = $pdo->prepare("SELECT id, nom, stock FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        exit;
    }
    
    // Vérifie si le produit est déjà dans le panier
    $whereClause = $userId ? 'utilisateur_id = ?' : 'session_id = ?';
    $params = $userId ? [$userId, $productId] : [$sessionId, $productId];
    
    $stmt = $pdo->prepare("SELECT id, quantite FROM panier WHERE {$whereClause} AND produit_id = ?");
    $stmt->execute($params);
    $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingItem) {
        // Mise à jour de la quantité
        $stmt = $pdo->prepare("UPDATE panier SET quantite = quantite + ? WHERE id = ?");
        $stmt->execute([$quantity, $existingItem['id']]);
        $message = "Produit mis à jour dans le panier";
    } else {
        // Insertion d'un nouvel article
        $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, session_id, produit_id, quantite) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $sessionId, $productId, $quantity]);
        $message = "Produit ajouté au panier";
    }
    
    // Récupère le contenu actuel du panier
    $whereClause = $userId ? 'p.utilisateur_id = ?' : 'p.session_id = ?';
    $params = $userId ? [$userId] : [$sessionId];
    
    $stmt = $pdo->prepare("
        SELECT p.id as panier_id, p.produit_id, p.quantite, pr.nom, pr.prix 
        FROM panier p 
        JOIN produits pr ON p.produit_id = pr.id 
        WHERE {$whereClause}
    ");
    $stmt->execute($params);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true, 
        'message' => $message,
        'cartItems' => $cartItems,
        'sessionInfo' => [
            'userId' => $userId,
            'sessionId' => $sessionId
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}