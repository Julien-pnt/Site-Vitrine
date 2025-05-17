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
    if (!isset($_POST['product_id']) || !isset($_POST['change'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Paramètres manquants'
        ]);
        exit;
    }
    
    $productId = intval($_POST['product_id']);
    $change = intval($_POST['change']);
    
    // Récupérer l'ID utilisateur ou session ID
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    // Construire la clause WHERE
    $whereClause = $userId ? 'utilisateur_id = ?' : 'session_id = ?';
    $whereValue = $userId ?: $sessionId;
    
    // Récupérer les informations actuelles du panier
    $stmt = $pdo->prepare("SELECT p.id, p.quantite, pr.stock, pr.nom, pr.prix, pr.prix_promo 
                           FROM panier p
                           JOIN produits pr ON p.produit_id = pr.id
                           WHERE $whereClause AND p.produit_id = ?");
    $stmt->execute([$whereValue, $productId]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cartItem) {
        echo json_encode([
            'success' => false,
            'message' => 'Article non trouvé dans le panier'
        ]);
        exit;
    }
    
    // Calculer la nouvelle quantité
    $newQuantity = $cartItem['quantite'] + $change;
    $action = null;
    
    // Traiter selon la nouvelle quantité
    if ($newQuantity <= 0) {
        // Supprimer l'article
        $stmt = $pdo->prepare("DELETE FROM panier WHERE id = ?");
        $stmt->execute([$cartItem['id']]);
        $action = 'remove';
    } else {
        // Vérifier le stock disponible
        if ($newQuantity > $cartItem['stock']) {
            $newQuantity = $cartItem['stock'];
        }
        
        // Mettre à jour la quantité
        $stmt = $pdo->prepare("UPDATE panier SET quantite = ?, date_ajout = NOW() WHERE id = ?");
        $stmt->execute([$newQuantity, $cartItem['id']]);
    }
    
    // Récupérer le nouveau total du panier
    $stmt = $pdo->prepare("SELECT SUM(p.quantite * IFNULL(pr.prix_promo, pr.prix)) as total, 
                                  SUM(p.quantite) as count
                           FROM panier p
                           JOIN produits pr ON p.produit_id = pr.id
                           WHERE $whereClause");
    $stmt->execute([$whereValue]);
    $cartSummary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Préparer les informations de l'article modifié
    $itemPrice = !empty($cartItem['prix_promo']) ? $cartItem['prix_promo'] : $cartItem['prix'];
    
    // Renvoyer la réponse
    echo json_encode([
        'success' => true,
        'action' => $action,
        'productId' => $productId,
        'cartTotal' => floatval($cartSummary['total']),
        'cartCount' => intval($cartSummary['count']),
        'cartItems' => [
            [
                'id' => $productId,
                'quantity' => $action === 'remove' ? 0 : $newQuantity,
                'price' => floatval($itemPrice),
                'total' => $action === 'remove' ? 0 : floatval($itemPrice * $newQuantity)
            ]
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Erreur dans update-quantity.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue'
    ]);
}