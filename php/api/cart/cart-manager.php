<?php

// Fichier central de gestion du panier qui sera réutilisé par tous les autres API
session_start();
require_once '../../config/database.php';

/**
 * Récupère le panier d'un utilisateur à partir de la base de données
 */
function getCartFromDatabase() {
    global $pdo;
    
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    $params = [];
    $where = '';
    
    if ($userId) {
        $where = 'utilisateur_id = ?';
        $params[] = $userId;
    } else {
        $where = 'session_id = ?';
        $params[] = $sessionId;
    }
    
    $query = "SELECT p.panier_id, p.produit_id, p.quantite, 
                     pr.nom, pr.reference, pr.prix, pr.prix_promo, pr.image, 
                     pr.stock, pr.stock_alerte, pr.visible
              FROM panier p
              JOIN produits pr ON p.produit_id = pr.id
              WHERE $where AND pr.visible = 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    $cartItems = [];
    $cartTotal = 0;
    $cartCount = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $price = !empty($row['prix_promo']) ? $row['prix_promo'] : $row['prix'];
        $quantity = min($row['quantite'], $row['stock']); // Limiter à la quantité en stock
        
        if ($quantity > 0) {
            $cartItems[$row['produit_id']] = [
                'id' => $row['produit_id'],
                'panier_id' => $row['panier_id'],
                'name' => $row['nom'],
                'price' => floatval($price),
                'regularPrice' => floatval($row['prix']),
                'quantity' => $quantity,
                'image' => $row['image'],
                'reference' => $row['reference'] ?: "ELX-{$row['produit_id']}",
                'availableStock' => $row['stock'],
                'stockAlerte' => $row['stock_alerte'],
                'hasPromo' => !empty($row['prix_promo'])
            ];
            
            $cartTotal += $price * $quantity;
            $cartCount += $quantity;
        }
    }
    
    return [
        'items' => $cartItems,
        'total' => $cartTotal,
        'count' => $cartCount
    ];
}

/**
 * Ajoute ou met à jour un produit dans le panier
 */
function addOrUpdateCartItem($productId, $quantity = 1) {
    global $pdo;
    
    try {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $sessionId = $userId ? null : session_id();
        
        // Vérifier que le produit existe et a du stock
        $stmt = $pdo->prepare("SELECT id, stock, visible FROM produits WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product || !$product['visible']) {
            return [
                'success' => false,
                'message' => 'Produit non disponible'
            ];
        }
        
        if ($product['stock'] <= 0) {
            return [
                'success' => false,
                'message' => 'Produit en rupture de stock'
            ];
        }
        
        // Vérifier si l'article existe déjà dans le panier
        $stmt = $pdo->prepare("SELECT panier_id, quantite FROM panier 
                              WHERE produit_id = ? AND (utilisateur_id = ? OR session_id = ?)");
        $stmt->execute([$productId, $userId, $sessionId]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cartItem) {
            // Mise à jour de la quantité
            $newQuantity = min($cartItem['quantite'] + $quantity, $product['stock']);
            
            $stmt = $pdo->prepare("UPDATE panier SET quantite = ? WHERE panier_id = ?");
            $stmt->execute([$newQuantity, $cartItem['panier_id']]);
        } else {
            // Nouvel ajout
            $newQuantity = min($quantity, $product['stock']);
            
            $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, session_id, produit_id, quantite)
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $sessionId, $productId, $newQuantity]);
        }
        
        // Récupérer le nouveau panier
        $cart = getCartFromDatabase();
        
        return [
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'cart' => $cart
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erreur lors de l\'ajout au panier: ' . $e->getMessage()
        ];
    }
}

/**
 * Supprime un produit du panier
 */
function removeCartItem($productId) {
    global $pdo;
    
    try {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $sessionId = $userId ? null : session_id();
        
        $stmt = $pdo->prepare("DELETE FROM panier 
                              WHERE produit_id = ? AND (utilisateur_id = ? OR session_id = ?)");
        $stmt->execute([$productId, $userId, $sessionId]);
        
        // Récupérer le nouveau panier
        $cart = getCartFromDatabase();
        
        return [
            'success' => true,
            'message' => 'Produit supprimé du panier',
            'cart' => $cart
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
        ];
    }
}

/**
 * Met à jour la quantité d'un produit dans le panier
 */
function updateCartItemQuantity($productId, $change) {
    global $pdo;
    
    try {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $sessionId = $userId ? null : session_id();
        
        // Vérifier que le produit existe et a du stock
        $stmt = $pdo->prepare("SELECT id, stock, visible FROM produits WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product || !$product['visible']) {
            return [
                'success' => false,
                'message' => 'Produit non disponible'
            ];
        }
        
        // Récupérer l'article du panier
        $stmt = $pdo->prepare("SELECT panier_id, quantite FROM panier 
                              WHERE produit_id = ? AND (utilisateur_id = ? OR session_id = ?)");
        $stmt->execute([$productId, $userId, $sessionId]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cartItem) {
            return [
                'success' => false,
                'message' => 'Produit non trouvé dans le panier'
            ];
        }
        
        $newQuantity = $cartItem['quantite'] + $change;
        
        if ($newQuantity <= 0) {
            // Supprimer l'article si la quantité est 0 ou moins
            return removeCartItem($productId);
        } else {
            // Limiter à la quantité en stock
            $newQuantity = min($newQuantity, $product['stock']);
            
            $stmt = $pdo->prepare("UPDATE panier SET quantite = ? WHERE panier_id = ?");
            $stmt->execute([$newQuantity, $cartItem['panier_id']]);
            
            // Récupérer le nouveau panier
            $cart = getCartFromDatabase();
            
            return [
                'success' => true,
                'message' => 'Quantité mise à jour',
                'cart' => $cart
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
        ];
    }
}

/**
 * Migre le panier du localStorage vers la base de données
 */
function migrateCartFromLocalStorage($localCart) {
    global $pdo;
    
    try {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $sessionId = $userId ? null : session_id();
        
        // Parcourir chaque article du localStorage
        foreach ($localCart as $item) {
            $productId = $item['id'];
            $quantity = $item['quantity'];
            
            // Vérifier que le produit existe et a du stock
            $stmt = $pdo->prepare("SELECT id, stock, visible FROM produits WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product || !$product['visible'] || $product['stock'] <= 0) {
                continue; // Ignorer les produits non disponibles
            }
            
            // Vérifier si l'article existe déjà dans le panier
            $stmt = $pdo->prepare("SELECT panier_id, quantite FROM panier 
                                  WHERE produit_id = ? AND (utilisateur_id = ? OR session_id = ?)");
            $stmt->execute([$productId, $userId, $sessionId]);
            $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cartItem) {
                // Mise à jour de la quantité
                $newQuantity = min($cartItem['quantite'] + $quantity, $product['stock']);
                
                $stmt = $pdo->prepare("UPDATE panier SET quantite = ? WHERE panier_id = ?");
                $stmt->execute([$newQuantity, $cartItem['panier_id']]);
            } else {
                // Nouvel ajout
                $newQuantity = min($quantity, $product['stock']);
                
                $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, session_id, produit_id, quantite)
                                      VALUES (?, ?, ?, ?)");
                $stmt->execute([$userId, $sessionId, $productId, $newQuantity]);
            }
        }
        
        // Récupérer le nouveau panier
        $cart = getCartFromDatabase();
        
        return [
            'success' => true,
            'message' => 'Panier synchronisé avec succès',
            'cart' => $cart
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erreur lors de la synchronisation: ' . $e->getMessage()
        ];
    }
}