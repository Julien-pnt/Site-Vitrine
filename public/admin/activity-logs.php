<?php
// Initialisation de la session et vérification d'authentification admin
session_start();
require_once '../../php/config/database.php';
require_once '../../php/utils/auth.php';

// Redirection si l'utilisateur n'est pas connecté en tant qu'admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /public/php/api/auth/login.php?redirect=admin');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20; // Logs par page
$offset = ($page - 1) * $limit;

// Filtres
$filters = [];
$params = [];
$filterQuery = "";

if (isset($_GET['user']) && !empty($_GET['user'])) {
    $filters[] = "utilisateur_id = ?";
    $params[] = $_GET['user'];
}

if (isset($_GET['action']) && !empty($_GET['action'])) {
    $filters[] = "action = ?";
    $params[] = $_GET['action'];
}

if (isset($_GET['date_start']) && !empty($_GET['date_start'])) {
    $filters[] = "date_action >= ?";
    $params[] = $_GET['date_start'] . ' 00:00:00';
}

if (isset($_GET['date_end']) && !empty($_GET['date_end'])) {
    $filters[] = "date_action <= ?";
    $params[] = $_GET['date_end'] . ' 23:59:59';
}

if (!empty($filters)) {
    $filterQuery = " WHERE " . implode(" AND ", $filters);
}

// Récupérer les logs avec pagination
try {
    // Compter le nombre total de logs pour la pagination
    $countQuery = "SELECT COUNT(*) as total FROM admin_logs" . $filterQuery;
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalLogs = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalLogs / $limit);
    
    // Requête pour récupérer les logs avec information utilisateur
    $query = "SELECT al.*, u.prenom, u.nom, u.email 
              FROM admin_logs al
              JOIN utilisateurs u ON al.utilisateur_id = u.id" . $filterQuery . "
              ORDER BY al.date_action DESC
              LIMIT $offset, $limit";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer tous les utilisateurs admin pour le filtre
    $userStmt = $pdo->query("SELECT id, prenom, nom FROM utilisateurs WHERE role = 'admin' ORDER BY nom, prenom");
    $adminUsers = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer tous les types d'actions pour le filtre
    $actionStmt = $pdo->query("SELECT DISTINCT action FROM admin_logs ORDER BY action");
    $actionTypes = $actionStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des logs: " . $e->getMessage();
    $logs = [];
    $totalPages = 1;
    $adminUsers = [];
    $actionTypes = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs d'activité - Administration</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/tables.css">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .filter-form {
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .filter-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .filter-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .reset-filters {
            background: #f0f0f0;
            color: #333;
        }
        .apply-filters {
            background: #d4af37;
            color: white;
        }
        .log-table td {
            vertical-align: middle;
        }
        .action-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 500;
            text-transform: capitalize;
        }
        .action-login {
            background-color: #e3f2fd;
            color: #0d47a1;
        }
        .action-logout {
            background-color: #ffebee;
            color: #c62828;
        }
        .action-modification {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        .action-creation {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .action-suppression {
            background-color: #ffebee;
            color: #c62828;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        .pagination a {
            padding: 8px 12px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }
        .pagination a.active {
            background: #d4af37;
            color: white;
        }
        .pagination a:hover:not(.active) {
            background: #e0e0e0;
        }
        .export-csv {
            margin-left: auto;
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
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
                    <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                    <li><a href="products.php"><i class="fas fa-watch"></i> Produits</a></li>
                    <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
                    <li><a href="collections.php"><i class="fas fa-layer-group"></i> Collections</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                    <li><a href="reviews.php"><i class="fas fa-star"></i> Avis Clients</a></li>
                    <li><a href="pages.php"><i class="fas fa-file-alt"></i> Pages</a></li>
                    <li><a href="export.php"><i class="fas fa-file-export"></i> Exportation</a></li>
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
                    <span>Bienvenue, <?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Admin') ?></span>
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </header>

            <div class="dashboard">
                <h1><i class="fas fa-history"></i> Logs d'activité administrateurs</h1>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <!-- Filtres -->
                <div class="filter-form">
                    <form method="GET" action="activity-logs.php">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="user">Administrateur</label>
                                <select id="user" name="user">
                                    <option value="">Tous les administrateurs</option>
                                    <?php foreach ($adminUsers as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= (isset($_GET['user']) && $_GET['user'] == $user['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="action">Type d'action</label>
                                <select id="action" name="action">
                                    <option value="">Toutes les actions</option>
                                    <?php foreach ($actionTypes as $action): ?>
                                        <option value="<?= $action ?>" <?= (isset($_GET['action']) && $_GET['action'] == $action) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $action))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="date_start">Date début</label>
                                <input type="date" id="date_start" name="date_start" value="<?= $_GET['date_start'] ?? '' ?>">
                            </div>
                            <div class="filter-group">
                                <label for="date_end">Date fin</label>
                                <input type="date" id="date_end" name="date_end" value="<?= $_GET['date_end'] ?? '' ?>">
                            </div>
                            <a href="export.php?type=logs<?= !empty($_GET) ? '&' . http_build_query($_GET) : '' ?>" class="export-csv">
                                <i class="fas fa-file-csv"></i> Exporter CSV
                            </a>
                        </div>
                        <div class="filter-buttons">
                            <button type="reset" class="reset-filters" onclick="window.location='activity-logs.php'">Réinitialiser</button>
                            <button type="submit" class="apply-filters">Appliquer les filtres</button>
                        </div>
                    </form>
                </div>
                
                <!-- Tableau des logs -->
                <div class="table-container">
                    <table class="data-table log-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Administrateur</th>
                                <th>Action</th>
                                <th>Détails</th>
                                <th>Adresse IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <?php 
                                    // Définir la classe CSS selon le type d'action
                                    $actionClass = 'action-other';
                                    if (strpos($log['action'], 'login') !== false) {
                                        $actionClass = 'action-login';
                                    } elseif (strpos($log['action'], 'logout') !== false) {
                                        $actionClass = 'action-logout';
                                    } elseif (strpos($log['action'], 'modif') !== false) {
                                        $actionClass = 'action-modification';
                                    } elseif (strpos($log['action'], 'creat') !== false || strpos($log['action'], 'ajout') !== false) {
                                        $actionClass = 'action-creation';
                                    } elseif (strpos($log['action'], 'suppr') !== false || strpos($log['action'], 'delet') !== false) {
                                        $actionClass = 'action-suppression';
                                    }
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i:s', strtotime($log['date_action'])) ?></td>
                                    <td><?= htmlspecialchars($log['prenom'] . ' ' . $log['nom']) ?></td>
                                    <td>
                                        <span class="action-badge <?= $actionClass ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $log['action']))) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($log['details'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="5" class="empty-table">Aucun log d'activité trouvé</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Précédent</a>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($start + 4, $totalPages);
                        $start = max(1, $end - 4);
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                               class="<?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Suivant &raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>