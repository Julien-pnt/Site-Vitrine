<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\php\api\cart\sync_cart.php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
require_once '../../config/database.php';

try {
    // 1. Récupérer les informations utilisateur
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    // 2. Récupérer le panier en base de données
    $whereClause = $userId ? 'p.utilisateur_id = ?' : 'p.session_id = ?';
    $whereValue = $userId ?: $sessionId;
    
    $query = "SELECT p.id as panier_id, p.produit_id, p.quantite, 
                     pr.nom, pr.reference, pr.prix, pr.prix_promo, pr.image, pr.stock
              FROM panier p
              JOIN produits pr ON p.produit_id = pr.id
              WHERE $whereClause AND pr.visible = 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$whereValue]);
    $dbCartItems = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $price = !empty($row['prix_promo']) ? $row['prix_promo'] : $row['prix'];
        
        $dbCartItems[$row['produit_id']] = [
            'id' => $row['produit_id'],
            'panier_id' => $row['panier_id'],
            'name' => $row['nom'],
            'reference' => $row['reference'] ?: "ELX-{$row['produit_id']}",
            'price' => floatval($price),
            'quantity' => intval($row['quantite']),
            'image' => $row['image'],
            'stock' => $row['stock']
        ];
    }
    
    // 3. Initialiser le panier en session si nécessaire
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // 4. Fusion et synchronisation (priorité à la DB)
    $sessionCart = $_SESSION['cart'];
    $mergedCart = [];
    
    // Ajouter tous les éléments de la base de données
    foreach ($dbCartItems as $id => $item) {
        $mergedCart[$id] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'reference' => $item['reference'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'image' => $item['image']
        ];
    }
    
    // Ajouter les éléments de la session qui ne sont pas dans la base de données
    foreach ($sessionCart as $id => $item) {
        if (!isset($mergedCart[$id])) {
            // Vérifier si le produit existe toujours
            $stmt = $pdo->prepare("SELECT id, stock FROM produits WHERE id = ? AND visible = 1");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                $mergedCart[$id] = $item;
                
                // Ajouter à la base de données
                $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, session_id, produit_id, quantite, date_ajout) 
                                     VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$userId, $sessionId, $id, min($item['quantity'], $product['stock'])]);
            }
        }
    }
    
    // 5. Mettre à jour la session avec les données fusionnées
    $_SESSION['cart'] = $mergedCart;
    
    // 6. Calculer les totaux
    $cartTotal = 0;
    $cartCount = 0;
    $cartContent = [];
    
    foreach ($mergedCart as $id => $item) {
        $cartTotal += $item['price'] * $item['quantity'];
        $cartCount += $item['quantity'];
        $cartContent[] = $item;
    }
    
    $_SESSION['cart_count'] = $cartCount;
    
    // 7. Renvoyer les données du panier
    echo json_encode([
        'success' => true,
        'message' => 'Panier synchronisé',
        'cartCount' => $cartCount,
        'cartTotal' => number_format($cartTotal, 2, ',', ' ') . ' €',
        'cartContent' => $cartContent
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}