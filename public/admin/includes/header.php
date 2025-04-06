<header class="main-header">
    <div class="header-left">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="header-search">
            <form action="/Site-Vitrine/public/admin/search.php" method="GET">
                <i class="fas fa-search"></i>
                <input type="text" name="q" placeholder="Recherche rapide..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </form>
        </div>
    </div>
    
    <div class="header-right">
        <div class="header-actions">
            <a href="/Site-Vitrine/public/admin/notifications.php" class="header-icon" title="Notifications">
                <?php
                // Compter les notifications non lues (à implémenter selon votre logique)
                $unreadNotifications = 0; // Remplacer par votre code pour compter les notifications
                if ($unreadNotifications > 0):
                ?>
                <span class="badge"><?= $unreadNotifications ?></span>
                <?php endif; ?>
                <i class="fas fa-bell"></i>
            </a>
            
            <div class="user-dropdown">
                <?php
                // Récupérer les informations de l'utilisateur connecté
                $currentUser = [
                    'prenom' => $_SESSION['user_prenom'] ?? 'Admin',
                    'nom' => $_SESSION['user_nom'] ?? '',
                    'email' => $_SESSION['user_email'] ?? '',
                    'avatar' => null // Chemin vers l'avatar s'il existe
                ];
                ?>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php if (!empty($currentUser['avatar'])): ?>
                            <img src="<?= htmlspecialchars($currentUser['avatar']) ?>" alt="Avatar">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                    </div>
                    <div class="user-name">
                        <?= htmlspecialchars($currentUser['prenom']) ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                
                <div class="dropdown-menu">
                    <a href="/Site-Vitrine/public/admin/profile.php">
                        <i class="fas fa-user"></i> Mon profil
                    </a>
                    <a href="/Site-Vitrine/public/admin/settings.php">
                        <i class="fas fa-cog"></i> Paramètres
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="/Site-Vitrine/php/utils/logout.php" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>