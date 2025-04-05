<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\public\php\api\products\check-stock.php
header('Content-Type: application/json');
require_once '../../../../php/config/database.php';

// Vérifier si l'ID du produit est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'ID de produit requis']);
    exit;
}

$productId = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

try {
    // Récupérer les informations du produit depuis la table mise à jour
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
        
        echo json_encode([
            'success' => true,
            'id' => $product['id'],
            'nom' => $product['nom'],
            'reference' => $product['reference'],
            'slug' => $product['slug'],
            'prix' => $product['prix'],
            'prix_promo' => $product['prix_promo'],
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
    echo json_encode([
        'error' => 'Erreur de base de données: ' . $e->getMessage(),
        'stock' => 0
    ]);
}
?>