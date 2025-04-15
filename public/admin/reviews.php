<!-- filepath: c:\xampp\htdocs\Site-Vitrine\public\admin\reviews.php -->
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

// Journalisation des accès
$logActivity = function($userId, $action) use ($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_logs (utilisateur_id, action, ip_address, date_action) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$userId, $action, $_SERVER['REMOTE_ADDR']]);
    } catch (PDOException $e) {
        // Silencieux en cas d'erreur
    }
};

// Enregistrer l'accès à la page
$userId = $_SESSION['user_id'];
$logActivity($userId, 'accès_gestion_avis');

// Récupérer les informations de l'utilisateur connecté
$userInfo = null;
if ($userId > 0) {
    try {
        $userStmt = $pdo->prepare("SELECT id, nom, prenom, email, photo, role FROM utilisateurs WHERE id = ?");
        $userStmt->execute([$userId]);
        $userInfo = $userStmt->fetch();
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des informations utilisateur: " . $e->getMessage());
    }
}

// Traitement des actions
$message = '';
$messageType = '';

if (isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'approve' && isset($_POST['review_id'])) {
            $stmt = $pdo->prepare("UPDATE avis SET statut = 'approuve' WHERE id = ?");
            $stmt->execute([$_POST['review_id']]);
            $logActivity($userId, 'approbation_avis_' . $_POST['review_id']);
            $message = "L'avis a été approuvé avec succès.";
            $messageType = 'success';
        } 
        elseif ($_POST['action'] === 'reject' && isset($_POST['review_id'])) {
            $stmt = $pdo->prepare("UPDATE avis SET statut = 'rejete' WHERE id = ?");
            $stmt->execute([$_POST['review_id']]);
            $logActivity($userId, 'rejet_avis_' . $_POST['review_id']);
            $message = "L'avis a été rejeté.";
            $messageType = 'success';
        }
        elseif ($_POST['action'] === 'delete' && isset($_POST['review_id'])) {
            $stmt = $pdo->prepare("DELETE FROM avis WHERE id = ?");
            $stmt->execute([$_POST['review_id']]);
            $logActivity($userId, 'suppression_avis_' . $_POST['review_id']);
            $message = "L'avis a été supprimé définitivement.";
            $messageType = 'success';
        }
        elseif ($_POST['action'] === 'bulk_approve' && isset($_POST['review_ids'])) {
            $ids = explode(',', $_POST['review_ids']);
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare("UPDATE avis SET statut = 'approuve' WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                $logActivity($userId, 'approbation_multiple_avis');
                $message = count($ids) . " avis ont été approuvés avec succès.";
                $messageType = 'success';
            }
        }
        elseif ($_POST['action'] === 'bulk_reject' && isset($_POST['review_ids'])) {
            $ids = explode(',', $_POST['review_ids']);
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare("UPDATE avis SET statut = 'rejete' WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                $logActivity($userId, 'rejet_multiple_avis');
                $message = count($ids) . " avis ont été rejetés.";
                $messageType = 'success';
            }
        }
    } catch (PDOException $e) {
        $message = "Une erreur est survenue lors du traitement de l'action. Veuillez réessayer.";
        $messageType = 'error';
        error_log("Erreur lors du traitement d'action sur les avis: " . $e->getMessage());
    }
}

// Paramètres de pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 15; // Nombre d'avis par page
$offset = ($page - 1) * $limit;

// Paramètres de filtrage
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$filterProduct = isset($_GET['product']) ? intval($_GET['product']) : 0;
$filterRating = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
$filterKeyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$filterDateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filterDateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Construction de la requête SQL avec filtres
$whereConditions = [];
$params = [];

if ($filterStatus) {
    $whereConditions[] = "a.statut = ?";
    $params[] = $filterStatus;
}

if ($filterProduct) {
    $whereConditions[] = "a.produit_id = ?";
    $params[] = $filterProduct;
}

if ($filterRating) {
    $whereConditions[] = "a.note = ?";
    $params[] = $filterRating;
}

