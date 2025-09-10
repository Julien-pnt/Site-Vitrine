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
    
    <!-- Security & Performance Meta Tags -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta name="robots" content="index, follow">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="<?php echo $relativePath; ?>/assets/css/design-system.css" as="style">
    <link rel="preload" href="<?php echo $relativePath; ?>/assets/css/navigation.css" as="style">
    <link rel="preload" href="<?php echo $relativePath; ?>/assets/js/navigation.js" as="script">
    
    <!-- DNS Prefetch -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    
    <!-- Critical CSS -->
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/design-system.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/navigation.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/products.css">
    
    <!-- Legacy CSS for backward compatibility -->
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/cart.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/notifications.css">
    <?php if (isset($additionalCss)) echo $additionalCss; ?>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo $relativePath; ?>/assets/img/layout/icon2.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo $relativePath; ?>/assets/img/layout/icon2.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <title><?php echo $pageTitle; ?></title>
    
    <?php if (isset($additionalHead)) echo $additionalHead; ?>
</head>
<body>
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Aller au contenu principal</a>
    
    <!-- Modern Header -->
    <header class="main-header">
        <div class="container">
            <nav class="navbar" role="navigation" aria-label="Navigation principale">
                <!-- Brand Logo -->
                <a href="<?php echo $relativePath; ?>/pages/Accueil.php" class="navbar-brand" aria-label="Accueil Elixir du Temps">
                    <img src="<?php echo $relativePath; ?>/assets/img/layout/logo.png" alt="Elixir du Temps" class="brand-logo" width="180" height="45">
                    <span class="brand-text">Elixir du Temps</span>
                </a>
                
                <!-- Main Navigation -->
                <ul class="navbar-nav" role="menubar">
                    <li class="nav-item" role="none">
                        <a href="<?php echo $relativePath; ?>/pages/Accueil.php" 
                           class="nav-link <?php if($currentPage == 'Accueil.php') echo 'active'; ?>" 
                           role="menuitem">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            Accueil
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown" role="none">
                        <a href="<?php echo $relativePath; ?>/pages/collections/Collections.php" 
                           class="nav-link dropdown-toggle <?php if($currentPage == 'Collections.php') echo 'active'; ?>" 
                           role="menuitem" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-gem" aria-hidden="true"></i>
                            Collections
                        </a>
                        <div class="dropdown-menu" role="menu">
                            <a href="<?php echo $relativePath; ?>/pages/collections/luxury.php" class="dropdown-item" role="menuitem">
                                <i class="fas fa-crown" aria-hidden="true"></i>
                                Collection Luxury
                            </a>
                            <a href="<?php echo $relativePath; ?>/pages/collections/sport.php" class="dropdown-item" role="menuitem">
                                <i class="fas fa-running" aria-hidden="true"></i>
                                Collection Sport
                            </a>
                            <a href="<?php echo $relativePath; ?>/pages/collections/classic.php" class="dropdown-item" role="menuitem">
                                <i class="fas fa-clock" aria-hidden="true"></i>
                                Collection Classic
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo $relativePath; ?>/pages/collections/nouveautes.php" class="dropdown-item" role="menuitem">
                                <i class="fas fa-star" aria-hidden="true"></i>
                                Nouveautés
                            </a>
                        </div>
                    </li>
                    
                    <li class="nav-item" role="none">
                        <a href="<?php echo $relativePath; ?>/pages/products/Montres.php" 
                           class="nav-link <?php if($currentPage == 'Montres.php') echo 'active'; ?>" 
                           role="menuitem">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            Montres
                        </a>
                    </li>
                    
                    <li class="nav-item" role="none">
                        <a href="<?php echo $relativePath; ?>/pages/APropos.php" 
                           class="nav-link <?php if($currentPage == 'APropos.php') echo 'active'; ?>" 
                           role="menuitem">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            À propos
                        </a>
                    </li>
                    
                    <li class="nav-item" role="none">
                        <a href="<?php echo $relativePath; ?>/pages/Contact.php" 
                           class="nav-link <?php if($currentPage == 'Contact.php') echo 'active'; ?>" 
                           role="menuitem">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                            Contact
                        </a>
                    </li>
                </ul>
                
                <!-- Search Bar -->
                <div class="search-bar">
                    <form action="<?php echo $relativePath; ?>/pages/products/search.php" method="GET" role="search">
                        <input type="search" 
                               class="search-input" 
                               placeholder="Rechercher une montre..." 
                               name="q" 
                               autocomplete="off"
                               aria-label="Rechercher des produits">
                        <i class="fas fa-search search-icon" aria-hidden="true"></i>
                    </form>
                    <div class="search-results" aria-live="polite"></div>
                </div>
                
                <!-- User Actions -->
                <div class="navbar-actions">
                    <!-- User Menu -->
                    <div class="dropdown">
                        <button class="action-btn dropdown-toggle" 
                                aria-label="Menu utilisateur" 
                                aria-haspopup="true" 
                                aria-expanded="false">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <!-- User logged in -->
                                <a href="<?php echo $relativePath; ?>/user/index.php" class="dropdown-item" role="menuitem">
                                    <i class="fas fa-user-circle" aria-hidden="true"></i>
                                    Mon compte
                                </a>
                                <a href="<?php echo $relativePath; ?>/user/orders.php" class="dropdown-item" role="menuitem">
                                    <i class="fas fa-box" aria-hidden="true"></i>
                                    Mes commandes
                                </a>
                                <a href="<?php echo $relativePath; ?>/user/wishlist.php" class="dropdown-item" role="menuitem">
                                    <i class="fas fa-heart" aria-hidden="true"></i>
                                    Mes favoris
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="<?php echo $relativePath; ?>/php/api/auth/logout.php" class="dropdown-item" role="menuitem">
                                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                                    Déconnexion
                                </a>
                            <?php else: ?>
                                <!-- User not logged in -->
                                <a href="<?php echo $relativePath; ?>/pages/auth/login.php" class="dropdown-item" role="menuitem">
                                    <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                                    Se connecter
                                </a>
                                <a href="<?php echo $relativePath; ?>/pages/auth/register.php" class="dropdown-item" role="menuitem">
                                    <i class="fas fa-user-plus" aria-hidden="true"></i>
                                    S'inscrire
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Wishlist -->
                    <button class="action-btn" aria-label="Liste de souhaits" title="Mes favoris">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                        <span class="badge" id="wishlist-count">0</span>
                    </button>
                    
                    <!-- Cart -->
                    <div class="dropdown">
                        <button class="action-btn dropdown-toggle" 
                                aria-label="Panier d'achat" 
                                aria-haspopup="true" 
                                aria-expanded="false">
                            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                            <?php
                            $cartItemCount = 0;
                            foreach ($cartItems as $item) {
                                $cartItemCount += $item['quantite'];
                            }
                            ?>
                            <span class="badge" id="cart-count"><?php echo $cartItemCount; ?></span>
                        </button>
                        
                        <!-- Cart Dropdown -->
                        <div class="dropdown-menu cart-dropdown" role="menu">
                            <div class="cart-dropdown-header">
                                <h6>Mon panier (<?php echo count($cartItems); ?> articles)</h6>
                            </div>
                            
                            <?php if (empty($cartItems)): ?>
                                <div class="cart-dropdown-empty">
                                    <i class="fas fa-shopping-cart fa-2x" aria-hidden="true"></i>
                                    <p>Votre panier est vide</p>
                                    <a href="<?php echo $relativePath; ?>/pages/products/Montres.php" class="btn btn-primary btn-sm">
                                        Découvrir nos montres
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="cart-dropdown-items">
                                    <?php foreach (array_slice($cartItems, 0, 3) as $item): ?>
                                        <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
                                            <div class="cart-item-image">
                                                <?php if (!empty($item['image'])): ?>
                                                    <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($item['image'])); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['nom']); ?>"
                                                         loading="lazy">
                                                <?php else: ?>
                                                    <div class="no-image">
                                                        <i class="fas fa-clock" aria-hidden="true"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="cart-item-info">
                                                <h6><?php echo htmlspecialchars($item['nom']); ?></h6>
                                                <div class="cart-item-price">
                                                    <?php if (!empty($item['prix_promo'])): ?>
                                                        <span class="price-current"><?php echo number_format($item['prix_promo'], 0, ',', ' '); ?> €</span>
                                                        <span class="price-old"><?php echo number_format($item['prix'], 0, ',', ' '); ?> €</span>
                                                    <?php else: ?>
                                                        <span class="price-current"><?php echo number_format($item['prix'], 0, ',', ' '); ?> €</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="cart-item-quantity">
                                                    Quantité: <?php echo $item['quantite']; ?>
                                                </div>
                                            </div>
                                            <button class="cart-item-remove" data-product-id="<?php echo $item['id']; ?>" aria-label="Supprimer cet article">
                                                <i class="fas fa-times" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($cartItems) > 3): ?>
                                        <div class="cart-more-items">
                                            <p>et <?php echo count($cartItems) - 3; ?> autres articles...</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="cart-dropdown-footer">
                                    <div class="cart-total">
                                        <strong>
                                            Total: 
                                            <span id="cart-total">
                                                <?php
                                                $total = 0;
                                                foreach ($cartItems as $item) {
                                                    $price = !empty($item['prix_promo']) ? $item['prix_promo'] : $item['prix'];
                                                    $total += $price * $item['quantite'];
                                                }
                                                echo number_format($total, 0, ',', ' ') . ' €';
                                                ?>
                                            </span>
                                        </strong>
                                    </div>
                                    <div class="cart-actions">
                                        <a href="<?php echo $relativePath; ?>/pages/products/panier.php" class="btn btn-outline-dark btn-sm">
                                            Voir le panier
                                        </a>
                                        <a href="<?php echo $relativePath; ?>/pages/products/checkout.php" class="btn btn-primary btn-sm">
                                            Commander
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" 
                        aria-label="Menu de navigation mobile" 
                        aria-expanded="false" 
                        aria-controls="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div class="mobile-overlay"></div>
    <nav class="mobile-menu" id="mobile-menu" aria-label="Navigation mobile">
        <ul class="mobile-nav" role="menubar">
            <li class="nav-item" role="none">
                <a href="<?php echo $relativePath; ?>/pages/Accueil.php" 
                   class="nav-link <?php if($currentPage == 'Accueil.php') echo 'active'; ?>" 
                   role="menuitem">
                    <i class="fas fa-home" aria-hidden="true"></i>
                    Accueil
                </a>
            </li>
            <li class="nav-item" role="none">
                <a href="<?php echo $relativePath; ?>/pages/collections/Collections.php" 
                   class="nav-link <?php if($currentPage == 'Collections.php') echo 'active'; ?>" 
                   role="menuitem">
                    <i class="fas fa-gem" aria-hidden="true"></i>
                    Collections
                </a>
            </li>
            <li class="nav-item" role="none">
                <a href="<?php echo $relativePath; ?>/pages/products/Montres.php" 
                   class="nav-link <?php if($currentPage == 'Montres.php') echo 'active'; ?>" 
                   role="menuitem">
                    <i class="fas fa-clock" aria-hidden="true"></i>
                    Montres
                </a>
            </li>
            <li class="nav-item" role="none">
                <a href="<?php echo $relativePath; ?>/pages/APropos.php" 
                   class="nav-link <?php if($currentPage == 'APropos.php') echo 'active'; ?>" 
                   role="menuitem">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                    À propos
                </a>
            </li>
            <li class="nav-item" role="none">
                <a href="<?php echo $relativePath; ?>/pages/Contact.php" 
                   class="nav-link <?php if($currentPage == 'Contact.php') echo 'active'; ?>" 
                   role="menuitem">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    Contact
                </a>
            </li>
        </ul>
        
        <div class="mobile-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $relativePath; ?>/user/index.php" class="btn btn-outline-dark btn-full">
                    <i class="fas fa-user" aria-hidden="true"></i>
                    Mon compte
                </a>
                <a href="<?php echo $relativePath; ?>/php/api/auth/logout.php" class="btn btn-ghost btn-full">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                    Déconnexion
                </a>
            <?php else: ?>
                <a href="<?php echo $relativePath; ?>/pages/auth/login.php" class="btn btn-primary btn-full">
                    <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                    Se connecter
                </a>
                <a href="<?php echo $relativePath; ?>/pages/auth/register.php" class="btn btn-outline-dark btn-full">
                    <i class="fas fa-user-plus" aria-hidden="true"></i>
                    S'inscrire
                </a>
            <?php endif; ?>
        </div>
    </nav>
    
    <!-- Breadcrumb (if applicable) -->
    <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
        <nav aria-label="Fil d'Ariane" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?php echo $relativePath; ?>/pages/Accueil.php">Accueil</a>
                    </li>
                    <?php foreach ($breadcrumb as $item): ?>
                        <?php if (isset($item['url'])): ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo $item['title']; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </nav>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main id="main-content" tabindex="-1">
    
    <!-- JavaScript -->
    <script src="<?php echo $relativePath; ?>/assets/js/navigation.js" defer></script>
    <script src="<?php echo $relativePath; ?>/assets/js/performance.js" defer></script>
    
    <!-- Legacy scripts for backward compatibility -->
    <script src="<?php echo $relativePath; ?>/assets/js/cart-functions.js" defer></script>
    <script src="<?php echo $relativePath; ?>/assets/js/header-functions.js" defer></script>
    <script src="<?php echo $relativePath; ?>/assets/js/cart-common.js" defer></script>
    <script src="<?php echo $relativePath; ?>/assets/js/add-to-cart.js" defer></script>
    
    <script>
        // Configuration globale
        window.SITE_CONFIG = {
            basePath: '<?php echo $relativePath; ?>',
            isLoggedIn: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>,
            cartCount: <?php echo $cartItemCount; ?>,
            apiEndpoints: {
                search: '<?php echo $relativePath; ?>/php/api/search.php',
                cart: '<?php echo $relativePath; ?>/php/api/cart/',
                auth: '<?php echo $relativePath; ?>/php/api/auth/'
            }
        };
        
        // Compatibility shim pour l'ancien code
        document.addEventListener('DOMContentLoaded', function() {
            // Synchroniser l'ancien système avec le nouveau
            if (window.elixirNav && typeof updateHeaderUserMenu === 'function') {
                updateHeaderUserMenu();
            }
            
            // Gérer les anciens événements de panier
            document.addEventListener('cartUpdated', function(e) {
                if (window.elixirNav) {
                    document.getElementById('cart-count').textContent = e.detail.count || 0;
                }
            });
        });
    </script>
</body>
</html>