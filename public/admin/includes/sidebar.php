<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="/Site-Vitrine/public/admin/index.php">
            <span>Administration</span>
        </a>
    </div>

    <nav class="sidebar-nav">
    <nav class="sidebar-nav">
        <div class="nav-section">
            <h3 class="nav-heading">Tableau de bord</h3>
            <ul>
                <li>
                    <a href="/Site-Vitrine/public/admin/index.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/index.php') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-home"></i>
                        <span>Accueil</span>
                    </a>
                </li>
                <li>
                    <a href="/Site-Vitrine/public/admin/analytics.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/analytics.php') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-chart-line"></i>
                        <span>Statistiques</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h3 class="nav-heading">Catalogue</h3>
            <ul>
                <li>
                    <a href="/Site-Vitrine/public/admin/products/index.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/products/') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-watch"></i>
                        <span>Produits</span>
                    </a>
                </li>
                <li>
                    <a href="/Site-Vitrine/public/admin/categories/index.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/categories/') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-tags"></i>
                        <span>Catégories</span>
                    </a>
                </li>
                <li>
                    <a href="/Site-Vitrine/public/admin/collections/index.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/collections/') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-layer-group"></i>
                        <span>Collections</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h3 class="nav-heading">Ventes</h3>
            <ul>
                <li>
                    <a href="/Site-Vitrine/public/admin/orders/index.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/orders/') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-shopping-cart"></i>
                        <span>Commandes</span>
                    </a>
                </li>
                <li>
                    <a href="/Site-Vitrine/public/admin/customers/index.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/customers/') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-users"></i>
                        <span>Clients</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h3 class="nav-heading">Administration</h3>
            <ul>
                <li>
                    <a href="/Site-Vitrine/public/admin/users/index.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-user-shield"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                <li>
                    <a href="/Site-Vitrine/public/admin/system-logs.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/system-logs.php') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-shield-alt"></i>
                        <span>Journaux système</span>
                    </a>
                </li>
                <li>
                    <a href="/Site-Vitrine/public/admin/settings.php" <?= strpos($_SERVER['PHP_SELF'], '/admin/settings.php') !== false ? 'class="active"' : '' ?>>
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="/public/pages/Accueil.php" target="_blank" title="Voir le site">
            <i class="fas fa-external-link-alt"></i>
        </a>
        <a href="/Site-Vitrine/public/pages/auth/logout.php" class="logout-btn" title="Déconnexion">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</aside>