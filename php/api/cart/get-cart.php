<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\php\api\cart\get-cart.php
// Démarrer la session avant tout
session_start();

// Forcer l'en-tête JSON et empêcher la mise en cache
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Bloquer l'affichage des erreurs PHP directement (pour éviter de corrompre le JSON)
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    // Connexion à la base de données
    require_once '../../config/database.php';

    // Récupérer l'ID utilisateur ou session ID
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();

    // Déboguer les valeurs de session
    $debug = [
        'userId' => $userId,
        'sessionId' => $sessionId
    ];
    
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
    
    // Renvoyer la réponse JSON
    echo json_encode([
        'success' => true,
        'cartCount' => $cartCount,
        'cartTotal' => number_format($cartTotal, 0, ',', ' ') . ' €',
        'cartContent' => $cartItems,
        'debug' => $debug
    ]);
    
} catch (Exception $e) {
    // Renvoyer l'erreur en JSON
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}