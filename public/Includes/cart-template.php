<?php
// Récupérer les articles du panier
function getCartItems() {
    if (!isset($_SESSION['user_id'])) {
        return []; // Si l'utilisateur n'est pas connecté
    }
    
    global $conn; // Supposant que $conn est disponible dans l'environnement
    
    try {
        $userId = $_SESSION['user_id'];
        $stmt = $conn->prepare("
            SELECT p.id, p.nom, p.prix, p.prix_promo, p.image, c.quantite
            FROM panier c
            JOIN produits p ON c.produit_id = p.id
            WHERE c.utilisateur_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur de récupération du panier: " . $e->getMessage());
        return [];
    }
}

// Calculer le total du panier
function calculateCartTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $price = !empty($item['prix_promo']) ? $item['prix_promo'] : $item['prix'];
        $total += $price * $item['quantite'];
    }
    return $total; // Assurez-vous que cette ligne existe
}

// Récupérer les articles du panier
$cartItems = getCartItems();
$cartTotal = calculateCartTotal($cartItems);
$cartCount = array_sum(array_column($cartItems, 'quantite'));
?>

<div class="cart-dropdown">
    <div class="cart-dropdown-header">
        <h3>Mon Panier</h3>
        <button class="close-cart-dropdown">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <?php if (empty($cartItems)): ?>
        <div class="cart-dropdown-empty">
            <i class="fas fa-shopping-cart"></i>
            <p>Votre panier est vide</p>
        </div>
    <?php else: ?>
        <div class="cart-dropdown-items">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
                    <div class="cart-item-image">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($item['image'])); ?>" 
                                 alt="<?php echo htmlspecialchars($item['nom']); ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="cart-item-details">
                        <h4 class="cart-item-title"><?php echo htmlspecialchars($item['nom']); ?></h4>
                        <div class="cart-item-price">
                            <?php if (!empty($item['prix_promo'])): ?>
                                <span class="price-current"><?php echo number_format($item['prix_promo'], 0, ',', ' '); ?> €</span>
                                <span class="price-old"><?php echo number_format($item['prix'], 0, ',', ' '); ?> €</span>
                            <?php else: ?>
                                <span class="price-current"><?php echo number_format($item['prix'], 0, ',', ' '); ?> €</span>
                            <?php endif; ?>
                        </div>
                        <div class="cart-item-quantity">
                            <button class="quantity-btn decrease" data-product-id="<?php echo $item['id']; ?>">-</button>
                            <span class="quantity-value"><?php echo $item['quantite']; ?></span>
                            <button class="quantity-btn increase" data-product-id="<?php echo $item['id']; ?>">+</button>
                        </div>
                    </div>
                    <button class="remove-cart-item" data-product-id="<?php echo $item['id']; ?>">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-dropdown-total">
            <span>Total:</span>
            <span class="cart-dropdown-total-value"><?php echo number_format($cartTotal, 0, ',', ' '); ?> €</span>
        </div>
        
        <div class="cart-dropdown-buttons">
            <a href="<?php echo $relativePath; ?>/pages/products/panier.php" class="cart-dropdown-button secondary">
                <i class="fas fa-shopping-cart"></i> Voir le panier
            </a>
            <a href="<?php echo $relativePath; ?>/pages/products/checkout.php" class="cart-dropdown-button primary">
                <i class="fas fa-credit-card"></i> Commander
            </a>
        </div>
    <?php endif; ?>
    
    <?php if (empty($cartItems)): ?>
        <div class="cart-dropdown-buttons single">
            <a href="<?php echo $relativePath; ?>/pages/products/Montres.php" class="cart-dropdown-button primary">
                <i class="fas fa-search"></i> Découvrir nos montres
            </a>
        </div>
    <?php endif; ?>
</div>