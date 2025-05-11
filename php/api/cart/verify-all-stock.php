<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\php\api\cart\verify-all-stock.php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
require_once '../../config/database.php';

// Initialiser la réponse
$response = [
    'success' => true,
    'hasAdjustments' => false,
    'message' => '',
    'adjustments' => []
];

// Vérifier que le panier existe
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $response['message'] = 'Votre panier est vide';
    echo json_encode($response);
    exit;
}

// Récupérer les IDs des produits dans le panier
$productIds = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));

// Récupérer les informations à jour pour tous les produits
$stmt = $pdo->prepare("SELECT id, nom, stock, visible FROM produits WHERE id IN ($placeholders)");
$stmt->execute($productIds);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Indexer les produits par ID
$productsById = [];
foreach ($products as $product) {
    $productsById[$product['id']] = $product;
}

// Messages d'ajustement
$adjustmentMessages = [];
$totalAdjustments = 0;

// Vérifier chaque produit dans le panier
foreach ($_SESSION['cart'] as $productId => $item) {
    // Vérifier si le produit existe et est visible
    if (!isset($productsById[$productId]) || !$productsById[$productId]['visible']) {
        // Produit non disponible, le retirer du panier
        unset($_SESSION['cart'][$productId]);
        $adjustmentMessages[] = "\"" . ($productsById[$productId]['nom'] ?? "Produit #$productId") . "\" a été retiré car il n'est plus disponible.";
        $totalAdjustments++;
        continue;
    }
    
    // Vérifier le stock
    $product = $productsById[$productId];
    if ($product['stock'] <= 0) {
        // Produit en rupture de stock, le retirer du panier
        unset($_SESSION['cart'][$productId]);
        $adjustmentMessages[] = "\"" . $product['nom'] . "\" a été retiré car il est en rupture de stock.";
        $totalAdjustments++;
    } else if ($item['quantity'] > $product['stock']) {
        // Quantité demandée supérieure au stock disponible, ajuster
        $_SESSION['cart'][$productId]['quantity'] = $product['stock'];
        $adjustmentMessages[] = "La quantité de \"" . $product['nom'] . "\" a été ajustée à " . $product['stock'] . ".";
        $totalAdjustments++;
    }
}

// Mettre à jour le compteur
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'];
}
$_SESSION['cart_count'] = $cartCount;

// Finaliser la réponse
if ($totalAdjustments > 0) {
    $response['hasAdjustments'] = true;
    $response['message'] = implode(' ', $adjustmentMessages);
    $response['adjustments'] = $adjustmentMessages;
}

echo json_encode($response);