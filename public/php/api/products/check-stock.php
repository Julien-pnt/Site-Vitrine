<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\public\php\api\products\check-stock.php

// Définir les headers pour éviter les problèmes CORS et le type de contenu
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Connexion à la base de données - vérifier que le chemin est correct
try {
    require_once '../../../../php/config/database.php';
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Erreur de configuration: Impossible de charger la connexion à la base de données',
        'details' => $e->getMessage(),
        'stock' => 0
    ]);
    exit;
}

// Vérifier si l'ID du produit est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'ID de produit requis']);
    exit;
}

// Nettoyage et validation de l'ID
$productId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if ($productId === false || $productId <= 0) {
    echo json_encode(['error' => 'ID de produit invalide']);
    exit;
}

try {
    // Récupérer les informations du produit avec une requête préparée
    $stmt = $pdo->prepare("
        SELECT 
            id, nom, reference, slug, prix, prix_promo, 
            stock, stock_alerte, visible, 
            description_courte, image
        FROM produits 
        WHERE id = ?
    ");
    
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        // Vérifier si le produit est visible/disponible
        if (!$product['visible']) {
            echo json_encode([
                'error' => 'Ce produit n\'est plus disponible',
                'visible' => false,
                'stock' => 0
            ]);
            exit;
        }
        
        // Vérifier si le stock est suffisant
        if ((int)$product['stock'] <= 0) {
            echo json_encode([
                'error' => 'Ce produit est actuellement en rupture de stock',
                'visible' => true,
                'stock' => 0
            ]);
            exit;
        }
        
        echo json_encode([
            'success' => true,
            'id' => $product['id'],
            'nom' => $product['nom'],
            'reference' => $product['reference'],
            'slug' => $product['slug'],
            'prix' => (float)$product['prix'],
            'prix_promo' => $product['prix_promo'] ? (float)$product['prix_promo'] : null,
            'stock' => (int)$product['stock'],
            'stock_alerte' => (int)$product['stock_alerte'],
            'visible' => (bool)$product['visible'],
            'image' => $product['image'],
            'description_courte' => $product['description_courte']
        ]);
    } else {
        echo json_encode([
            'error' => 'Produit non trouvé',
            'stock' => 0
        ]);
    }
} catch (PDOException $e) {
    // Log l'erreur pour l'administrateur mais ne pas exposer les détails techniques à l'utilisateur
    error_log('Erreur dans check-stock.php: ' . $e->getMessage());
    
    echo json_encode([
        'error' => 'Impossible de vérifier le stock. Veuillez réessayer.',
        'stock' => 0
    ]);
}
?>