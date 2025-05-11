<?php

// Définir le chemin relatif si non défini
if (!isset($relativePath)) {
    $relativePath = ".."; // Par défaut, remontez d'un niveau
}

// Définir le titre de la page si non défini
if (!isset($pageTitle)) {
    $pageTitle = "Elixir du Temps - Montres de luxe";
}

// Déterminer la page active pour le menu
$currentPage = basename($_SERVER['PHP_SELF']);

// Fonction améliorée pour obtenir les éléments du panier depuis la table panier
function getSimpleCartItems() {
   
    
    // Connexion à la base de données pour obtenir les données à jour
    global $pdo;
    if (!isset($pdo)) {
        try {
            require_once __DIR__ . '/../../php/config/database.php';
        } catch (Exception $e) {
            error_log("Erreur lors de la connexion à la base de données: " . $e->getMessage());
            return []; // Retourner un panier vide en cas d'erreur
        }
    }
    
    try {
        // Récupérer l'ID utilisateur ou session ID
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $sessionId = session_id();
        
        // Pour débogage
        error_log("Session ID: $sessionId, User ID: " . ($userId ?: 'non connecté'));
        
        // Construire la clause WHERE
        $whereClause = $userId ? 'p.utilisateur_id = ?' : 'p.session_id = ?';
        $whereValue = $userId ?: $sessionId;
        
        // Requête pour récupérer le panier avec les informations produit
        $query = "SELECT p.id as panier_id, p.produit_id as id, p.quantite, 
                         pr.nom, pr.reference, pr.prix, pr.prix_promo, pr.image, 
                         pr.stock, pr.stock_alerte, pr.visible
                  FROM panier p
                  JOIN produits pr ON p.produit_id = pr.id
                  WHERE $whereClause AND pr.visible = 1";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$whereValue]);
        
        $cartItems = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Ignorer les produits sans stock
            if ($row['stock'] <= 0) continue;
            
            // Ajuster la quantité au stock disponible
            $quantity = min($row['quantite'], $row['stock']);
            
            // Stocker l'article avec toutes les informations nécessaires
            $cartItems[$row['id']] = [
                'id' => $row['id'],
                'panier_id' => $row['panier_id'],
                'nom' => $row['nom'],
                'reference' => $row['reference'] ?: "ELX-{$row['id']}",
                'prix' => $row['prix'],
                'prix_promo' => $row['prix_promo'],
                'quantite' => $quantity,
                'image' => $row['image'],
                'stock' => $row['stock'],
                'stock_alerte' => $row['stock_alerte']
            ];
        }
        
        return $cartItems;
        
    } catch (PDOException $e) {
        // En cas d'erreur, logger et retourner un panier vide
        error_log("Erreur lors de la récupération du panier: " . $e->getMessage());
        return [];
    }
}

$cartItems = getSimpleCartItems();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Elixir du Temps - Découvrez notre collection de montres de luxe, alliant tradition et innovation.'; ?>">
    <meta name="base-url" content="<?php echo $relativePath; ?>">
    
    <!-- Ressources CSS -->
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/cart.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/notifications.css">
    <?php if (isset($additionalCss)) echo $additionalCss; ?>
    
    <link rel="shortcut icon" href="<?php echo $relativePath; ?>/assets/img/layout/icon2.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <title><?php echo $pageTitle; ?></title>
    
    <?php if (isset($additionalHead)) echo $additionalHead; ?>
    
    <script src="<?php echo $relativePath; ?>/assets/js/cart-functions.js"></script>
    <script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
    <script src="<?php echo $relativePath; ?>/assets/js/cart-common.js"></script>
    <script src="<?php echo $relativePath; ?>/assets/js/add-to-cart.js"></script>
