<?php
// Démarrer la session avant tout
session_start();

// Forcer l'en-tête JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Bloquer l'affichage des erreurs PHP directement
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
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
    
    // Supprimer du panier en session
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
    
    // Supprimer de la base de données
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    $whereClause = $userId ? 'utilisateur_id = ? AND produit_id = ?' : 'session_id = ? AND produit_id = ?';
    $whereParams = $userId ? [$userId, $productId] : [$sessionId, $productId];
    
    $stmt = $pdo->prepare("DELETE FROM panier WHERE $whereClause");
    $stmt->execute($whereParams);
    
    // Recalculer les totaux
    $cartTotal = 0;
    $cartCount = 0;
    
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        foreach ($_SESSION['cart'] as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
            $cartCount += $item['quantity'];
        }
    }
    
    $_SESSION['cart_count'] = $cartCount;
    
    echo json_encode([
        'success' => true,
        'message' => 'Produit supprimé du panier',
        'cartCount' => $cartCount,
        'cartTotal' => number_format($cartTotal, 2, ',', ' ') . ' €'
    ]);
    
} catch (Exception $e) {
    // Renvoyer l'erreur en JSON
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}