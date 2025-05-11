<?php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
require_once '../../config/database.php';

// Validation des données reçues
if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Données manquantes'
    ]);
    exit;
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Vérifier que le produit existe et est visible
$stmt = $pdo->prepare("SELECT id, nom, reference, prix, prix_promo, stock, stock_alerte, image, visible 
                     FROM produits 
                     WHERE id = ? AND visible = TRUE");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode([
        'success' => false,
        'message' => 'Produit non disponible'
    ]);
    exit;
}

// Vérifier le stock disponible
if ($product['stock'] <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Produit en rupture de stock'
    ]);
    exit;
}

// Initialiser le panier si nécessaire
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ajouter ou mettre à jour le produit dans le panier SESSION
if (isset($_SESSION['cart'][$product_id])) {
    // Vérifier que la nouvelle quantité ne dépasse pas le stock
    $newQuantity = $_SESSION['cart'][$product_id]['quantity'] + $quantity;
    if ($newQuantity > $product['stock']) {
        $newQuantity = $product['stock'];
    }
    $_SESSION['cart'][$product_id]['quantity'] = $newQuantity;
} else {
    // Déterminer le prix à utiliser
    $price = !empty($product['prix_promo']) ? $product['prix_promo'] : $product['prix'];
    
    // Nouveau produit dans le panier
    $_SESSION['cart'][$product_id] = [
        'id' => $product_id,
        'name' => $product['nom'],
        'reference' => $product['reference'] ?: "ELX-$product_id",
        'price' => floatval($price),
        'quantity' => min($quantity, $product['stock']),
        'image' => $product['image']
    ];
}

// NOUVEAU: Synchroniser avec la base de données
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$sessionId = session_id();

// Vérifier si le produit est déjà dans le panier en base de données
$whereClause = $userId ? 'utilisateur_id = ? AND produit_id = ?' : 'session_id = ? AND produit_id = ?';
$whereParams = $userId ? [$userId, $product_id] : [$sessionId, $product_id];

$stmt = $pdo->prepare("SELECT id, quantite FROM panier WHERE $whereClause");
$stmt->execute($whereParams);
$dbCartItem = $stmt->fetch(PDO::FETCH_ASSOC);

if ($dbCartItem) {
    // Mettre à jour la quantité en base de données
    $dbNewQuantity = min($_SESSION['cart'][$product_id]['quantity'], $product['stock']);
    $stmt = $pdo->prepare("UPDATE panier SET quantite = ?, date_ajout = NOW() WHERE id = ?");
    $stmt->execute([$dbNewQuantity, $dbCartItem['id']]);
} else {
    // Insérer le nouveau produit en base de données
    $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, session_id, produit_id, quantite, date_ajout) 
                         VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([
        $userId, 
        $sessionId, 
        $product_id, 
        min($_SESSION['cart'][$product_id]['quantity'], $product['stock'])
    ]);
}

// Calculer le total (partie existante)
$cartTotal = 0;
$cartCount = 0;
$cartContent = [];

foreach ($_SESSION['cart'] as $id => $item) {
    // Requête pour avoir les infos à jour
    $stmt = $pdo->prepare("SELECT prix, prix_promo, stock FROM produits WHERE id = ? AND visible = TRUE");
    $stmt->execute([$id]);
    $currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($currentProduct) {
        $currentPrice = !empty($currentProduct['prix_promo']) ? $currentProduct['prix_promo'] : $currentProduct['prix'];
        
        // Ajuster la quantité si nécessaire
        $currentQuantity = min($item['quantity'], $currentProduct['stock']);
        if ($currentQuantity <= 0) {
            unset($_SESSION['cart'][$id]);
            
            // NOUVEAU: Supprimer aussi de la base de données
            $stmt = $pdo->prepare("DELETE FROM panier WHERE $whereClause AND produit_id = ?");
            $stmt->execute(array_merge($whereParams, [$id]));
            
            continue;
        }
        
        $_SESSION['cart'][$id]['quantity'] = $currentQuantity;
        $cartTotal += $currentPrice * $currentQuantity;
        $cartCount += $currentQuantity;
        $cartContent[] = $_SESSION['cart'][$id];
        
        // NOUVEAU: Synchroniser avec la base de données
        $stmt = $pdo->prepare("UPDATE panier SET quantite = ? WHERE $whereClause AND produit_id = ?");
        $stmt->execute(array_merge([$currentQuantity], $whereParams, [$id]));
    } else {
        // Le produit n'est plus disponible
        unset($_SESSION['cart'][$id]);
        
        // NOUVEAU: Supprimer aussi de la base de données
        $stmt = $pdo->prepare("DELETE FROM panier WHERE $whereClause AND produit_id = ?");
        $stmt->execute(array_merge($whereParams, [$id]));
    }
}

// Stocker le compteur pour un accès facile
$_SESSION['cart_count'] = $cartCount;

echo json_encode([
    'success' => true,
    'message' => 'Produit ajouté au panier',
    'cartCount' => $cartCount,
    'cartTotal' => number_format($cartTotal, 2, ',', ' ') . ' €',
    'cartContent' => $cartContent,
    'productName' => $product['nom'],
    'productPrice' => floatval($product['prix']),
    'productPromoPrice' => $product['prix_promo'] ? floatval($product['prix_promo']) : null,
    'productImage' => $product['image'],
    'productReference' => $product['reference'],
    // NOUVEAU: Information de débogage
    'dbSync' => [
        'userId' => $userId,
        'sessionId' => $sessionId
    ]
]);