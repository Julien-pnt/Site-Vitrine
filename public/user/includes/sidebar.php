<?php
// Compter les favoris pour le badge
$stmtWishlist = $conn->prepare("SELECT COUNT(*) FROM favoris WHERE utilisateur_id = ?");
$stmtWishlist->execute([$userId]);
$wishlistCount = $stmtWishlist->fetchColumn();
?>

<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-user">
            <div class="user-info">
                <h3><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
    </div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" <?php echo ($pageTitle === "Tableau de bord") ? 'class="active"' : ''; ?>>
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
        </li>
        <li>
            <a href="profile.php" <?php echo ($pageTitle === "Mon profil") ? 'class="active"' : ''; ?>>
                <i class="fas fa-user"></i>
                <span>Mon profil</span>
            </a>
        </li>
        <li>
            <a href="orders.php" <?php echo ($pageTitle === "Mes commandes") ? 'class="active"' : ''; ?>>
                <i class="fas fa-shopping-bag"></i>
                <span>Mes commandes</span>
            </a>
        </li>
        <li>
            <a href="addresses.php" <?php echo ($pageTitle === "Mes adresses") ? 'class="active"' : ''; ?>>
                <i class="fas fa-map-marker-alt"></i>
                <span>Mes adresses</span>
            </a>
        </li>
        <li>
            <a href="wishlist.php" <?php echo ($pageTitle === "Mes favoris") ? 'class="active"' : ''; ?>>
                <i class="fas fa-heart"></i>
                <span>Mes favoris</span>
                <?php if ($wishlistCount > 0): ?>
                <span class="wishlist-count"><?php echo $wishlistCount; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="../pages/products/panier.php" <?php echo ($pageTitle === "Mon panier") ? 'class="active"' : ''; ?>>
                <i class="fas fa-shopping-cart"></i>
                <span>Mon panier</span>
            </a>
        </li>
        <li class="separator"></li>
        <li>
            <a href="../php/api/auth/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </li>
    </ul>
</aside>