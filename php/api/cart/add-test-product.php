<?php
session_start();
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Récupérer un produit aléatoire dans la base de données
    $stmt = $pdo->query("SELECT id, nom, prix FROM produits WHERE stock > 0 AND visible = 1 ORDER BY RAND() LIMIT 1");
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Aucun produit disponible'
        ]);
        exit;
    }
    
    // Récupérer l'ID utilisateur ou session ID
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    // Vérifier si le produit est déjà dans le panier
    $whereClause = $userId ? 'utilisateur_id = ? AND produit_id = ?' : 'session_id = ? AND produit_id = ?';
    $whereValue = $userId ? [$userId, $product['id']] : [$sessionId, $product['id']];
    
    $stmt = $pdo->prepare("SELECT id, quantite FROM panier WHERE {$whereClause}");
    $stmt->execute($whereValue);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cartItem) {
        // Mettre à jour la quantité
        $stmt = $pdo->prepare("UPDATE panier SET quantite = quantite + 1 WHERE id = ?");
        $stmt->execute([$cartItem['id']]);
        $message = "Produit mis à jour dans le panier";
    } else {
        // Ajouter un nouvel article
        $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, session_id, produit_id, quantite) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $sessionId, $product['id'], 1]);
        $message = "Produit ajouté au panier";
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'product' => $product,
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