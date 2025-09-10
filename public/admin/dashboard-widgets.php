<?php
// Protection CSRF ajoutée automatiquement
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

session_start();
require_once '../../php/config/database.php';
require_once '../../php/utils/auth.php';

// Vérification de l'authentification admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /public/php/api/auth/login.php?redirect=admin');
    exit;
}

// Récupérer les widgets activés pour l'utilisateur
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT widgets FROM admin_preferences WHERE user_id = ?");
$stmt->execute([$userId]);
$preferences = $stmt->fetch(PDO::FETCH_ASSOC);

// Widgets par défaut si aucune préférence n'existe
$activeWidgets = $preferences ? json_decode($preferences['widgets'], true) : [
    'sales_chart' => true,
    'low_stock' => true,
    'recent_orders' => true,
    'top_products' => true,
    'revenue_stats' => true,
    'customer_activity' => false,
    'marketing_stats' => false
];

// Traitement des modifications de préférences
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_widgets') {
    $newWidgets = [];
    
    // Récupérer les états des widgets depuis le formulaire
    $widgetOptions = [
        'sales_chart', 'low_stock', 'recent_orders', 'top_products', 
        'revenue_stats', 'customer_activity', 'marketing_stats'
    ];
    
    foreach ($widgetOptions as $widget) {
        $newWidgets[$widget] = isset($_POST['widget'][$widget]) ? true : false;
    }
    
    // Mettre à jour ou créer les préférences
    if ($preferences) {
        $stmt = $pdo->prepare("UPDATE admin_preferences SET widgets = ? WHERE user_id = ?");
        $stmt->execute([json_encode($newWidgets), $userId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO admin_preferences (user_id, widgets) VALUES (?, ?)");
        $stmt->execute([$userId, json_encode($newWidgets)]);
    }
    
    // Rediriger vers le tableau de bord avec les nouvelles préférences
    header('Location: index.php?dashboard_updated=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnalisation du Tableau de Bord - Elixir du Temps</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .widget-settings {
            background-color: #1f1f1f;
            border-radius: 8px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .widget-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .widget-item {
            background: #2d2d2d;
            border-radius: 6px;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .widget-item label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            width: 100%;
        }
        
        .widget-item i {
            font-size: 1.5em;
            color: #d4af37;
            min-width: 30px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #444;
            transition: .4s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: #d4af37;
        }
        
        input:focus + .toggle-slider {
            box-shadow: 0 0 1px #d4af37;
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        .actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #d4af37;
            color: #121212;
        }
        
        .btn-secondary {
            background-color: #444;
            color: #fff;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar (même code que dans index.php) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/layout/logo.png" alt="Elixir du Temps" class="logo">
                <h2>Administration</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                    <li><a href="products.php"><i class="fas fa-watch"></i> Produits</a></li>
                    <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
                    <li><a href="collections.php"><i class="fas fa-layer-group"></i> Collections</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                    <li><a href="reviews.php"><i class="fas fa-star"></i> Avis Clients</a></li>
                    <li><a href="pages.php"><i class="fas fa-file-alt"></i> Pages</a></li>
                    <li class="active"><a href="dashboard-widgets.php"><i class="fas fa-th-large"></i> Widgets</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../pages/Accueil.html" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
                <a href="../../php/api/auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-search">
                    <input type="search" placeholder="Rechercher...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
                <div class="header-user">
                    <span>Configuration du tableau de bord</span>
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </header>

            <div class="dashboard">
                <h1>Personnaliser le Tableau de Bord</h1>
                <p>Configurez les widgets à afficher sur votre tableau de bord pour une expérience personnalisée.</p>
                
                <div class="widget-settings">
                    <h2>Widgets disponibles</h2>
                    <form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="save_widgets">
                        
                        <div class="widget-list">
                            <!-- Graphique des ventes -->
                            <div class="widget-item">
                                <label for="widget-sales-chart">
                                    <i class="fas fa-chart-line"></i>
                                    <div>
                                        <strong>Graphique des ventes</strong>
                                        <div>Suivi des ventes quotidiennes</div>
                                    </div>
                                </label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="widget-sales-chart" name="widget[sales_chart]" <?= $activeWidgets['sales_chart'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            
                            <!-- Produits en rupture de stock -->
                            <div class="widget-item">
                                <label for="widget-low-stock">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div>
                                        <strong>Ruptures de stock</strong>
                                        <div>Alertes de stock faible</div>
                                    </div>
                                </label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="widget-low-stock" name="widget[low_stock]" <?= $activeWidgets['low_stock'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            
                            <!-- Commandes récentes -->
                            <div class="widget-item">
                                <label for="widget-recent-orders">
                                    <i class="fas fa-shopping-cart"></i>
                                    <div>
                                        <strong>Commandes récentes</strong>
                                        <div>Dernières commandes clients</div>
                                    </div>
                                </label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="widget-recent-orders" name="widget[recent_orders]" <?= $activeWidgets['recent_orders'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            
                            <!-- Produits les plus vendus -->
                            <div class="widget-item">
                                <label for="widget-top-products">
                                    <i class="fas fa-trophy"></i>
                                    <div>
                                        <strong>Produits populaires</strong>
                                        <div>Classement des meilleures ventes</div>
                                    </div>
                                </label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="widget-top-products" name="widget[top_products]" <?= $activeWidgets['top_products'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            
                            <!-- Statistiques de revenus -->
                            <div class="widget-item">
                                <label for="widget-revenue-stats">
                                    <i class="fas fa-euro-sign"></i>
                                    <div>
                                        <strong>Statistiques de revenus</strong>
                                        <div>Analyse financière</div>
                                    </div>
                                </label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="widget-revenue-stats" name="widget[revenue_stats]" <?= $activeWidgets['revenue_stats'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            
                            <!-- Activité des clients -->
                            <div class="widget-item">
                                <label for="widget-customer-activity">
                                    <i class="fas fa-users"></i>
                                    <div>
                                        <strong>Activité des clients</strong>
                                        <div>Suivi des sessions et interactions</div>
                                    </div>
                                </label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="widget-customer-activity" name="widget[customer_activity]" <?= $activeWidgets['customer_activity'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            
                            <!-- Statistiques marketing -->
                            <div class="widget-item">
                                <label for="widget-marketing-stats">
                                    <i class="fas fa-bullhorn"></i>
                                    <div>
                                        <strong>Statistiques marketing</strong>
                                        <div>Campagnes et conversions</div>
                                    </div>
                                </label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="widget-marketing-stats" name="widget[marketing_stats]" <?= $activeWidgets['marketing_stats'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="actions">
                            <a href="index.php" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Enregistrer les préférences</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>