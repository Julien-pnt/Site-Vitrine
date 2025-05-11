<?php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
require_once '../../config/database.php';

// Initialiser la réponse
$response = [
    'success' => true,
    'cartCount' => 0,
    'cartTotal' => '0,00 €',
    'cartItems' => []
];

try {
    // Récupérer l'ID utilisateur ou session ID
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    // Construire la clause WHERE
    $whereClause = $userId ? 'p.utilisateur_id = ?' : 'p.session_id = ?';
    $whereValue = $userId ?: $sessionId;
    
    // Requête pour récupérer le panier
    $query = "SELECT p.id as panier_id, p.produit_id, p.quantite, 
                     pr.nom, pr.reference, pr.prix, pr.prix_promo, pr.image, 
                     pr.stock, pr.stock_alerte, pr.visible
              FROM panier p
              JOIN produits pr ON p.produit_id = pr.id
              WHERE $whereClause AND pr.visible = 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$whereValue]);
    
    $cartItems = [];
    $cartTotal = 0;
    $cartCount = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Calculer le prix (normal ou promo)
        $price = !empty($row['prix_promo']) ? $row['prix_promo'] : $row['prix'];
        
        // Créer l'objet article de panier
        $cartItem = [
            'id' => $row['produit_id'],
            'panier_id' => $row['panier_id'],
            'name' => $row['nom'],
            'reference' => $row['reference'] ?: "ELX-{$row['produit_id']}",
            'price' => floatval($price),
            'regularPrice' => floatval($row['prix']),
            'quantity' => intval($row['quantite']),
            'image' => $row['image'],
            'hasPromo' => !empty($row['prix_promo']) && $row['prix_promo'] < $row['prix']
        ];
        
        $cartItems[] = $cartItem;
        
        // Calculer les totaux
        $cartTotal += $price * $row['quantite'];
        $cartCount += $row['quantite'];
    }
    
    // Mettre à jour la réponse
    $response['cartCount'] = $cartCount;
    $response['cartTotal'] = number_format($cartTotal, 0, ',', ' ') . ' €';
    $response['cartItems'] = $cartItems;
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Erreur: ' . $e->getMessage();
}

echo json_encode($response);