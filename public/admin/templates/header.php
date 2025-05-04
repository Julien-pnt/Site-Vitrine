<?php
// Récupérer les informations utilisateur si non déjà disponibles
if (!isset($userInfo) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    try {
        $userStmt = $pdo->prepare("SELECT id, nom, prenom, email, photo, role FROM utilisateurs WHERE id = ?");
        $userStmt->execute([$userId]);
        $userInfo = $userStmt->fetch();
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des informations utilisateur: " . $e->getMessage());
        $userInfo = null;
    }
}
?>

<header class="main-header">
    <div class="header-search">
        <form action="<?= isset($search_action) ? $search_action : $admin_root . 'search-results.php' ?>" method="GET">
            <input type="search" 
                   name="<?= isset($search_param) ? $search_param : 'q' ?>" 
                   placeholder="<?= isset($search_placeholder) ? $search_placeholder : 'Rechercher...' ?>" 
                   value="<?= isset($search_value) ? htmlspecialchars($search_value) : '' ?>"
                   aria-label="Recherche">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="user-dropdown" id="userProfileDropdown">
        <?php if (isset($userInfo) && !empty($userInfo['photo'])): ?>
            <img src="<?= $admin_root ?>../uploads/users/<?= htmlspecialchars($userInfo['photo']) ?>" alt="<?= htmlspecialchars($userInfo['prenom']) ?>" class="avatar">
        <?php else: ?>
            <img src="<?= $admin_root ?>../assets/img/favicon.ico" alt="Avatar" class="avatar">
        <?php endif; ?>
        <span class="username"><?= isset($userInfo) ? htmlspecialchars($userInfo['prenom']) : 'Admin' ?></span>
        <i class="fas fa-chevron-down dropdown-arrow"></i>
        
        <div class="dropdown-menu" id="userDropdownMenu">
            <a href="<?= $admin_root ?>profile.php"><i class="fas fa-user-circle"></i> Mon profil</a>
            <a href="<?= $admin_root ?>settings.php"><i class="fas fa-cog"></i> Paramètres</a>
            <a href="<?= $admin_root ?>../php/api/auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>
</header>