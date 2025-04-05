<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\public\php\api\products\check-stock.php

// Définir les headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Essayer plusieurs chemins possibles pour trouver database.php
$found = false;
$possiblePaths = [
    // Chemin relatif
    __DIR__ . '/../../../config/database.php',
    // Chemin via document root
    $_SERVER['DOCUMENT_ROOT'] . '/php/config/database.php',
    // Autre chemin possible
    dirname(dirname(dirname(dirname(__DIR__)))) . '/php/config/database.php'
];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $found = true;
        break;
    }
}

if (!$found) {
    echo json_encode([
        'error' => 'Fichier de configuration introuvable',
        'paths_tested' => $possiblePaths,
        'stock' => 0
    ]);
    exit;
}

// Vérifier si $pdo existe
if (!isset($pdo)) {
    echo json_encode([
        'error' => 'Connexion à la base de données non établie',
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
    // Version debug - affiche les détails de l'erreur
    echo json_encode([
        'error' => 'Impossible de vérifier le stock. Veuillez réessayer.',
        'debug' => [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ],
        'stock' => 0
    ]);
    
    // Enregistrer aussi l'erreur dans les logs
    error_log('Erreur PDO dans check-stock.php: ' . $e->getMessage());
}
?>