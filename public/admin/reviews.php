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
try {
    $countSql = "SELECT COUNT(*) FROM avis a 
                 LEFT JOIN produits p ON a.produit_id = p.id 
                 LEFT JOIN utilisateurs u ON a.utilisateur_id = u.id 
                 $whereClause";

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalReviews = $countStmt->fetchColumn();
    $totalPages = ceil($totalReviews / $limit);
} catch (PDOException $e) {
    error_log("Erreur de comptage des avis: " . $e->getMessage());
    $totalReviews = 0;
    $totalPages = 1;
}

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


// Simplification du code de pagination avec une fonction
function getPaginationUrl($newPage) {
    $queryParams = $_GET;
    $queryParams['page'] = $newPage;
    return '?' . http_build_query($queryParams);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des avis clients - Elixir du Temps</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/reviews.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/header.js" defer></script>
    <style>
        /* Améliorations visuelles pour la gestion des avis */
        .content-wrapper {
            padding: 25px;
            background: #f8f9fa;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .content-header {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-header h1 {
            font-size: 1.8rem;
            color: #333;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .content-header h1 i {
            color: #d4af37;
        }
        
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 18px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 {
            font-size: 1.3rem;
            margin: 0;
            color: #495057;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-header h2 i {
            color: #d4af37;
            font-size: 1.1rem;
        }
        
        .card-content {
            padding: 20px;
        }
        
        .review-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.25);
            outline: none;
        }
        
        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            margin-top: auto;
        }
        
        .primary-button, .secondary-button, .danger-button {
            padding: 10px 18px;
            border-radius: 5px;
            border: none;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .primary-button {
            background: linear-gradient(45deg, #d4af37, #e6c863);
            color: white;
            box-shadow: 0 4px 8px rgba(212, 175, 55, 0.15);
        }
        
        .primary-button:hover:not(:disabled) {
            background: linear-gradient(45deg, #c4a030, #d6b853);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(212, 175, 55, 0.2);
        }
        
        .secondary-button {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #ced4da;
        }
        
        .secondary-button:hover:not(:disabled) {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        .danger-button {
            background: linear-gradient(45deg, #dc3545, #e05d6a);
            color: white;
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.15);
        }
        
        .danger-button:hover:not(:disabled) {
            background: linear-gradient(45deg, #c82333, #d04356);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(220, 53, 69, 0.2);
        }
        
        .primary-button:disabled, .secondary-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .icon-button {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            border: none;
            background: none;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #495057;
        }
        
        .icon-button:hover {
            background-color: rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
        
        .icon-button.success {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .icon-button.success:hover {
            background-color: rgba(40, 167, 69, 0.15);
        }
        
        .icon-button.danger {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        .icon-button.danger:hover {
            background-color: rgba(220, 53, 69, 0.15);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .data-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .data-table tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.04);
        }
        
        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        
        .table-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        
        .d-inline {
            display: inline-block;
        }
        
        .star-rating {
            color: #ffc107;
            display: flex;
        }
        
        .review-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .status-approved {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status-rejected {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .text-center {
            text-align: center;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeInDown 0.4s ease;
        }
        
        .alert i {
            font-size: 1.2rem;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid #28a745;
            color: #28a745;
        }
        
        .alert-error {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            color: #dc3545;
        }
        
        .alert-info {
            background-color: rgba(13, 110, 253, 0.1);
            border-left: 4px solid #0d6efd;
            color: #0d6efd;
        }
        
        .bulk-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        
        .custom-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        
        .checkbox-mark {
            position: relative;
            height: 20px;
            width: 20px;
            background-color: #f8f9fa;
            border: 2px solid #ced4da;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .custom-checkbox:hover .checkbox-mark {
            border-color: #d4af37;
        }
        
        .custom-checkbox input:checked ~ .checkbox-mark {
            background-color: #d4af37;
            border-color: #d4af37;
        }
        
        .checkbox-mark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        .custom-checkbox input:checked ~ .checkbox-mark:after {
            display: block;
        }
        
        .review-content {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 25px;
        }
        
        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 35px;
            height: 35px;
            padding: 0 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        
        .pagination a {
            background: white;
            border: 1px solid #dee2e6;
            color: #495057;
        }
        
        .pagination a:hover {
            background: #f8f9fa;
            border-color: #d4af37;
            transform: translateY(-2px);
        }
        
        .pagination span.current {
            background: #d4af37;
            color: white;
            border: 1px solid #d4af37;
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
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 650px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            padding: 25px;
            opacity: 0;
            transform: scale(0.9);
            transition: all 0.3s ease;
        }
        
        .modal-content.fade-in {
            opacity: 1;
            transform: scale(1);
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #adb5bd;
            transition: all 0.2s ease;
        }
        
        .close-modal:hover {
            color: #495057;
            transform: rotate(90deg);
        }
        
        .review-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        
        .detail-comment {
            grid-column: 1 / 3;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #d4af37;
            white-space: pre-line;
            margin-top: 10px;
        }
        
        .detail-heading {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .detail-value {
            margin-bottom: 15px;
        }
        
        .static-stars {
            color: #ffc107;
            font-size: 1.2rem;
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 992px) {
            .review-filters {
                flex-direction: column;
            }
            
            .filter-actions {
                margin-top: 15px;
                width: 100%;
            }
            
            .review-detail-grid {
                grid-template-columns: 1fr;
            }
            
            .detail-comment {
                grid-column: 1;
            }
            
            .modal-actions {
                flex-wrap: wrap;
            }
            
            .data-table th:nth-child(1),
            .data-table td:nth-child(1),
            .data-table th:nth-child(6),
            .data-table td:nth-child(6) {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .bulk-actions {
                flex-wrap: wrap;
            }
            
            .header-actions {
                margin-top: 10px;
            }
            
            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Intégration du template de sidebar -->
        <?php 
        $admin_root = '';
        include 'templates/sidebar.php'; 
        ?>

        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header
            $admin_root = '';
            
            // Personnaliser la recherche pour la page avis
            $search_placeholder = "Rechercher un avis...";
            $search_action = "reviews.php";
            $search_param = "keyword";
            $search_value = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            
            include 'templates/header.php'; 
            ?>

            <div class="content-wrapper">
                <div class="content-header">
                    <h1><i class="fas fa-star"></i> Gestion des avis clients</h1>
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
                        <h2><i class="fas fa-filter"></i> Filtres</h2>
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
                    <div class="card-header">
                        <h2><i class="fas fa-comments"></i> Liste des avis</h2>
                        <span class="badge"><?= number_format($totalReviews) ?> avis</span>
                    </div>
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
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                                    <button type="submit" class="icon-button success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" class="d-inline">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                                    <button type="submit" class="icon-button danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                
                                                <form method="POST" class="d-inline delete-form">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
                                <a href="<?= getPaginationUrl(1) ?>">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="<?= getPaginationUrl($page-1) ?>">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php
                            // Afficher toujours les 2 premières pages
                            for ($i = 1; $i <= min(2, $totalPages); $i++) {
                                if ($i == $page) {
                                    echo "<span class=\"current\">$i</span>";
                                } else {
                                    echo "<a href=\"" . getPaginationUrl($i) . "\">$i</a>";
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
                                    echo "<a href=\"" . getPaginationUrl($i) . "\">$i</a>";
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
                                        echo "<a href=\"" . getPaginationUrl($i) . "\">$i</a>";
                                    }
                                }
                            }
                            ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="<?= getPaginationUrl($page+1) ?>">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="<?= getPaginationUrl($totalPages) ?>">
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
        <div class="modal-content">
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
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="bulk_approve">
        <input type="hidden" name="review_ids" id="bulkApproveIds">
    </form>
    
    <form id="bulkRejectForm" method="POST" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="review_id" value="${id}">
                            <button type="submit" class="primary-button">
                                <i class="fas fa-check"></i> Approuver
                            </button>
                        </form>
                        
                        <form method="POST" class="d-inline">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
                modal.style.display = 'flex';
                
                // Ajouter la classe pour l'animation
                setTimeout(() => {
                    document.querySelector('#reviewDetailModal .modal-content').classList.add('fade-in');
                }, 10);
            });
        });
        
        // Fermer le modal
        closeModal.addEventListener('click', function() {
            document.querySelector('#reviewDetailModal .modal-content').classList.remove('fade-in');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        });
        
        // Fermer le modal en cliquant en dehors
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                document.querySelector('#reviewDetailModal .modal-content').classList.remove('fade-in');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
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
        
        // Animation d'entrée pour les lignes du tableau
        const tableRows = document.querySelectorAll('.data-table tbody tr');
        tableRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            row.style.transition = `all 0.3s ease ${index * 0.05}s`;
            
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 100);
        });
    });
    </script>
</body>
</html>