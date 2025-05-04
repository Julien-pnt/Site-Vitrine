<?php
// Déterminer la page active
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="<?php echo $admin_root ?? ''; ?>index.php">
            <img src="<?php echo $admin_root ?? ''; ?>../assets/img/layout/logo.png" alt="Logo" class="sidebar-logo">
            <span>Elixir du Temps</span>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <h3 class="nav-heading">Navigation</h3>
            <ul>
                <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <a href="<?php echo $admin_root ?? ''; ?>index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                </li>
                <li class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">
                    <a href="<?php echo $admin_root ?? ''; ?>products.php"><i class="fas fa-box"></i> Produits</a>
                </li>
                <li class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
                    <a href="<?php echo $admin_root ?? ''; ?>categories.php"><i class="fas fa-tags"></i> Catégories</a>
                </li>
                <li class="<?php echo ($current_page == 'collections.php') ? 'active' : ''; ?>">
                    <a href="<?php echo $admin_root ?? ''; ?>collections.php"><i class="fas fa-layer-group"></i> Collections</a>
                </li>
                <li class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                    <a href="<?php echo $admin_root ?? ''; ?>orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a>
                </li>
                <li class="<?php echo ($current_page == 'users.php' || $current_page == 'index.php' && strpos($_SERVER['REQUEST_URI'], '/users/') !== false) ? 'active' : ''; ?>">
                    <a href="<?php echo $admin_root ?? ''; ?>users/index.php"><i class="fas fa-users"></i> Utilisateurs</a>
                </li>
                <li class="<?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">
                    <a href="<?php echo $admin_root ?? ''; ?>reviews.php"><i class="fas fa-star"></i> Avis Clients</a>
                </li>
                <li class="<?php echo ($current_page == 'promotions.php') ? 'active' : ''; ?>">
                    <a href="<?php echo $admin_root ?? ''; ?>promotions.php"><i class="fas fa-percent"></i> Promotions</a>
                </li>
                <li class="<?php echo ($current_page == 'system-logs.php') ? 'active' : ''; ?>">
                    <a href="<?php echo $admin_root ?? ''; ?>system-logs.php"><i class="fas fa-history"></i> Historique</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="sidebar-footer">
        <a href="../../pages/Accueil.php" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
        <a href="../../php/api/auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
</aside>