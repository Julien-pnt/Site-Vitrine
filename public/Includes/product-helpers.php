<?php
// Utiliser la classe Database depuis le bon chemin
require_once __DIR__ . '/../../php/config/database.php';

// Fonction pour récupérer les informations de stock d'un produit
function getProductStockInfo($productId) {
    // Utilisation de la classe Database
    $database = new Database();
    $pdo = $database->getConnection();
    
    $query = "SELECT stock, stock_alerte FROM produits WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $productId]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        // Valeurs par défaut si le produit n'est pas trouvé
        return [
            'stock' => 0,
            'stock_alerte' => 5,
            'status' => 'out-of-stock',
            'label' => 'Rupture de stock',
            'icon' => 'fa-times-circle',
            'available' => false
        ];
    }
    
    // Déterminer le statut du stock
    if ($result['stock'] <= 0) {
        $status = 'out-of-stock';
        $label = 'Rupture de stock';
        $icon = 'fa-times-circle';
        $available = false;
    } elseif ($result['stock'] < $result['stock_alerte']) {
        $status = 'low-stock';
        $label = 'Stock limité';
        $icon = 'fa-exclamation-circle';
        $available = true;
    } else {
        $status = 'in-stock';
        $label = 'En stock';
        $icon = 'fa-check-circle';
        $available = true;
    }
    
    return [
        'stock' => $result['stock'],
        'stock_alerte' => $result['stock_alerte'],
        'status' => $status,
        'label' => $label,
        'icon' => $icon,
        'available' => $available
    ];
}

// Fonction pour générer l'indicateur de stock HTML
function generateStockIndicator($productId) {
    $stockInfo = getProductStockInfo($productId);
    
    return '<div class="stock-indicator ' . $stockInfo['status'] . '">
        <i class="fas ' . $stockInfo['icon'] . '"></i> ' . $stockInfo['label'] . '
    </div>';
}

// Fonction pour vérifier si un produit est disponible
function isProductAvailable($productId) {
    $stockInfo = getProductStockInfo($productId);
    return $stockInfo['available'];
}

// Fonction optimisée pour charger les stocks de plusieurs produits en une seule requête
function loadProductsStockBatch($productIds) {
    if (empty($productIds)) {
        return [];
    }
    
    // Utilisation de la classe Database
    $database = new Database();
    $pdo = $database->getConnection();
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $query = "SELECT id, stock, stock_alerte FROM produits WHERE id IN ($placeholders)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(array_values($productIds)); // Assurez-vous que les valeurs sont dans un tableau indexé
    
    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        
        // Déterminer le statut du stock
        if ($row['stock'] <= 0) {
            $status = 'out-of-stock';
            $label = 'Rupture de stock';
            $icon = 'fa-times-circle';
            $available = false;
        } elseif ($row['stock'] < $row['stock_alerte']) {
            $status = 'low-stock';
            $label = 'Stock limité';
            $icon = 'fa-exclamation-circle';
            $available = true;
        } else {
            $status = 'in-stock';
            $label = 'En stock';
            $icon = 'fa-check-circle';
            $available = true;
        }
        
        $results[$id] = [
            'stock' => $row['stock'],
            'stock_alerte' => $row['stock_alerte'],
            'status' => $status,
            'label' => $label,
            'icon' => $icon,
            'available' => $available
        ];
    }
    
    return $results;
}

// Générer l'indicateur de stock pour un produit à partir des données déjà chargées
function generateStockIndicatorFromData($stockInfo) {
    return '<div class="stock-indicator ' . $stockInfo['status'] . '">
        <i class="fas ' . $stockInfo['icon'] . '"></i> ' . $stockInfo['label'] . '
    </div>';
}

// Fonction pour récupérer les informations du produit (nom, prix, etc.)
function getProductInfo($productId) {
    // Utilisation de la classe Database
    $database = new Database();
    $pdo = $database->getConnection();
    
    $query = "SELECT id, nom, prix, prix_promo, nouveaute FROM produits WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $productId]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        // Valeurs par défaut si le produit n'est pas trouvé
        return [
            'id' => $productId,
            'nom' => 'Produit non trouvé',
            'prix' => 0,
            'prix_promo' => null,
            'nouveaute' => false
        ];
    }
    
    return $result;
}

// Fonction pour récupérer les informations de plusieurs produits en une seule requête
function getProductsInfoBatch($productIds) {
    if (empty($productIds)) {
        return [];
    }
    
    // Utilisation de la classe Database
    $database = new Database();
    $pdo = $database->getConnection();
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $query = "SELECT id, nom, prix, prix_promo, nouveaute FROM produits WHERE id IN ($placeholders)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(array_values($productIds));
    
    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[$row['id']] = $row;
    }
    
    return $results;
}

// Fonction pour formater le prix correctement
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' €';
}
?>