</head>
<body>
    <!-- Header section -->
    <header class="header">
        <div class="logo-container">
            <a href="<?php echo $relativePath; ?>/pages/Accueil.php" aria-label="Accueil Elixir du Temps">
                <img src="<?php echo $relativePath; ?>/assets/img/layout/logo.png" alt="Logo Elixir du Temps" class="logo" width="180" height="60">
            </a>
        </div>
        
        <nav aria-label="Navigation principale">
            <ul class="menu-bar">
                <li><a href="<?php echo $relativePath; ?>/pages/Accueil.php" <?php if($currentPage == 'Accueil.php') echo 'class="active"'; ?>>Accueil</a></li>
                <li><a href="<?php echo $relativePath; ?>/pages/collections/Collections.php" <?php if($currentPage == 'Collections.php') echo 'class="active"'; ?>>Collections</a></li>
                <li><a href="<?php echo $relativePath; ?>/pages/products/Montres.php" <?php if($currentPage == 'Montres.php') echo 'class="active"'; ?>>Montres</a></li>
                <li><a href="<?php echo $relativePath; ?>/pages/APropos.php" <?php if($currentPage == 'APropos.php') echo 'class="active"'; ?>>À propos</a></li>
                <li><a href="<?php echo $relativePath; ?>/pages/Contact.php" <?php if($currentPage == 'Contact.php') echo 'class="active"'; ?>>Contact</a></li>
            </ul>
        </nav>
        
        <!-- User and Cart Icons -->
        <div class="user-cart-container">
            <!-- User dropdown menu -->
            <div class="user-menu-container">
                <div class="user-icon-wrapper">
                    <i class="fas fa-user"></i>
                    <span class="user-menu-arrow">▼</span>
                </div>
                
                <div class="user-dropdown">
                    <!-- Options pour utilisateurs non connectés -->
                    <div class="guest-options">
                        <a href="<?php echo $relativePath; ?>/pages/auth/login.php" class="dropdown-item">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </a>
                        <a href="<?php echo $relativePath; ?>/pages/auth/register.php" class="dropdown-item">
                            <i class="fas fa-user-plus"></i> S'inscrire
                        </a>
                    </div>
                    
                    <!-- Options pour utilisateurs connectés -->
                    <div class="user-options" style="display: none;">
                        <a href="<?php echo $relativePath; ?>/user/index.php" class="dropdown-item">
                            <i class="fas fa-user-circle"></i> Mon compte
                        </a>
                        <a href="<?php echo $relativePath; ?>/user/orders.php" class="dropdown-item">
                            <i class="fas fa-box"></i> Mes commandes
                        </a>
                        <a href="<?php echo $relativePath; ?>/user/wishlist.php" class="dropdown-item">
                            <i class="fas fa-heart"></i> Mes favoris
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo $relativePath; ?>/php/api/auth/logout.php" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Cart Icon avec compteur -->
            <div class="cart-wrapper">
                <div class="cart-icon" id="cart-icon">
                    <button class="nav-icon toggle-cart" aria-label="Panier">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        $cartItemCount = 0;
                        foreach ($cartItems as $item) {
                            $cartItemCount += $item['quantite'];
                        }
                        ?>
                        <span class="cart-badge" id="cart-count"><?php echo $cartItemCount; ?></span>
                    </button>
                </div>
                
                <!-- Contenu du panier dropdown -->
                <div class="cart-dropdown">
                    <div class="cart-dropdown-header">
                        <span class="cart-dropdown-title">Mon panier</span>
                        <button class="close-cart-dropdown" aria-label="Fermer le panier">&times;</button>
                    </div>
                    
                    <!-- Message panier vide - toujours présent, affiché/masqué via JS -->
                    <div class="cart-dropdown-empty" <?php if (!empty($cartItems)): ?>style="display: none;"<?php endif; ?>>
                        <i class="fas fa-shopping-cart"></i>
                        <p>Votre panier est vide</p>
                    </div>
                    
                    <!-- Conteneur pour les articles - sera rempli dynamiquement par JS -->
                    <div class="cart-dropdown-items" <?php if (empty($cartItems)): ?>style="display: none;"<?php endif; ?>>
                        <?php if (!empty($cartItems)): ?>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
                                    <div class="cart-item-image">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($item['image'])); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['nom']); ?>">
                                        <?php else: ?>
                                            <div class="no-image"><i class="fas fa-image"></i></div>
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
                                            <button class="remove-cart-item" data-product-id="<?php echo $item['id']; ?>">×</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Pied du panier toujours présent -->
                    <div class="cart-dropdown-footer" <?php if (empty($cartItems)): ?>style="display: none;"<?php endif; ?>>
                        <div class="cart-total">
                            <span>Total:</span>
                            <span id="cart-dropdown-total">
                                <?php
                                $total = 0;
                                if (!empty($cartItems)) {
                                    foreach ($cartItems as $item) {
                                        $price = !empty($item['prix_promo']) ? $item['prix_promo'] : $item['prix'];
                                        $total += $price * $item['quantite'];
                                    }
                                }
                                echo number_format($total, 0, ',', ' ') . ' €';
                                ?>
                            </span>
                        </div>
                        
                        <div class="cart-buttons">
                            <a href="<?php echo $relativePath; ?>/pages/products/panier.php" class="cart-button secondary">Voir le panier</a>
                            <a href="<?php echo $relativePath; ?>/pages/products/checkout.php" class="cart-button primary">Commander</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Chargement des scripts dans le bon ordre -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser les gestionnaires d'événements pour les boutons du panier
            if (window.cartFunctions) {
                window.cartFunctions.initCartButtonHandlers();
            }
            
            // Ouvrir/fermer le dropdown du panier
            const cartIcon = document.querySelector('.toggle-cart');
            const cartDropdown = document.querySelector('.cart-dropdown');
            const closeCartButton = document.querySelector('.close-cart-dropdown');
            
            if (cartIcon && cartDropdown) {
                cartIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    cartDropdown.classList.toggle('active');
                    
                    // Fermer le menu utilisateur s'il est ouvert
                    const userDropdown = document.querySelector('.user-dropdown');
                    if (userDropdown && userDropdown.classList.contains('active')) {
                        userDropdown.classList.remove('active');
                    }
                });
                
                if (closeCartButton) {
                    closeCartButton.addEventListener('click', function() {
                        cartDropdown.classList.remove('active');
                    });
                }
                
                // Fermer le dropdown en cliquant à l'extérieur
                document.addEventListener('click', function(e) {
                    if (!cartDropdown.contains(e.target) && !cartIcon.contains(e.target)) {
                        cartDropdown.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>