<?php
// Initialisation de la session et vérification d'authentification admin
session_start();
require_once '../../php/config/database.php';
require_once '../../php/utils/auth.php';

// Redirection si l'utilisateur n'est pas connecté en tant qu'admin
if (!isLoggedIn() || !isAdmin()) {
    // Modifier cette ligne pour pointer vers le bon fichier de login
    header('Location: /public/php/api/auth/login.php?redirect=admin');
    exit;
}

// Journalisation des accès au dashboard
$logActivity = function($userId, $action) use ($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_logs (utilisateur_id, action, ip_address, date_action) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$userId, $action, $_SERVER['REMOTE_ADDR']]);
    } catch (PDOException $e) {
        // Silencieux en cas d'erreur de log - ne pas bloquer l'expérience utilisateur
    }
};

// Enregistrer l'accès au dashboard
$userId = $_SESSION['user_id'];
$logActivity($userId, 'accès_dashboard');

// Récupérer le prénom de l'administrateur pour personnalisation
try {
    $stmt = $pdo->prepare("SELECT prenom, nom, derniere_connexion FROM utilisateurs WHERE id = ? AND role = 'admin'");
    $stmt->execute([$userId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Mettre à jour la dernière connexion
    $updateStmt = $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
    $updateStmt->execute([$userId]);
} catch (PDOException $e) {
    $admin = ['prenom' => 'Admin', 'nom' => ''];
    // Log l'erreur dans un fichier
    error_log("Erreur SQL (dashboard): " . $e->getMessage());
}

// Récupérer les informations complètes de l'utilisateur connecté
$userId = $_SESSION['user_id'] ?? 0;
$userInfo = null;

if ($userId > 0) {
    try {
        $userStmt = $pdo->prepare("SELECT id, nom, prenom, email, photo, role FROM utilisateurs WHERE id = ?");
        $userStmt->execute([$userId]);
        $userInfo = $userStmt->fetch();
        
        // Créer le répertoire pour les avatars par défaut s'il n'existe pas
        $avatarsDir = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/assets/img/avatars/';
        if (!is_dir($avatarsDir)) {
            mkdir($avatarsDir, 0755, true);
        }
        
        // Copier l'image par défaut si elle n'existe pas déjà
        $defaultUserImage = $avatarsDir . 'user-default.png';
        if (!file_exists($defaultUserImage)) {
            // Utiliser une image existante comme modèle
            $sourceImage = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/assets/img/layout/jb3.jpg';
            if (file_exists($sourceImage)) {
                copy($sourceImage, $defaultUserImage);
            }
        }
    } catch (PDOException $e) {
        // Gérer silencieusement l'erreur
        error_log("Erreur lors de la récupération des informations utilisateur: " . $e->getMessage());
    }
}

// Statistiques du tableau de bord avec gestion d'erreurs
$stats = [
    'totalProducts' => 0,
    'lowStockProducts' => 0,
    'pendingOrders' => 0,
    'totalUsers' => 0,
    'monthlyRevenue' => 0,
    'orderCompletionRate' => 0,
    'averageOrderValue' => 0
];

try {
    // Nombre total de produits
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produits");
    $stats['totalProducts'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Nombre de produits en rupture de stock
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produits WHERE stock <= stock_alerte");
    $stats['lowStockProducts'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Nombre de commandes en attente de traitement
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes WHERE statut IN ('en_attente', 'payee')");
    $stats['pendingOrders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Nombre total d'utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateurs");
    $stats['totalUsers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Chiffre d'affaires total du mois en cours
    $stmt = $pdo->query("SELECT SUM(total) as ca FROM commandes 
                         WHERE MONTH(date_commande) = MONTH(CURRENT_DATE()) 
                         AND YEAR(date_commande) = YEAR(CURRENT_DATE()) 
                         AND statut IN ('payee', 'en_preparation', 'expediee', 'livree')");
    $stats['monthlyRevenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['ca'] ?? 0;
    
    // Taux de conversion des commandes (commandes complétées / commandes totales)
    $stmt = $pdo->query("SELECT 
                         (SELECT COUNT(*) FROM commandes WHERE statut IN ('livree', 'expediee')) as completed,
                         (SELECT COUNT(*) FROM commandes) as total");
    $conversionData = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['orderCompletionRate'] = $conversionData['total'] > 0 
        ? round(($conversionData['completed'] / $conversionData['total']) * 100, 1) 
        : 0;
        
    // Valeur moyenne des commandes
    $stmt = $pdo->query("SELECT AVG(total) as avg_value FROM commandes 
                         WHERE statut IN ('payee', 'en_preparation', 'expediee', 'livree')");
    $stats['averageOrderValue'] = $stmt->fetch(PDO::FETCH_ASSOC)['avg_value'] ?? 0;
    
    // Calculer les alertes et notifications
    $notifications = [];
    
    // Alerte pour rupture de stock critique (stock à 0)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM produits WHERE stock = 0");
    $stockoutCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    if ($stockoutCount > 0) {
        $notifications[] = [
            'type' => 'danger',
            'icon' => 'exclamation-triangle',
            'message' => "$stockoutCount produit(s) en rupture totale de stock",
            'link' => 'products.php?filter=stockout'
        ];
    }
    
    // Alerte pour commandes non traitées depuis plus de 48h
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM commandes 
                         WHERE statut = 'payee' AND date_commande < DATE_SUB(NOW(), INTERVAL 2 DAY)");
    $oldOrdersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    if ($oldOrdersCount > 0) {
        $notifications[] = [
            'type' => 'warning',
            'icon' => 'clock',
            'message' => "$oldOrdersCount commande(s) payée(s) en attente depuis plus de 48h",
            'link' => 'orders.php?filter=delayed'
        ];
    }
    
    // Alerte pour nouveaux avis clients à modérer
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM avis WHERE modere = 0");
    $pendingReviewsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    if ($pendingReviewsCount > 0) {
        $notifications[] = [
            'type' => 'info',
            'icon' => 'star',
            'message' => "$pendingReviewsCount nouvel(aux) avis client(s) à modérer",
            'link' => 'reviews.php?filter=pending'
        ];
    }
    
} catch (PDOException $e) {
    // Log l'erreur dans un fichier
    error_log("Erreur SQL (stats dashboard): " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Elixir du Temps</title>
    <link rel="icon" href="../assets/img/layout/jb3.jpg" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Ajoutez cette ligne dans la section <head>, avant les autres scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <style>
        /* Styles pour les notifications */
        .notifications-container {
            margin-bottom: 25px;
        }
        .notification {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            animation: fadeIn 0.5s;
        }
        .notification-icon {
            margin-right: 15px;
            font-size: 18px;
        }
        .notification-content {
            flex-grow: 1;
        }
        .notification-link {
            text-decoration: underline;
            font-weight: 500;
            color: inherit;
        }
        .notification-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            color: #dc3545;
        }
        .notification-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        .notification-info {
            background-color: rgba(23, 162, 184, 0.1);
            border-left: 4px solid #17a2b8;
            color: #17a2b8;
        }
        .notification-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid #28a745;
            color: #28a745;
        }
        
        /* Styles pour les quick actions */
        .quick-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .quick-action-btn {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-color: #d4af37;
        }
        .quick-action-btn i {
            margin-right: 8px;
            color: #d4af37;
        }
        
        /* Animation de fade in */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Amélioration générale des liens */
        a {
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }
        
        /* Liens de la sidebar */
        .sidebar-nav a {
            color: #f8f9fa;
            position: relative;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 6px;
        }
        
        .sidebar-nav a:hover {
            background-color: rgba(212, 175, 55, 0.1);
            color: #d4af37;
        }
        
        .sidebar-nav .active a {
            background-color: rgba(212, 175, 55, 0.15);
            color: #d4af37;
        }
        
        /* Animation de l'indicateur pour les liens de la sidebar */
        .sidebar-nav a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 0;
            background-color: #d4af37;
            transition: width 0.3s ease;
        }
        
        .sidebar-nav a:hover::after {
            width: 30%;
        }
        
        .sidebar-nav .active a::after {
            width: 50%;
        }
        
        /* Liens dans le footer de la sidebar */
        .sidebar-footer a {
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .sidebar-footer a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-footer a i {
            margin-right: 8px;
        }
        
        /* Liens de notification */
        .notification-link {
            color: inherit;
            font-weight: 600;
            position: relative;
            padding: 2px 5px;
            margin-left: 6px;
            border-radius: 4px;
        }
        
        .notification-link::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 1px;
            bottom: 0;
            left: 0;
            background-color: currentColor;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .notification-link:hover::after {
            transform: scaleX(1);
        }
        
        .notification-danger .notification-link:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }
        
        .notification-warning .notification-link:hover {
            background-color: rgba(255, 193, 7, 0.2);
        }
        
        .notification-info .notification-link:hover {
            background-color: rgba(23, 162, 184, 0.2);
        }
        
        /* Liens d'action rapide */
        .quick-action-btn {
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        
        /* Liens "Voir tout" */
        .view-all {
            font-size: 0.85rem;
            color: #d4af37;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .view-all:hover {
            background-color: rgba(212, 175, 55, 0.1);
            color: #c4a030;
        }
        
        /* Liens d'action dans les tableaux */
        .action-btn {
            display: inline-block;
            padding: 6px 12px;
            background-color: rgba(212, 175, 55, 0.1);
            color: #d4af37;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            background-color: rgba(212, 175, 55, 0.2);
            transform: translateY(-1px);
        }
        
        /* Lien vers les logs */
        .view-logs {
            color: #d4af37;
            font-weight: 500;
            padding: 3px 6px;
            border-radius: 3px;
            position: relative;
            background-color: rgba(212, 175, 55, 0.08);
            transition: all 0.3s ease;
        }
        
        .view-logs:hover {
            background-color: rgba(212, 175, 55, 0.15);
        }
        
        /* Animation de surbrillance pour les liens importants */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(212, 175, 55, 0); }
            100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
        }
        
        .notification-link:hover {
            animation: pulse 1.5s infinite;
        }

        /* Ajoutez ces styles dans votre fichier admin.css ou dans la section <style> de index.php */
        .header-search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .header-search input {
            padding-right: 40px; /* Espace pour l'icône */
            width: 100%;
            border-radius: 20px;
            border: 1px solid #e0e0e0;
            padding: 8px 15px;
            height: 38px; /* Hauteur fixe pour uniformité */
        }

        .header-search button {
            position: absolute;
            top: 50%; /* Centre verticalement */
            right: 15%; /* Décalage plus important vers la droite */
            transform: translateY(-50%); /* Assure un centrage parfait */
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-search button:hover {
            color: #d4af37;
        }

        /* Remplacer ou ajouter ces styles */
        .user-dropdown {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius);
        }

        .dropdown-arrow {
            transition: transform 0.2s ease;
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 5px);
            right: 0;
            width: 200px;
            background-color: white;
            border-radius: var(--radius);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 0.5rem;
            z-index: 1000;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 5px;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .user-dropdown {
            position: relative;
            display: flex;
            align-items: center;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 20px;
            transition: background-color 0.3s;
        }

        .user-dropdown:hover {
            background-color: rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <a href="index.php">
                    <!-- Ajouter une classe spécifique avec contrainte de taille -->
                    <img src="../assets/img/layout/logo.png" alt="Logo" class="sidebar-logo">
                    <span>Elixir du Temps</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-heading">Navigation</h3>
                    <ul>
                        <li class="active"><a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                        <li><a href="products.php"><i class="fas fa-box"></i> Produits</a></li>
                        <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
                        <li><a href="collections.php"><i class="fas fa-layer-group"></i> Collections</a></li>
                        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                        <li><a href="users/index.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                        <li><a href="reviews.php"><i class="fas fa-star"></i> Avis Clients</a></li>
                        <li><a href="promotions.php"><i class="fas fa-percent"></i> Promotions</a></li>
                        <li><a href="pages.php"><i class="fas fa-file-alt"></i> Pages</a></li>
                        <li><a href="export.php"><i class="fas fa-file-export"></i> Exportation</a></li>
                        <li><a href="system-logs.php"><i class="fas fa-history"></i> Historique</a></li>
                    </ul>
                </div>
            </nav>
            <div class="sidebar-footer">
                <a href="../pages/Accueil.html" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
                <a href="../../php/api/auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="header-search">
                    <form action="search-results.php" method="GET">
                        <input type="search" name="q" placeholder="Rechercher..." aria-label="Recherche globale">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="user-dropdown" id="userProfileDropdown">
                    <?php if ($userInfo && !empty($userInfo['photo'])): ?>
                        <!-- Utiliser la photo de la base de données si elle existe -->
                        <img src="../uploads/users/<?= htmlspecialchars($userInfo['photo']) ?>" alt="<?= htmlspecialchars($userInfo['prenom']) ?>" class="avatar">
                    <?php else: ?>
                        <!-- Image par défaut basée sur le rôle -->
                        <?php $defaultImage = ($userInfo && $userInfo['role'] == 'admin') ? 'user-default.png' : 'user-default.png'; ?>
                        <img src="../assets/img/avatars/<?= $defaultImage ?>" alt="Avatar" class="avatar">
                    <?php endif; ?>
                    <span class="username"><?= $userInfo ? htmlspecialchars($userInfo['prenom']) : 'Admin' ?></span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                    <div class="dropdown-menu" id="userDropdownMenu">
                        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
                        <a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a>
                        <a href="../../php/api/auth/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            </header>

            <div class="dashboard">
                <h1>Tableau de bord</h1>
                
                <!-- Notifications -->
                <?php if (!empty($notifications)): ?>
                <div class="notifications-container">
                    <?php foreach ($notifications as $notification): ?>
                    <div class="notification notification-<?= $notification['type'] ?>">
                        <div class="notification-icon">
                            <i class="fas fa-<?= $notification['icon'] ?>"></i>
                        </div>
                        <div class="notification-content">
                            <?= htmlspecialchars($notification['message']) ?> 
                            <a href="<?= $notification['link'] ?>" class="notification-link">Voir</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Actions rapides -->
                <div class="quick-actions">
                    <a href="add-product.php" class="quick-action-btn">
                        <i class="fas fa-plus"></i> Ajouter un produit
                    </a>
                    <a href="orders.php?filter=newest" class="quick-action-btn">
                        <i class="fas fa-shopping-cart"></i> Voir nouvelles commandes
                    </a>
                    <a href="reviews.php?filter=pending" class="quick-action-btn">
                        <i class="fas fa-star"></i> Modérer les avis
                    </a>
                    <a href="export.php" class="quick-action-btn">
                        <i class="fas fa-file-export"></i> Exporter données
                    </a>
                    <a href="#" class="quick-action-btn" id="clearCacheBtn">
                        <i class="fas fa-broom"></i> Vider le cache
                    </a>
                </div>
                
                <!-- Cartes de statistiques -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-card-icon blue">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-card-info">
                            <h2><?= number_format($stats['totalProducts']) ?></h2>
                            <p>Produits</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon orange">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-card-info">
                            <h2><?= number_format($stats['lowStockProducts']) ?></h2>
                            <p>Ruptures de stock</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon green">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-card-info">
                            <h2><?= number_format($stats['pendingOrders']) ?></h2>
                            <p>Commandes en attente</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon purple">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-card-info">
                            <h2><?= number_format($stats['totalUsers']) ?></h2>
                            <p>Utilisateurs</p>
                        </div>
                    </div>
                </div>
                
                <!-- Graphique de ventes -->
                <div class="dashboard-row">
                    <div class="chart-container">
                        <h3>Ventes des 30 derniers jours</h3>
                        <canvas id="salesChart"></canvas>
                        <div id="chartError" style="display:none; text-align:center; padding:20px; color:#dc3545;">
                            <i class="fas fa-exclamation-circle"></i> Impossible de charger les données du graphique
                        </div>
                    </div>
                    
                    <!-- Revenus et performance -->
                    <div class="performance-card">
                        <h3>Performance</h3>
                        <div class="revenue">
                            <h4>CA mensuel</h4>
                            <p class="amount"><?= number_format($stats['monthlyRevenue'], 2, ',', ' ') ?> €</p>
                        </div>
                        <div class="performance-stats">
                            <div class="perf-item">
                                <span>Taux de conversion</span>
                                <span class="value"><?= number_format($stats['orderCompletionRate'], 1) ?>%</span>
                            </div>
                            <div class="perf-item">
                                <span>Panier moyen</span>
                                <span class="value"><?= number_format($stats['averageOrderValue'], 2, ',', ' ') ?> €</span>
                            </div>
                            <div class="perf-item">
                                <span>Activité admin</span>
                                <a href="activity-logs.php" class="view-logs">Voir les logs</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reste du dashboard inchangé -->
                <!-- Dernières commandes et ruptures de stock -->
                <div class="dashboard-row">
                    <!-- Dernières commandes -->
                    <div class="latest-card">
                        <div class="latest-header">
                            <h3>Dernières commandes</h3>
                            <a href="orders.php" class="view-all">Tout voir</a>
                        </div>
                        <div class="latest-content">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT c.reference, c.date_commande, c.statut, c.total, CONCAT(u.prenom, ' ', u.nom) as client 
                                                        FROM commandes c 
                                                        JOIN utilisateurs u ON c.utilisateur_id = u.id 
                                                        ORDER BY c.date_commande DESC 
                                                        LIMIT 5");
                                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($orders as $order) {
                                        $statusClass = '';
                                        switch ($order['statut']) {
                                            case 'en_attente': $statusClass = 'status-pending'; break;
                                            case 'payee': $statusClass = 'status-paid'; break;
                                            case 'en_preparation': $statusClass = 'status-preparing'; break;
                                            case 'expediee': $statusClass = 'status-shipped'; break;
                                            case 'livree': $statusClass = 'status-delivered'; break;
                                            case 'annulee': $statusClass = 'status-cancelled'; break;
                                            case 'remboursee': $statusClass = 'status-refunded'; break;
                                        }
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($order['reference']) . '</td>';
                                        echo '<td>' . htmlspecialchars($order['client']) . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($order['date_commande'])) . '</td>';
                                        echo '<td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $order['statut']))) . '</span></td>';
                                        echo '<td>' . number_format($order['total'], 2, ',', ' ') . ' €</td>';
                                        echo '</tr>';
                                    }

                                    if (empty($orders)) {
                                        echo '<tr><td colspan="5" class="empty-table">Aucune commande récente</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Produits en rupture de stock -->
                    <div class="latest-card">
                        <div class="latest-header">
                            <h3>Ruptures de stock</h3>
                            <a href="products.php?filter=low-stock" class="view-all">Tout voir</a>
                        </div>
                        <div class="latest-content">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Référence</th>
                                        <th>Stock</th>
                                        <th>Alerte à</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT id, nom, reference, stock, stock_alerte 
                                                       FROM produits 
                                                       WHERE stock <= stock_alerte 
                                                       ORDER BY stock ASC 
                                                       LIMIT 5");
                                    $lowStockItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($lowStockItems as $item) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($item['nom']) . '</td>';
                                        echo '<td>' . htmlspecialchars($item['reference']) . '</td>';
                                        echo '<td class="' . ($item['stock'] == 0 ? 'stock-out' : 'stock-low') . '">' . $item['stock'] . '</td>';
                                        echo '<td>' . $item['stock_alerte'] . '</td>';
                                        echo '<td><a href="edit-product.php?id=' . $item['id'] . '" class="action-btn">Gérer</a></td>';
                                        echo '</tr>';
                                    }

                                    if (empty($lowStockItems)) {
                                        echo '<tr><td colspan="5" class="empty-table">Tous les produits ont un stock suffisant</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
    // Graphique des ventes des 30 derniers jours avec gestion d'erreur
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour afficher une erreur de chargement du graphique
        function showChartError() {
            document.getElementById('chartError').style.display = 'block';
            document.getElementById('salesChart').style.display = 'none';
        }
        
        // Charger les données du graphique avec gestion d'erreur
        fetch('api/get-sales-chart-data.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                if (!data || !data.labels || !data.sales) {
                    throw new Error('Format de données invalide');
                }
                
                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Ventes (€)',
                            data: data.sales,
                            backgroundColor: 'rgba(212, 175, 55, 0.2)',
                            borderColor: '#d4af37',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: '#d4af37'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString() + ' €';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y.toLocaleString() + ' €';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Erreur de chargement des données:', error);
                showChartError();
            });

        // Gestionnaire pour le bouton de nettoyage du cache
        document.getElementById('clearCacheBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            fetch('api/clear-cache.php')
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Créer une notification temporaire
                        const notification = document.createElement('div');
                        notification.className = 'notification notification-success';
                        notification.innerHTML = `
                            <div class="notification-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="notification-content">
                                Le cache a été vidé avec succès.
                            </div>
                        `;
                        
                        // Ajouter la notification en haut du dashboard
                        const notificationsContainer = document.querySelector('.notifications-container') || 
                            document.querySelector('.dashboard').insertBefore(
                                document.createElement('div'),
                                document.querySelector('.dashboard').firstChild
                            );
                        
                        notificationsContainer.className = 'notifications-container';
                        notificationsContainer.prepend(notification);
                        
                        // Supprimer la notification après 3 secondes
                        setTimeout(() => {
                            notification.style.opacity = '0';
                            setTimeout(() => notification.remove(), 300);
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du nettoyage du cache:', error);
                });
        });
    });
    
    // Placer ce code dans votre balise <script> existante
    document.addEventListener('DOMContentLoaded', function() {
        // Menu déroulant utilisateur amélioré
        const userDropdown = document.getElementById('userProfileDropdown');
        const dropdownMenu = document.getElementById('userDropdownMenu');
        const dropdownArrow = document.querySelector('.dropdown-arrow');
        
        if (userDropdown && dropdownMenu) {
            // Fermer le menu au chargement
            dropdownMenu.style.display = 'none';
            dropdownMenu.style.opacity = '0';
            dropdownMenu.style.transform = 'translateY(-10px)';
            
            // Toggle le menu au clic
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation(); // Empêche la propagation au document
                
                const isOpen = dropdownMenu.style.display === 'block';
                
                if (isOpen) {
                    // Fermer le menu
                    dropdownArrow.style.transform = 'rotate(0deg)';
                    dropdownMenu.style.opacity = '0';
                    dropdownMenu.style.transform = 'translateY(-10px)';
                    
                    setTimeout(() => {
                        dropdownMenu.style.display = 'none';
                    }, 200); // Délai correspondant à la transition
                } else {
                    // Ouvrir le menu
                    dropdownMenu.style.display = 'block';
                    dropdownArrow.style.transform = 'rotate(180deg)';
                    
                    // Force reflow pour permettre la transition
                    void dropdownMenu.offsetWidth;
                    
                    dropdownMenu.style.opacity = '1';
                    dropdownMenu.style.transform = 'translateY(0)';
                }
            });
            
            // Fermer le menu quand on clique ailleurs
            document.addEventListener('click', function(e) {
                if (dropdownMenu.style.display === 'block') {
                    dropdownArrow.style.transform = 'rotate(0deg)';
                    dropdownMenu.style.opacity = '0';
                    dropdownMenu.style.transform = 'translateY(-10px)';
                    
                    setTimeout(() => {
                        dropdownMenu.style.display = 'none';
                    }, 200);
                }
            });
            
            // Empêcher la fermeture lorsqu'on clique sur les éléments du menu
            dropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
    </script>
</body>
</html>