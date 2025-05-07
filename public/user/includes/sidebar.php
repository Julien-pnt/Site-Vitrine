<?php
// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $relativePath . '/pages/auth/login.php');
    exit;
}

// Définir quelle page est active
$current_page = basename($_SERVER['PHP_SELF']);

// Compter les favoris pour le badge
$stmtWishlist = $conn->prepare("SELECT COUNT(*) FROM favoris WHERE utilisateur_id = ?");
$stmtWishlist->execute([$userId]);
$wishlistCount = $stmtWishlist->fetchColumn();
?>

<!-- Sidebar -->
<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <img src="<?php echo $relativePath; ?>/assets/img/layout/logo.png" alt="Elixir du Temps" class="sidebar-logo">
    </div>
    
    <nav class="sidebar-nav">
        <a href="index.php" class="sidebar-nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
        </a>
        <a href="orders.php" class="sidebar-nav-item <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
            <i class="fas fa-shopping-bag"></i>
            <span>Mes commandes</span>
        </a>
        <a href="profile.php" class="sidebar-nav-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span>Mon profil</span>
        </a>
        <a href="addresses.php" class="sidebar-nav-item <?php echo ($current_page == 'addresses.php') ? 'active' : ''; ?>">
            <i class="fas fa-map-marker-alt"></i>
            <span>Mes adresses</span>
        </a>
        <a href="wishlist.php" class="sidebar-nav-item <?php echo ($current_page == 'wishlist.php') ? 'active' : ''; ?>">
            <i class="fas fa-heart"></i>
            <span>Mes favoris</span>
            <?php if ($wishlistCount > 0): ?>
            <span class="wishlist-count"><?php echo $wishlistCount; ?></span>
            <?php endif; ?>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="<?php echo $relativePath; ?>/pages/auth/logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>

<style>
/* Style amélioré pour le badge compteur de favoris */
.wishlist-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark, #c0a02c));
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    position: relative;
    animation: pulse 2s infinite;
}

.sidebar-nav-item:hover .wishlist-count {
    transform: scale(1.1);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.25);
}

.sidebar-nav-item.active .wishlist-count {
    background: white;
    color: var(--primary-color);
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(212, 175, 55, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(212, 175, 55, 0);
    }
}
</style>