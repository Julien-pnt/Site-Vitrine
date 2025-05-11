<?php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
require_once '../../config/database.php';

// Ce script synchronise le contenu du localStorage avec la table panier
if (!isset($_POST['cart'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Aucune donnée de panier reçue'
    ]);
    exit;
}

$cart = json_decode($_POST['cart'], true);
if (!is_array($cart)) {
    echo json_encode([
        'success' => false,
        'message' => 'Format de panier invalide'
    ]);
    exit;
}

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$sessionId = session_id();

try {
    // Commencer une transaction
    $pdo->beginTransaction();
    
    // Supprimer d'abord tous les articles du panier
    $whereClause = $userId ? 'utilisateur_id = ?' : 'session_id = ?';
    $stmt = $pdo->prepare("DELETE FROM panier WHERE {$whereClause}");
    $stmt->execute([$userId ? $userId : $sessionId]);
    
    // Ajouter chaque article du localStorage
    foreach ($cart as $item) {
        if (!isset($item['id']) || !isset($item['quantity'])) {
            continue;
        }
        
        $productId = intval($item['id']);
        $quantity = intval($item['quantity']);
        
        // Vérifier que le produit existe et est disponible
        $stmt = $pdo->prepare("SELECT id, stock FROM produits WHERE id = ? AND visible = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product || $product['stock'] <= 0) {
            continue; // Ignorer les produits non disponibles
        }
        
        // Limiter la quantité au stock disponible
        $quantity = min($quantity, $product['stock']);
        
        // Insérer dans la table panier
        $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, session_id, produit_id, quantite) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $sessionId, $productId, $quantity]);
    }
    
    // Valider la transaction
    $pdo->commit();
    
    // Récupérer le panier mis à jour depuis la base de données
    $cartWhere = $userId ? 'p.utilisateur_id = ?' : 'p.session_id = ?';
    $cartParams = [$userId ? $userId : $sessionId];
    
    $query = "SELECT p.id as panier_id, p.produit_id, p.quantite, 
                     pr.nom, pr.reference, pr.prix, pr.prix_promo, pr.image, 
                     pr.stock, pr.stock_alerte, pr.visible
              FROM panier p
              JOIN produits pr ON p.produit_id = pr.id
              WHERE {$cartWhere} AND pr.visible = 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($cartParams);
    
    $cartItems = [];
    $cartTotal = 0;
    $cartCount = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $quantity = min($row['quantite'], $row['stock']);
        $price = !empty($row['prix_promo']) ? $row['prix_promo'] : $row['prix'];
        
        $cartItems[] = [
            'id' => $row['produit_id'],
            'panier_id' => $row['panier_id'],
            'name' => $row['nom'],
            'reference' => $row['reference'] ?: "ELX-{$row['produit_id']}",
            'price' => floatval($price),
            'regularPrice' => floatval($row['prix']),
            'quantity' => $quantity,
            'image' => $row['image'],
            'availableStock' => $row['stock'],
            'stockAlerte' => $row['stock_alerte'],
            'hasPromo' => !empty($row['prix_promo']) && $row['prix_promo'] < $row['prix']
        ];
        
        $cartTotal += $price * $quantity;
        $cartCount += $quantity;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Panier synchronisé avec succès',
        'cartCount' => $cartCount,
        'cartItems' => $cartItems,
        'debug' => [
            'userId' => $userId,
            'sessionId' => $sessionId
        ]
    ]);
    
} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    $pdo->rollBack();
    
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la synchronisation: ' . $e->getMessage()
    ]);
}