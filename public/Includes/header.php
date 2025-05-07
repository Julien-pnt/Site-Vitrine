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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Elixir du Temps - Découvrez notre collection de montres de luxe, alliant tradition et innovation.'; ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $pageTitle; ?>">
    <meta property="og:description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Elixir du Temps - Découvrez notre collection de montres de luxe, alliant tradition et innovation.'; ?>">
    <meta property="og:image" content="<?php echo $relativePath; ?>/assets/img/layout/social-preview.jpg">
    
    <!-- Script pour corriger le fondu blanc -->
    <script>
        // Force l'affichage immédiat du contenu
        document.documentElement.style.opacity = "1";
        function ensureVisibility() {
            document.body.classList.add('video-loaded');
        }
        // S'exécute dès que possible
        document.addEventListener('DOMContentLoaded', ensureVisibility);
        // Backup au cas où DOMContentLoaded ne se déclencherait pas
        setTimeout(ensureVisibility, 100);
    </script>
    
    <!-- Ressources CSS communes -->
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>/assets/css/footer.css">
    <?php if (isset($additionalCss)) echo $additionalCss; ?>
    
    <link rel="shortcut icon" href="<?php echo $relativePath; ?>/assets/img/layout/icon2.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <title><?php echo $pageTitle; ?></title>
    
    <?php if (isset($additionalHead)) echo $additionalHead; ?>
    
    <style>
        /* RESET COMPLET du panier */
        .cart-icon {
            position: relative !important;
            cursor: pointer;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #d4af37;
            color: white;
            font-size: 10px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Supprimez toutes les anciennes règles */
        .cart-dropdown {
            /* Le style sera appliqué par JS */
        }

        /* Alignement correct des icônes */
        .user-cart-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
        }

        .user-menu-container, .cart-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .user-icon-wrapper {
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }

        .user-dropdown {
            position: absolute;
            top: calc(100% + 5px);
            right: -5px;
            width: 220px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            z-index: 9998;
            display: none;
            padding: 10px 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .dropdown-item:hover {
            background-color: rgba(212, 175, 55, 0.1);
        }

        .dropdown-item i {
            margin-right: 10px;
            color: #d4af37;
            width: 16px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #eee;
            margin: 8px 0;
        }
    </style>
</head>
<body class="video-loaded">
    <!-- Loader -->
    <div class="loader-container">
        <div class="loader">
            <span class="visually-hidden">Chargement en cours...</span>
        </div>
    </div>
    
    <!-- Header section with logo and navigation menu -->
    <header class="header">
        <div class="logo-container">
            <a href="<?php echo $relativePath; ?>/pages/Accueil.php" aria-label="Accueil Elixir du Temps">
                <img src="<?php echo $relativePath; ?>/assets/img/layout/logo.png" alt="Logo Elixir du Temps" class="logo" width="180" height="60" fetchpriority="high">
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-user">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
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
            
            <!-- Structure correcte pour l'icône du panier -->
            <div class="cart-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span class="cart-badge"><?php echo isset($cartCount) ? $cartCount : 0; ?></span>
                
                <?php include_once(__DIR__ . '/cart-template.php'); ?>
            </div>
        </div>
    </header>

    <!-- Solution extrême pour le panier -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Récupérer les éléments
        const cartIcon = document.querySelector('.cart-icon');
        const cartDropdown = document.querySelector('.cart-dropdown');
        
        if (cartIcon && cartDropdown) {
            console.log("Script d'urgence activé pour le panier");
            
            // IMPORTANT: Ne PAS remplacer l'élément cartIcon car cela supprime le dropdown à l'intérieur
            // const newCartIcon = cartIcon.cloneNode(true);
            // cartIcon.parentNode.replaceChild(newCartIcon, cartIcon);
            
            // Au lieu de cela, supprimez simplement tous les écouteurs d'événements existants
            const oldIcon = cartIcon.querySelector('svg');
            const newIcon = oldIcon.cloneNode(true);
            oldIcon.parentNode.replaceChild(newIcon, oldIcon);
            
            // RÉINITIALISER COMPLÈTEMENT LE DROPDOWN
            Object.assign(cartDropdown.style, {
                display: 'none',
                position: 'absolute',
                top: 'calc(100% + 5px)',
                right: '-5px',
                width: '320px',
                backgroundColor: 'white', 
                boxShadow: '0 5px 20px rgba(0,0,0,0.15)',
                borderRadius: '8px',
                zIndex: '9999',
                // Force une couleur de texte visible
                color: '#333'
            });
            
            // Ajouter l'écouteur au SVG de l'icône
            cartIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log("Clic sur panier détecté (script d'urgence)");
                
                // Basculer l'affichage directement
                cartDropdown.style.display = 
                    cartDropdown.style.display === 'block' ? 'none' : 'block';
                
                console.log("État du panier:", cartDropdown.style.display);
            });
            

            
            // Fermeture au clic extérieur
            document.addEventListener('click', function(e) {
                if (cartDropdown.style.display === 'block' && 
                    !cartIcon.contains(e.target) && 
                    !cartDropdown.contains(e.target)) {
                    cartDropdown.style.display = 'none';
                }
            });
        } else {
            console.error("Éléments du panier introuvables");
        }
    });
    </script>

    <!-- Le contenu spécifique de la page sera inséré ici -->