if ($filterKeyword) {
    $whereConditions[] = "(a.commentaire LIKE ? OR p.nom LIKE ? OR CONCAT(u.prenom, ' ', u.nom) LIKE ?)";
    $params[] = "%$filterKeyword%";
    $params[] = "%$filterKeyword%";
    $params[] = "%$filterKeyword%";
}

if ($filterDateFrom) {
    $whereConditions[] = "a.date_creation >= ?";
    $params[] = $filterDateFrom . ' 00:00:00';
}

if ($filterDateTo) {
    $whereConditions[] = "a.date_creation <= ?";
    $params[] = $filterDateTo . ' 23:59:59';
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Requête pour compter le nombre total d'avis
$countSql = "SELECT COUNT(*) FROM avis a 
             LEFT JOIN produits p ON a.produit_id = p.id 
             LEFT JOIN utilisateurs u ON a.utilisateur_id = u.id 
             $whereClause";

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalReviews = $countStmt->fetchColumn();
$totalPages = ceil($totalReviews / $limit);

// Requête pour récupérer les avis avec pagination
$sql = "SELECT a.*, p.nom as produit_nom, p.slug as produit_slug, CONCAT(u.prenom, ' ', u.nom) as utilisateur_nom
        FROM avis a
        LEFT JOIN produits p ON a.produit_id = p.id
        LEFT JOIN utilisateurs u ON a.utilisateur_id = u.id
        $whereClause
        ORDER BY a.date_creation DESC
        LIMIT $offset, $limit";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des produits pour le filtre
$productStmt = $pdo->query("SELECT id, nom FROM produits ORDER BY nom");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// Nombre d'avis en attente pour la notification
$pendingReviewsCount = $pdo->query("SELECT COUNT(*) FROM avis WHERE statut = 'en_attente'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des avis clients - Elixir du Temps</title>
    <link rel="icon" href="../assets/img/layout/jb3.jpg" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <style>
        /* Styles spécifiques pour la gestion des avis */
        .review-filters {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
            align-items: end;
        }
        
        .review-filters .filter-group {
            margin-bottom: 0;
        }
        
        .review-filters .filter-actions {
            display: flex;
            gap: 10px;
        }
        
        .star-rating {
            color: #d4af37;
            white-space: nowrap;
        }
        
        .table-actions form {
            display: inline;
        }
        
        .review-content {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .review-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        
        .status-approved {
            background-color: rgba(40, 167, 69, 0.2);
            color: #155724;
        }
        
        .status-rejected {
            background-color: rgba(220, 53, 69, 0.2);
            color: #721c24;
        }
        
        .bulk-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .review-detail-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .modal-content {
            background-color: white;
            margin: 50px auto;
            padding: 25px;
            border-radius: 8px;
            max-width: 700px;
            position: relative;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            cursor: pointer;
            font-size: 1.3rem;
            color: #666;
        }
        
        .review-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .review-image img {
            max-width: 100%;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        
        .detail-heading {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 0.85rem;
        }
        
        .detail-value {
            margin-bottom: 15px;
        }
        
        .detail-comment {
            grid-column: 1 / -1;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            white-space: pre-line;
            margin-top: 10px;
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        /* Animation de fondu pour le modal */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .fade-in {
            animation: fadeIn 0.3s;
        }
        
        /* Style pour les étoiles interactives */
        .static-stars {
            font-size: 1.2rem;
            letter-spacing: 2px;
        }
        
        /* Checkbox customisée */
        .custom-checkbox {
            position: relative;
            padding-left: 30px;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
        }
        
        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        
        .checkbox-mark {
            position: absolute;
            left: 0;
            height: 20px;
            width: 20px;
            background-color: #eee;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .custom-checkbox:hover input ~ .checkbox-mark {
            background-color: #ddd;
        }
        
        .custom-checkbox input:checked ~ .checkbox-mark {
            background-color: #d4af37;
            border-color: #d4af37;
        }
        
        .checkbox-mark:after {
            content: "";
            position: absolute;
            display: none;
        }
        
        .custom-checkbox input:checked ~ .checkbox-mark:after {
            display: block;
        }
        
        .custom-checkbox .checkbox-mark:after {
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        /* Alertes et messages */
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            animation: slideIn 0.3s;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-color: #28a745;
            color: #155724;
        }
        
        .alert-error {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
            color: #721c24;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Pagination améliorée */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 5px;
            border-radius: 4px;
            background: white;
            border: 1px solid #e0e0e0;
            color: #333;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .pagination a:hover {
            background-color: #f5f5f5;
            border-color: #d4af37;
        }
        
        .pagination .current {
            background-color: #d4af37;
            color: white;
            border-color: #d4af37;
        }
        
        /* Responsive design */
        @media (max-width: 992px) {
            .review-detail-grid {
                grid-template-columns: 1fr;
            }
            
            .data-table th, .data-table td {
                padding: 10px 12px;
            }
            
            .review-content {
                max-width: 150px;
            }
        }
        
        @media (max-width: 768px) {
            .review-filters {
                grid-template-columns: 1fr;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
            
            .modal-content {
                margin: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <a href="index.php">
                    <img src="../assets/img/layout/logo.png" alt="Logo" class="sidebar-logo">
                    <span>Elixir du Temps</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-heading">Navigation</h3>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                        <li><a href="products.php"><i class="fas fa-box"></i> Produits</a></li>
                        <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
                        <li><a href="collections.php"><i class="fas fa-layer-group"></i> Collections</a></li>
                        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                        <li><a href="users/index.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                        <li class="active"><a href="reviews.php"><i class="fas fa-star"></i> Avis Clients</a></li>
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
                        <img src="../uploads/users/<?= htmlspecialchars($userInfo['photo']) ?>" alt="<?= htmlspecialchars($userInfo['prenom']) ?>" class="avatar">
                    <?php else: ?>
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

            <div class="content-wrapper">
                <div class="content-header">
                    <h1>Gestion des avis clients</h1>
                    <div class="header-actions">
                        <a href="export.php?type=reviews" class="secondary-button"><i class="fas fa-file-export"></i> Exporter</a>
                    </div>
                </div>
                
                <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
                <?php endif; ?>
                
                <?php if ($pendingReviewsCount > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Il y a actuellement <strong><?= $pendingReviewsCount ?></strong> avis en attente de modération.</span>
                </div>
                <?php endif; ?>
                
                <!-- Filtres -->
                <div class="card">
                    <div class="card-header">
                        <h2>Filtres</h2>
                        <button type="button" id="toggleFilters" class="icon-button"><i class="fas fa-filter"></i></button>
                    </div>
                    <div class="card-content" id="filtersContent">
                        <form method="GET" action="reviews.php" id="filterForm">
                            <div class="review-filters">
                                <div class="filter-group">
                                    <label for="status">Statut</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Tous</option>
                                        <option value="en_attente" <?= $filterStatus === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                        <option value="approuve" <?= $filterStatus === 'approuve' ? 'selected' : '' ?>>Approuvé</option>
                                        <option value="rejete" <?= $filterStatus === 'rejete' ? 'selected' : '' ?>>Rejeté</option>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label for="product">Produit</label>
                                    <select name="product" id="product" class="form-control">
                                        <option value="">Tous les produits</option>
                                        <?php foreach ($products as $product): ?>
                                        <option value="<?= $product['id'] ?>" <?= $filterProduct == $product['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($product['nom']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label for="rating">Note</label>
                                    <select name="rating" id="rating" class="form-control">
                                        <option value="">Toutes les notes</option>
                                        <option value="5" <?= $filterRating === 5 ? 'selected' : '' ?>>5 étoiles</option>
                                        <option value="4" <?= $filterRating === 4 ? 'selected' : '' ?>>4 étoiles</option>
                                        <option value="3" <?= $filterRating === 3 ? 'selected' : '' ?>>3 étoiles</option>
                                        <option value="2" <?= $filterRating === 2 ? 'selected' : '' ?>>2 étoiles</option>
                                        <option value="1" <?= $filterRating === 1 ? 'selected' : '' ?>>1 étoile</option>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label for="keyword">Recherche</label>
                                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Mot-clé..." value="<?= htmlspecialchars($filterKeyword) ?>">
                                </div>
                                
                                <div class="filter-group">
                                    <label for="date_from">Date début</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="<?= $filterDateFrom ?>">
                                </div>
                                
                                <div class="filter-group">
                                    <label for="date_to">Date fin</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="<?= $filterDateTo ?>">
                                </div>
                                
                                <div class="filter-actions">
                                    <button type="submit" class="primary-button"><i class="fas fa-search"></i> Filtrer</button>
                                    <a href="reviews.php" class="secondary-button"><i class="fas fa-undo"></i> Réinitialiser</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Actions groupées -->
                <div class="bulk-actions">
                    <label class="custom-checkbox">
                        <input type="checkbox" id="selectAll">
                        <span class="checkbox-mark"></span>
                        Tout sélectionner
                    </label>
                    
                    <button id="bulkApproveBtn" class="primary-button" disabled><i class="fas fa-check"></i> Approuver</button>
                    <button id="bulkRejectBtn" class="secondary-button" disabled><i class="fas fa-times"></i> Rejeter</button>
                </div>
                
                <!-- Liste des avis -->
                <div class="card">
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="40px"><i class="fas fa-check-square"></i></th>
                                        <th>Produit</th>
                                        <th>Client</th>
                                        <th>Note</th>
                                        <th>Commentaire</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($reviews)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun avis trouvé avec les critères sélectionnés.</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($reviews as $review): ?>
                                        <tr>
                                            <td>
                                                <label class="custom-checkbox">
                                                    <input type="checkbox" class="review-checkbox" value="<?= $review['id'] ?>">
                                                    <span class="checkbox-mark"></span>
                                                </label>
                                            </td>
                                            <td><?= htmlspecialchars($review['produit_nom']) ?></td>
                                            <td><?= htmlspecialchars($review['utilisateur_nom']) ?></td>
                                            <td>
                                                <div class="star-rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <?php if ($i <= $review['note']): ?>
                                                            <i class="fas fa-star"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-star"></i>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="review-content" title="<?= htmlspecialchars($review['commentaire']) ?>">
                                                    <?= htmlspecialchars(substr($review['commentaire'], 0, 50)) ?>
                                                    <?= strlen($review['commentaire']) > 50 ? '...' : '' ?>
                                                </div>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($review['date_creation'])) ?></td>
                                            <td>
                                                <?php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch($review['statut']) {
                                                        case 'en_attente':
                                                            $statusClass = 'status-pending';
                                                            $statusText = 'En attente';
                                                            break;
                                                        case 'approuve':
                                                            $statusClass = 'status-approved';
                                                            $statusText = 'Approuvé';
                                                            break;
                                                        case 'rejete':
                                                            $statusClass = 'status-rejected';
                                                            $statusText = 'Rejeté';
                                                            break;
                                                    }
                                                ?>
                                                <span class="review-status <?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td class="table-actions">
                                                <button type="button" class="icon-button view-review" data-id="<?= $review['id'] ?>" data-product="<?= htmlspecialchars($review['produit_nom']) ?>" data-user="<?= htmlspecialchars($review['utilisateur_nom']) ?>" data-rating="<?= $review['note'] ?>" data-date="<?= date('d/m/Y H:i', strtotime($review['date_creation'])) ?>" data-comment="<?= htmlspecialchars($review['commentaire']) ?>" data-status="<?= $review['statut'] ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if ($review['statut'] === 'en_attente'): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                                    <button type="submit" class="icon-button success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                                    <button type="submit" class="icon-button danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                
                                                <form method="POST" class="d-inline delete-form">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                                    <button type="button" class="icon-button danger delete-review">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=1<?= isset($_GET['status']) ? '&status='.$_GET['status'] : '' ?>
                                                <?= isset($_GET['product']) ? '&product='.$_GET['product'] : '' ?>
                                                <?= isset($_GET['rating']) ? '&rating='.$_GET['rating'] : '' ?>
                                                <?= isset($_GET['keyword']) ? '&keyword='.$_GET['keyword'] : '' ?>
                                                <?= isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : '' ?>
                                                <?= isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : '' ?>">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="?page=<?= $page-1 ?><?= isset($_GET['status']) ? '&status='.$_GET['status'] : '' ?>
                                                            <?= isset($_GET['product']) ? '&product='.$_GET['product'] : '' ?>
                                                            <?= isset($_GET['rating']) ? '&rating='.$_GET['rating'] : '' ?>
                                                            <?= isset($_GET['keyword']) ? '&keyword='.$_GET['keyword'] : '' ?>
                                                            <?= isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : '' ?>
                                                            <?= isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : '' ?>">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php
                            // Afficher toujours les 2 premières pages
                            for ($i = 1; $i <= min(2, $totalPages); $i++) {
                                if ($i == $page) {
                                    echo "<span class=\"current\">$i</span>";
                                } else {
                                    echo "<a href=\"?page=$i" . 
                                        (isset($_GET['status']) ? '&status='.$_GET['status'] : '') .
                                        (isset($_GET['product']) ? '&product='.$_GET['product'] : '') .
                                        (isset($_GET['rating']) ? '&rating='.$_GET['rating'] : '') .
                                        (isset($_GET['keyword']) ? '&keyword='.$_GET['keyword'] : '') .
                                        (isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : '') .
                                        (isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : '') . "\">$i</a>";
                                }
                            }
                            
                            // Afficher ... si nécessaire
                            if ($page > 3) {
                                echo "<span>...</span>";
                            }
                            
                            // Afficher la page actuelle et les pages autour
                            for ($i = max(3, $page - 1); $i <= min($totalPages - 2, $page + 1); $i++) {
                                if ($i == $page) {
                                    echo "<span class=\"current\">$i</span>";
                                } else {
                                    echo "<a href=\"?page=$i" . 
                                        (isset($_GET['status']) ? '&status='.$_GET['status'] : '') .
                                        (isset($_GET['product']) ? '&product='.$_GET['product'] : '') .
                                        (isset($_GET['rating']) ? '&rating='.$_GET['rating'] : '') .
                                        (isset($_GET['keyword']) ? '&keyword='.$_GET['keyword'] : '') .
                                        (isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : '') .
                                        (isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : '') . "\">$i</a>";
                                }
                            }
                            
                            // Afficher ... si nécessaire
                            if ($page < $totalPages - 2) {
                                echo "<span>...</span>";
                            }
                            
                            // Afficher les 2 dernières pages si totalPages > 2
                            if ($totalPages > 2) {
                                for ($i = max($totalPages - 1, 3); $i <= $totalPages; $i++) {
                                    if ($i == $page) {
                                        echo "<span class=\"current\">$i</span>";
                                    } else {
                                        echo "<a href=\"?page=$i" . 
                                            (isset($_GET['status']) ? '&status='.$_GET['status'] : '') .
                                            (isset($_GET['product']) ? '&product='.$_GET['product'] : '') .
                                            (isset($_GET['rating']) ? '&rating='.$_GET['rating'] : '') .
                                            (isset($_GET['keyword']) ? '&keyword='.$_GET['keyword'] : '') .
                                            (isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : '') .
                                            (isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : '') . "\">$i</a>";
                                    }
                                }
                            }
                            ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page+1 ?><?= isset($_GET['status']) ? '&status='.$_GET['status'] : '' ?>
                                                            <?= isset($_GET['product']) ? '&product='.$_GET['product'] : '' ?>
                                                            <?= isset($_GET['rating']) ? '&rating='.$_GET['rating'] : '' ?>
                                                            <?= isset($_GET['keyword']) ? '&keyword='.$_GET['keyword'] : '' ?>
                                                            <?= isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : '' ?>
                                                            <?= isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : '' ?>">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="?page=<?= $totalPages ?><?= isset($_GET['status']) ? '&status='.$_GET['status'] : '' ?>
                                                                <?= isset($_GET['product']) ? '&product='.$_GET['product'] : '' ?>
                                                                <?= isset($_GET['rating']) ? '&rating='.$_GET['rating'] : '' ?>
                                                                <?= isset($_GET['keyword']) ? '&keyword='.$_GET['keyword'] : '' ?>
                                                                <?= isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : '' ?>
                                                                <?= isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : '' ?>">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal de détail -->
    <div class="review-detail-modal" id="reviewDetailModal">
        <div class="modal-content fade-in">
            <span class="close-modal" id="closeModal"><i class="fas fa-times"></i></span>
            <h2>Détail de l'avis</h2>
            
            <div class="review-detail-grid">
                <div class="review-info">
                    <div class="detail-heading">Produit</div>
                    <div class="detail-value" id="reviewProduct"></div>
                    
                    <div class="detail-heading">Client</div>
                    <div class="detail-value" id="reviewUser"></div>
                    
                    <div class="detail-heading">Date</div>
                    <div class="detail-value" id="reviewDate"></div>
                </div>
                
                <div class="review-stats">
                    <div class="detail-heading">Note</div>
                    <div class="detail-value">
                        <div class="static-stars" id="reviewStars"></div>
                    </div>
                    
                    <div class="detail-heading">Statut</div>
                    <div class="detail-value" id="reviewStatus"></div>
                </div>
                
                <div class="detail-comment" id="reviewComment"></div>
            </div>
            
            <div class="modal-actions" id="modalActions">
                <!-- Les actions seront ajoutées dynamiquement par JS -->
            </div>
        </div>
    </div>
    
    <!-- Formulaires pour actions groupées -->
    <form id="bulkApproveForm" method="POST" style="display:none;">
        <input type="hidden" name="action" value="bulk_approve">
        <input type="hidden" name="review_ids" id="bulkApproveIds">
    </form>
    
    <form id="bulkRejectForm" method="POST" style="display:none;">
        <input type="hidden" name="action" value="bulk_reject">
        <input type="hidden" name="review_ids" id="bulkRejectIds">
    </form>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle filtres
        const toggleFilters = document.getElementById('toggleFilters');
        const filtersContent = document.getElementById('filtersContent');
        
        toggleFilters.addEventListener('click', function() {
            if (filtersContent.style.display === 'none') {
                filtersContent.style.display = 'block';
                toggleFilters.innerHTML = '<i class="fas fa-times"></i>';
            } else {
                filtersContent.style.display = 'none';
                toggleFilters.innerHTML = '<i class="fas fa-filter"></i>';
            }
        });
        
        // Gestion du modal de détail
        const modal = document.getElementById('reviewDetailModal');
        const closeModal = document.getElementById('closeModal');
        const viewButtons = document.querySelectorAll('.view-review');
        
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const product = this.getAttribute('data-product');
                const user = this.getAttribute('data-user');
                const rating = this.getAttribute('data-rating');
                const date = this.getAttribute('data-date');
                const comment = this.getAttribute('data-comment');
                const status = this.getAttribute('data-status');
                
                // Remplir le modal avec les détails
                document.getElementById('reviewProduct').textContent = product;
                document.getElementById('reviewUser').textContent = user;
                document.getElementById('reviewDate').textContent = date;
                
                // Afficher les étoiles
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= rating) {
                        starsHtml += '<i class="fas fa-star"></i>';
                    } else {
                        starsHtml += '<i class="far fa-star"></i>';
                    }
                }
                document.getElementById('reviewStars').innerHTML = starsHtml;
                
                // Afficher le commentaire
                document.getElementById('reviewComment').textContent = comment;
                
                // Afficher le statut
                let statusHtml = '';
                switch(status) {
                    case 'en_attente':
                        statusHtml = '<span class="review-status status-pending">En attente</span>';
                        break;
                    case 'approuve':
                        statusHtml = '<span class="review-status status-approved">Approuvé</span>';
                        break;
                    case 'rejete':
                        statusHtml = '<span class="review-status status-rejected">Rejeté</span>';
                        break;
                }
                document.getElementById('reviewStatus').innerHTML = statusHtml;
                
                // Afficher les actions appropriées
                const modalActions = document.getElementById('modalActions');
                modalActions.innerHTML = '';
                
                if (status === 'en_attente') {
                    modalActions.innerHTML = `
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="review_id" value="${id}">
                            <button type="submit" class="primary-button">
                                <i class="fas fa-check"></i> Approuver
                            </button>
                        </form>
                        
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="review_id" value="${id}">
                            <button type="submit" class="secondary-button">
                                <i class="fas fa-times"></i> Rejeter
                            </button>
                        </form>
                    `;
                }
                
                // Ajouter l'action de suppression
                modalActions.innerHTML += `
                    <form method="POST" class="d-inline delete-form">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="review_id" value="${id}">
                        <button type="button" class="danger-button delete-modal-review">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                `;
                
                // Ajouter l'événement de confirmation pour la suppression
                document.querySelector('.delete-modal-review').addEventListener('click', function() {
                    if (confirm('Êtes-vous sûr de vouloir supprimer cet avis ? Cette action est irréversible.')) {
                        this.closest('form').submit();
                    }
                });
                
                // Afficher le modal
                modal.style.display = 'block';
                
                // Ajouter la classe pour l'animation
                setTimeout(() => {
                    document.querySelector('.modal-content').classList.add('fade-in');
                }, 10);
            });
        });
        
        // Fermer le modal
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // Fermer le modal en cliquant en dehors
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        // Confirmation pour les suppressions
        document.querySelectorAll('.delete-review').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer cet avis ? Cette action est irréversible.')) {
                    this.closest('form').submit();
                }
            });
        });
        
        // Sélection de tous les avis
        const selectAll = document.getElementById('selectAll');
        const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
        
        selectAll.addEventListener('change', function() {
            reviewCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkButtons();
        });
        
        // Mise à jour du statut des boutons d'action groupée
        reviewCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBulkButtons();
                
                // Vérifier si tous les avis sont sélectionnés
                const allChecked = [...reviewCheckboxes].every(cb => cb.checked);
                selectAll.checked = allChecked;
            });
        });
        
        function updateBulkButtons() {
            const checkedReviews = document.querySelectorAll('.review-checkbox:checked');
            const bulkApproveBtn = document.getElementById('bulkApproveBtn');
            const bulkRejectBtn = document.getElementById('bulkRejectBtn');
            
            if (checkedReviews.length > 0) {
                bulkApproveBtn.disabled = false;
                bulkRejectBtn.disabled = false;
            } else {
                bulkApproveBtn.disabled = true;
                bulkRejectBtn.disabled = true;
            }
        }
        
        // Actions groupées
        document.getElementById('bulkApproveBtn').addEventListener('click', function() {
            const checkedReviews = document.querySelectorAll('.review-checkbox:checked');
            if (checkedReviews.length === 0) return;
            
            if (confirm(`Êtes-vous sûr de vouloir approuver ${checkedReviews.length} avis ?`)) {
                const ids = [...checkedReviews].map(cb => cb.value).join(',');
                document.getElementById('bulkApproveIds').value = ids;
                document.getElementById('bulkApproveForm').submit();
            }
        });
        
        document.getElementById('bulkRejectBtn').addEventListener('click', function() {
            const checkedReviews = document.querySelectorAll('.review-checkbox:checked');
            if (checkedReviews.length === 0) return;
            
            if (confirm(`Êtes-vous sûr de vouloir rejeter ${checkedReviews.length} avis ?`)) {
                const ids = [...checkedReviews].map(cb => cb.value).join(',');
                document.getElementById('bulkRejectIds').value = ids;
                document.getElementById('bulkRejectForm').submit();
            }
        });
        
        // Menu déroulant utilisateur
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
                e.stopPropagation();
                
                const isOpen = dropdownMenu.style.display === 'block';
                
                if (isOpen) {
                    dropdownArrow.style.transform = 'rotate(0deg)';
                    dropdownMenu.style.opacity = '0';
                    dropdownMenu.style.transform = 'translateY(-10px)';
                    
                    setTimeout(() => {
                        dropdownMenu.style.display = 'none';
                    }, 200);
                } else {
                    dropdownMenu.style.display = 'block';
                    dropdownArrow.style.transform = 'rotate(180deg)';
                    
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
            
            // Empêcher la fermeture quand on clique sur les éléments du menu
            dropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
    </script>
</body>
</html>