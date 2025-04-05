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

// Récupérer le prénom de l'administrateur pour personnalisation
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT prenom, nom FROM utilisateurs WHERE id = ? AND role = 'admin'");
$stmt->execute([$userId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les statistiques clés pour le tableau de bord
// Nombre total de produits
$stmt = $pdo->query("SELECT COUNT(*) as total FROM produits");
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Nombre de produits en rupture de stock
$stmt = $pdo->query("SELECT COUNT(*) as total FROM produits WHERE stock <= stock_alerte");
$lowStockProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Nombre de commandes en attente de traitement
$stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes WHERE statut IN ('en_attente', 'payee')");
$pendingOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Nombre total d'utilisateurs
$stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateurs");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Chiffre d'affaires total du mois en cours
$stmt = $pdo->query("SELECT SUM(total) as ca FROM commandes WHERE MONTH(date_commande) = MONTH(CURRENT_DATE()) AND YEAR(date_commande) = YEAR(CURRENT_DATE()) AND statut IN ('payee', 'en_preparation', 'expediee', 'livree')");
$monthlyRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['ca'] ?? 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Elixir du Temps</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar de navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/layout/logo.png" alt="Elixir du Temps" class="logo">
                <h2>Administration</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                    <li><a href="products.php"><i class="fas fa-watch"></i> Produits</a></li>
                    <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
                    <li><a href="collections.php"><i class="fas fa-layer-group"></i> Collections</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                    <li><a href="reviews.php"><i class="fas fa-star"></i> Avis Clients</a></li>
                    <li><a href="pages.php"><i class="fas fa-file-alt"></i> Pages</a></li>
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
                    <span>Bienvenue, <?= htmlspecialchars($admin['prenom'] ?? $admin['nom'] ?? 'Admin') ?></span>
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </header>

            <div class="dashboard">
                <h1>Tableau de bord</h1>
                
                <!-- Cartes de statistiques -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-card-icon blue">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-card-info">
                            <h2><?= number_format($totalProducts) ?></h2>
                            <p>Produits</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon orange">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-card-info">
                            <h2><?= number_format($lowStockProducts) ?></h2>
                            <p>Ruptures de stock</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon green">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-card-info">
                            <h2><?= number_format($pendingOrders) ?></h2>
                            <p>Commandes en attente</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon purple">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-card-info">
                            <h2><?= number_format($totalUsers) ?></h2>
                            <p>Utilisateurs</p>
                        </div>
                    </div>
                </div>
                
                <!-- Graphique de ventes -->
                <div class="dashboard-row">
                    <div class="chart-container">
                        <h3>Ventes des 30 derniers jours</h3>
                        <canvas id="salesChart"></canvas>
                    </div>
                    
                    <!-- Revenus et performance -->
                    <div class="performance-card">
                        <h3>Performance</h3>
                        <div class="revenue">
                            <h4>CA mensuel</h4>
                            <p class="amount"><?= number_format($monthlyRevenue, 2, ',', ' ') ?> €</p>
                        </div>
                        <div class="performance-stats">
                            <div class="perf-item">
                                <span>Taux de conversion</span>
                                <span class="value">3.2%</span>
                            </div>
                            <div class="perf-item">
                                <span>Panier moyen</span>
                                <span class="value">450,00 €</span>
                            </div>
                            <div class="perf-item">
                                <span>Trafic mensuel</span>
                                <span class="value">1,243</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Dernières activités -->
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
    <script src="js/admin.js"></script>
    <script>
    // Graphique des ventes des 30 derniers jours
    document.addEventListener('DOMContentLoaded', function() {
        fetch('api/get-sales-chart-data.php')
            .then(response => response.json())
            .then(data => {
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
            .catch(error => console.error('Erreur de chargement des données:', error));
    });
    </script>
</body>
</html>