<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\php\api\cart\add-item.php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
require_once '../../config/database.php';

// Vérifier les paramètres
if (!isset($_POST['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID produit manquant'
    ]);
    exit;
}

$productId = intval($_POST['product_id']);
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$sessionId = session_id();

try {
    // Vérifier que le produit existe et est disponible
    $stmt = $pdo->prepare("SELECT id, nom, reference, prix, prix_promo, stock, stock_alerte, image, visible 
                          FROM produits WHERE id = ? AND visible = 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Produit non disponible'
        ]);
        exit;
    }
    
    if ($product['stock'] <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Produit en rupture de stock'
        ]);
        exit;
    }
    
    // Vérifier si le produit est déjà dans le panier
    $whereClause = '';
    $params = [];
    
    if ($userId) {
        $whereClause = 'utilisateur_id = ? AND produit_id = ?';
        $params = [$userId, $productId];
    } else {
        $whereClause = 'session_id = ? AND produit_id = ?';
        $params = [$sessionId, $productId];
    }
    
    $stmt = $pdo->prepare("SELECT id, quantite FROM panier WHERE {$whereClause}");
    $stmt->execute($params);
    $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingItem) {
        // Mettre à jour la quantité
        $newQuantity = min($existingItem['quantite'] + $quantity, $product['stock']);
        
        $stmt = $pdo->prepare("UPDATE panier SET quantite = ? WHERE id = ?");
        $stmt->execute([$newQuantity, $existingItem['id']]);
    } else {
        // Ajouter un nouvel article
        $newQuantity = min($quantity, $product['stock']);
        
        $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, session_id, produit_id, quantite) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $sessionId, $productId, $newQuantity]);
    }
    
    // Récupérer le panier mis à jour
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
        if ($row['stock'] <= 0) continue;
        
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
        'message' => 'Produit ajouté au panier',
        'cartCount' => $cartCount,
        'cartTotal' => number_format($cartTotal, 2, ',', ' ') . ' €',
        'cart' => [
            'items' => $cartItems,
            'count' => $cartCount,
            'total' => $cartTotal
        ],
        'productName' => $product['nom'],
        'productPrice' => floatval($product['prix']),
        'productPromoPrice' => $product['prix_promo'] ? floatval($product['prix_promo']) : null,
        'productImage' => $product['image'],
        'productReference' => $product['reference'],
        'debug' => [
            'userId' => $userId,
            'sessionId' => $sessionId
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'ajout au panier: ' . $e->getMessage()
    ]);
}