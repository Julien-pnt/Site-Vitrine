<?php
// Démarrer la session avant tout
session_start();

// Forcer l'en-tête JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Log des requêtes pour débogage
file_put_contents(__DIR__ . '/update_log.txt', 
    date('Y-m-d H:i:s') . ' - ' . 
    'POST: ' . print_r($_POST, true) . ' - ' .
    'SESSION: ' . print_r($_SESSION, true) . "\n", 
    FILE_APPEND);

// Bloquer l'affichage des erreurs PHP directement
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    // Connexion à la base de données
    require_once '../../config/database.php';
    
    // Vérifier les paramètres
    if (!isset($_POST['product_id']) || !isset($_POST['change'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Paramètres manquants'
        ]);
        exit;
    }
    
    $productId = intval($_POST['product_id']);
    $change = intval($_POST['change']);
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    // Récupérer les informations du produit
    $stmt = $pdo->prepare("SELECT stock FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Produit non trouvé'
        ]);
        exit;
    }
    
    // Construire la clause WHERE
    $whereClause = $userId ? 'produit_id = ? AND utilisateur_id = ?' : 'produit_id = ? AND session_id = ?';
    $whereParams = $userId ? [$productId, $userId] : [$productId, $sessionId];
    
    // Récupérer l'article du panier
    $stmt = $pdo->prepare("SELECT id, quantite FROM panier WHERE $whereClause");
    $stmt->execute($whereParams);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cartItem) {
        echo json_encode([
            'success' => false,
            'message' => 'Article non trouvé dans votre panier'
        ]);
        exit;
    }
    
    // Calculer la nouvelle quantité
    $newQuantity = $cartItem['quantite'] + $change;
    
    // Vérifier les limites
    if ($newQuantity <= 0) {
        // Supprimer l'article du panier
        $stmt = $pdo->prepare("DELETE FROM panier WHERE id = ?");
        $stmt->execute([$cartItem['id']]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Article supprimé du panier'
        ]);
    } else {
        // Limiter à la quantité en stock
        $newQuantity = min($newQuantity, $product['stock']);
        
        // Mettre à jour la quantité
        $stmt = $pdo->prepare("UPDATE panier SET quantite = ? WHERE id = ?");
        $stmt->execute([$newQuantity, $cartItem['id']]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Quantité mise à jour'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}