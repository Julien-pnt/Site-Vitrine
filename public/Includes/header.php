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
                <li><a href="<?php echo $relativePath; ?>/pages/Collections.php" <?php if($currentPage == 'Collections.php') echo 'class="active"'; ?>>Collections</a></li>
                <li><a href="<?php echo $relativePath; ?>/pages/Montres.php" <?php if($currentPage == 'Montres.php') echo 'class="active"'; ?>>Montres</a></li>
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
            
            <div class="cart-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span class="cart-badge">0</span>
                
                <div class="cart-dropdown">
                    <div class="cart-dropdown-header">
                        <h3>Mon Panier</h3>
                    </div>
                    <div class="cart-dropdown-items">
                        <!-- Le panier sera rempli dynamiquement via JavaScript -->
                    </div>
                    <div class="cart-dropdown-empty">Votre panier est vide</div>
                    <div class="cart-dropdown-total">
                        <span>Total:</span>
                        <span class="cart-dropdown-total-value">0,00 €</span>
                    </div>
                    <div class="cart-dropdown-buttons">
                        <a href="<?php echo $relativePath; ?>/pages/products/panier.php" class="cart-dropdown-button secondary">Voir le panier</a>
                        <a href="<?php echo $relativePath; ?>/pages/Montres.php" class="cart-dropdown-button primary">Découvrir nos montres</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Le contenu spécifique de la page sera inséré ici -->