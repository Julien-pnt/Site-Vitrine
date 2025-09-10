<?php
// Protection CSRF ajoutée automatiquement
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialisation de la session et vérification d'authentification admin
session_start();
require_once '../../php/config/database.php';
require_once '../../php/utils/auth.php';
require_once '../../php/models/Order.php';
require_once '../../php/utils/Logger.php';

// Redirection si l'utilisateur n'est pas connecté en tant qu'admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /Site-Vitrine/public/pages/auth/login.php?redirect=admin/orders');
    exit;
}

// Initialiser le modèle de commande et le logger
$orderModel = new Order($pdo);
$logger = new Logger($pdo);

// Gestion de la pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 15; // Plus que les autres pages car les commandes sont importantes
$offset = ($page - 1) * $perPage;

// Gestion des filtres et de la recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Traitement des actions sur les commandes
if (isset($_POST['action']) && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $action = $_POST['action'];
    $success = false;
    $message = '';
    
    switch ($action) {
        case 'update_status':
            $newStatus = $_POST['status'];
            $success = $orderModel->updateOrderStatus($orderId, $newStatus);
            $message = $success 
                ? 'Statut de la commande mis à jour avec succès' 
                : 'Erreur lors de la mise à jour du statut';
                
            // Logger l'activité
            if ($success) {
                $orderRef = $orderModel->getOrderReference($orderId);
                $logger->info('admin', 'update_order_status', [
                    'order_id' => $orderId,
                    'order_ref' => $orderRef,
                    'old_status' => $_POST['old_status'],
                    'new_status' => $newStatus
                ]);
            }
            break;
            
        case 'add_note':
            $note = trim($_POST['note']);
            if (!empty($note)) {
                $success = $orderModel->addOrderNote($orderId, $note);
                $message = $success 
                    ? 'Note ajoutée avec succès' 
                    : 'Erreur lors de l\'ajout de la note';
                    
                // Logger l'activité
                if ($success) {
                    $orderRef = $orderModel->getOrderReference($orderId);
                    $logger->info('admin', 'add_order_note', [
                        'order_id' => $orderId,
                        'order_ref' => $orderRef
                    ]);
                }
            } else {
                $message = 'Veuillez saisir une note';
            }
            break;
    }
    
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $success ? 'success' : 'danger';
    
    // Rediriger pour éviter les soumissions multiples
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Récupérer le nombre total de commandes pour la pagination
$totalOrders = $orderModel->getTotalCount($search, $statusFilter, $dateFrom, $dateTo, $filter);
$totalPages = ceil($totalOrders / $perPage);

// Récupérer les commandes avec pagination et filtres
$orders = $orderModel->getAllOrders($perPage, $offset, $search, $statusFilter, $dateFrom, $dateTo, $filter);

// Récupérer le message de session s'il existe
$message = $_SESSION['message'] ?? null;
$messageType = $_SESSION['message_type'] ?? 'info';

// Supprimer le message après l'avoir récupéré
if (isset($_SESSION['message'])) {
    unset($_SESSION['message'], $_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des commandes - Administration</title>
    <link rel="shortcut icon" href="../assets/img/layout/icon2.png" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/orders.css">
    <link rel="stylesheet" href="css/sidebar.css">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/header.js" defer></script>
    <style>
        :root {
            --primary-color: #d4af37;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --primary-light: rgba(212, 175, 55, 0.1);
            --transition: all 0.3s ease;
        }
        
        /* Style pour la page de commandes */
        .orders-container {
            margin-bottom: 2rem;
        }
        
        .card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 1.25rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-header h2 i {
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .page-header h1 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 700;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .page-header h1 i {
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .controls-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            align-items: center;
        }
        
        .search-form {
            flex-grow: 1;
            display: flex;
            gap: 0.5rem;
        }
        
        .search-form input {
            flex-grow: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .filters-dropdown {
            position: relative;
        }
        
        .filters-dropdown-toggle {
            background-color: #fff;
            border: 1px solid #ced4da;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .filters-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 1000;
            min-width: 300px;
            padding: 1rem;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: none;
        }
        
        .filters-dropdown-menu.show {
            display: block;
            animation: fadeIn 0.2s ease;
        }
        
        .filter-group {
            margin-bottom: 1rem;
        }
        
        .filter-group:last-child {
            margin-bottom: 0;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #495057;
        }
        
        .status-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .status-filter label {
            display: flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            background-color: #f8f9fa;
            cursor: pointer;
            font-size: 0.875rem;
        }
        
        .status-filter input {
            margin-right: 0.25rem;
        }
        
        .date-inputs {
            display: flex;
            gap: 0.5rem;
        }
        
        .date-inputs input {
            flex: 1;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        
        .filter-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .quick-filters {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .quick-filter {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #ced4da;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .quick-filter:hover, .quick-filter.active {
            background-color: var(--primary-light);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            line-height: 1.5;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            gap: 0.5rem;
            white-space: nowrap;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #c49e30;
            transform: translateY(-2px);
        }
        
        .btn-outline-secondary {
            background-color: transparent;
            border-color: #ced4da;
            color: var(--secondary-color);
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .btn-outline-primary {
            background-color: transparent;
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: #fff;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background-color: #d4edda;
            border-left: 4px solid var(--success-color);
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-left: 4px solid var(--danger-color);
            color: #721c24;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-left: 4px solid var(--info-color);
            color: #0c5460;
        }
        
        .alert-icon {
            font-size: 1.25rem;
        }
        
        .alert-content {
            flex: 1;
        }
        
        .alert-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
            padding: 0;
            line-height: 1;
        }
        
        .alert-close:hover {
            opacity: 1;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, 
        .data-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .data-table tbody tr:hover {
            background-color: var(--primary-light);
        }

        .data-table .reference-column {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .data-table .date-column {
            width: 120px;
            font-size: 0.9rem;
        }
        
        .data-table .status-column {
            width: 130px;
        }
        
        .data-table .price-column {
            width: 100px;
            text-align: right;
            font-weight: 600;
        }
        
        .data-table .actions-column {
            width: 100px;
            text-align: center;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background-color: rgba(108, 117, 125, 0.15);
            color: #6c757d;
        }
        
        .status-paid {
            background-color: rgba(23, 162, 184, 0.15);
            color: #17a2b8;
        }
        
        .status-preparing {
            background-color: rgba(255, 193, 7, 0.15);
            color: #856404;
        }
        
        .status-shipped {
            background-color: rgba(0, 123, 255, 0.15);
            color: #007bff;
        }
        
        .status-delivered {
            background-color: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        
        .status-cancelled {
            background-color: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }
        
        .status-refunded {
            background-color: rgba(111, 66, 193, 0.15);
            color: #6f42c1;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        
        .pagination-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 0.5rem;
            border-radius: 4px;
            border: 1px solid #ced4da;
            background-color: #ffffff;
            color: #495057;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .pagination-item:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background-color: var(--primary-light);
        }
        
        .pagination-item.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }
        
        .pagination-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Styles pour le modal de détail de commande */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal-dialog {
            width: 100%;
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-height: calc(100vh - 60px);
            display: flex;
            flex-direction: column;
        }
        
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .modal-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            padding: 0;
            cursor: pointer;
            color: #6c757d;
        }
        
        .modal-close:hover {
            color: #343a40;
        }
        
        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
        }
        
        .order-detail-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .order-info {
            flex: 1;
            min-width: 300px;
        }
        
        .order-customer {
            flex: 1;
            min-width: 300px;
        }
        
        .order-status-update {
            width: 100%;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .order-status-form {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .order-status-form select {
            flex: 1;
            min-width: 200px;
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        
        .order-items {
            margin-bottom: 1.5rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #e9ecef;
            gap: 1rem;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            background-color: #f8f9fa;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .item-reference {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .item-price {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            min-width: 120px;
        }
        
        .item-quantity {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .item-total {
            font-weight: 600;
        }
        
        .order-summary {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .summary-row:last-child {
            margin-bottom: 0;
            padding-top: 0.5rem;
            border-top: 1px solid #e9ecef;
            font-weight: 600;
        }
        
        .customer-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .address-card {
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        
        .address-card h4 {
            margin-top: 0;
            margin-bottom: 0.75rem;
            font-size: 1rem;
            color: #495057;
        }
        
        .address-card p {
            margin: 0;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .notes-section {
            margin-bottom: 1.5rem;
        }
        
        .note-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }
        
        .note-form textarea {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            min-height: 60px;
            resize: vertical;
        }
        
        .info-heading {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #343a40;
        }
        
        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .info-list li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: flex-start;
        }
        
        .info-list .label {
            font-weight: 500;
            min-width: 120px;
            color: #6c757d;
        }
        
        .info-list .value {
            flex: 1;
        }
        
        @media (max-width: 768px) {
            .info-list li {
                flex-direction: column;
            }
            
            .info-list .label {
                margin-bottom: 0.25rem;
            }
            
            .order-status-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .order-item {
                flex-wrap: wrap;
            }
            
            .item-price {
                width: 100%;
                flex-direction: row;
                justify-content: space-between;
                margin-top: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Utiliser le template de sidebar -->
        <?php 
        // Définit la racine relative pour les liens dans la sidebar
        $admin_root = '';
        include 'templates/sidebar.php'; 
        ?>

        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header
            $admin_root = '';
            
            // Personnaliser la recherche pour la page commandes
            $search_placeholder = "Rechercher une commande...";
            $search_action = "orders.php";
            $search_param = "search";
            $search_value = $search;
            
            include 'templates/header.php'; 
            ?>

            <div class="content">
                <div class="page-header">
                    <h1><i class="fas fa-shopping-cart"></i> Gestion des commandes</h1>
                    
                    <div class="export-section">
                        <a href="export-orders.php" class="btn btn-outline-primary">
                            <i class="fas fa-file-export"></i> Exporter
                        </a>
                    </div>
                </div>

                <!-- Message d'alerte (succès/erreur) -->
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?>">
                        <div class="alert-icon">
                            <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : ($messageType === 'danger' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                        </div>
                        <div class="alert-content"><?= $message ?></div>
                        <button type="button" class="alert-close">&times;</button>
                    </div>
                <?php endif; ?>

                <div class="orders-container">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-list"></i> Liste des commandes</h2>
                        </div>
                        <div class="card-body">
                            <!-- Contrôles de recherche et filtres -->
                            <div class="controls-container">
                                <form action="orders.php" method="GET" class="search-form">
                                    <input type="text" name="search" placeholder="Rechercher par référence, client ou email..." value="<?= htmlspecialchars($search) ?>">
                                    <?php if (!empty($statusFilter)): ?>
                                        <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
                                    <?php endif; ?>
                                    <?php if (!empty($dateFrom)): ?>
                                        <input type="hidden" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
                                    <?php endif; ?>
                                    <?php if (!empty($dateTo)): ?>
                                        <input type="hidden" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">
                                    <?php endif; ?>
                                    <?php if (!empty($filter)): ?>
                                        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                                    <?php endif; ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Rechercher
                                    </button>
                                    <?php if (!empty($search) || !empty($statusFilter) || !empty($dateFrom) || !empty($dateTo) || !empty($filter)): ?>
                                        <a href="orders.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Effacer filtres
                                        </a>
                                    <?php endif; ?>
                                </form>
                                
                                <!-- Filtre avancés -->
                                <div class="filters-dropdown">
                                    <button type="button" class="filters-dropdown-toggle" id="filtersToggle">
                                        <i class="fas fa-filter"></i> Filtres avancés
                                    </button>
                                    <div class="filters-dropdown-menu" id="filtersMenu">
                                        <form action="orders.php" method="GET">
                                            <?php if (!empty($search)): ?>
                                                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                            <?php endif; ?>
                                            
                                            <div class="filter-group">
                                                <label class="filter-label">Statut</label>
                                                <div class="status-filter">
                                                    <label>
                                                        <input type="radio" name="status" value="" <?= empty($statusFilter) ? 'checked' : '' ?>>
                                                        Tous
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="en_attente" <?= $statusFilter === 'en_attente' ? 'checked' : '' ?>>
                                                        En attente
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="payee" <?= $statusFilter === 'payee' ? 'checked' : '' ?>>
                                                        Payée
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="en_preparation" <?= $statusFilter === 'en_preparation' ? 'checked' : '' ?>>
                                                        En préparation
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="expediee" <?= $statusFilter === 'expediee' ? 'checked' : '' ?>>
                                                        Expédiée
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="livree" <?= $statusFilter === 'livree' ? 'checked' : '' ?>>
                                                        Livrée
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="annulee" <?= $statusFilter === 'annulee' ? 'checked' : '' ?>>
                                                        Annulée
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="remboursee" <?= $statusFilter === 'remboursee' ? 'checked' : '' ?>>
                                                        Remboursée
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="filter-group">
                                                <label class="filter-label">Période</label>
                                                <div class="date-inputs">
                                                    <input type="date" name="date_from" placeholder="Date début" value="<?= htmlspecialchars($dateFrom) ?>">
                                                    <input type="date" name="date_to" placeholder="Date fin" value="<?= htmlspecialchars($dateTo) ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="filter-actions">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-check"></i> Appliquer
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                                                    <i class="fas fa-undo"></i> Réinitialiser
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Filtres rapides -->
                            <div class="quick-filters">
                                <a href="orders.php" class="quick-filter <?= empty($filter) && empty($statusFilter) ? 'active' : '' ?>">
                                    Toutes
                                </a>
                                <a href="orders.php?filter=today" class="quick-filter <?= $filter === 'today' ? 'active' : '' ?>">
                                    Aujourd'hui
                                </a>
                                <a href="orders.php?filter=yesterday" class="quick-filter <?= $filter === 'yesterday' ? 'active' : '' ?>">
                                    Hier
                                </a>
                                <a href="orders.php?filter=this-week" class="quick-filter <?= $filter === 'this-week' ? 'active' : '' ?>">
                                    Cette semaine
                                </a>
                                <a href="orders.php?filter=this-month" class="quick-filter <?= $filter === 'this-month' ? 'active' : '' ?>">
                                    Ce mois
                                </a>
                                <a href="orders.php?filter=delayed" class="quick-filter <?= $filter === 'delayed' ? 'active' : '' ?>">
                                    En retard
                                </a>
                                <a href="orders.php?status=payee" class="quick-filter <?= $statusFilter === 'payee' && empty($filter) ? 'active' : '' ?>">
                                    À préparer
                                </a>
                                <a href="orders.php?status=en_preparation" class="quick-filter <?= $statusFilter === 'en_preparation' && empty($filter) ? 'active' : '' ?>">
                                    À expédier
                                </a>
                            </div>

                            <!-- Tableau des commandes -->
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Référence</th>
                                            <th>Client</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($orders)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Aucune commande trouvée</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($orders as $order): ?>
                                                <?php 
                                                // Déterminer la classe CSS du statut
                                                $statusClass = '';
                                                $statusLabel = '';
                                                
                                                switch ($order['statut']) {
                                                    case 'en_attente':
                                                        $statusClass = 'status-pending';
                                                        $statusLabel = 'En attente';
                                                        break;
                                                    case 'payee':
                                                        $statusClass = 'status-paid';
                                                        $statusLabel = 'Payée';
                                                        break;
                                                    case 'en_preparation':
                                                        $statusClass = 'status-preparing';
                                                        $statusLabel = 'En préparation';
                                                        break;
                                                    case 'expediee':
                                                        $statusClass = 'status-shipped';
                                                        $statusLabel = 'Expédiée';
                                                        break;
                                                    case 'livree':
                                                        $statusClass = 'status-delivered';
                                                        $statusLabel = 'Livrée';
                                                        break;
                                                    case 'annulee':
                                                        $statusClass = 'status-cancelled';
                                                        $statusLabel = 'Annulée';
                                                        break;
                                                    case 'remboursee':
                                                        $statusClass = 'status-refunded';
                                                        $statusLabel = 'Remboursée';
                                                        break;
                                                }
                                                ?>
                                                <tr>
                                                    <td class="reference-column"><?= htmlspecialchars($order['reference']) ?></td>
                                                    <td><?= htmlspecialchars($order['client_nom']) ?></td>
                                                    <td class="date-column"><?= date('d/m/Y H:i', strtotime($order['date_commande'])) ?></td>
                                                    <td class="status-column">
                                                        <span class="status-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                                    </td>
                                                    <td class="price-column"><?= number_format($order['total'], 2, ',', ' ') ?> €</td>
                                                    <td class="actions-column">
                                                        <div class="actions">
                                                            <button class="btn btn-sm btn-outline-primary view-order-btn" data-id="<?= $order['id'] ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <a href="invoice.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Facture">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                        </div>
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
                                        <a href="orders.php?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($statusFilter) ? '&status=' . urlencode($statusFilter) : '' ?><?= !empty($dateFrom) ? '&date_from=' . urlencode($dateFrom) : '' ?><?= !empty($dateTo) ? '&date_to=' . urlencode($dateTo) : '' ?><?= !empty($filter) ? '&filter=' . urlencode($filter) : '' ?>" class="pagination-item">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="pagination-item disabled"><i class="fas fa-chevron-left"></i></span>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Afficher un nombre limité de pages
                                    $startPage = max($page - 2, 1);
                                    $endPage = min($startPage + 4, $totalPages);
                                    
                                    // Ajuster startPage si nécessaire
                                    if ($endPage - $startPage < 4) {
                                        $startPage = max($endPage - 4, 1);
                                    }
                                    
                                    for ($i = $startPage; $i <= $endPage; $i++): 
                                    ?>
                                        <a href="orders.php?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($statusFilter) ? '&status=' . urlencode($statusFilter) : '' ?><?= !empty($dateFrom) ? '&date_from=' . urlencode($dateFrom) : '' ?><?= !empty($dateTo) ? '&date_to=' . urlencode($dateTo) : '' ?><?= !empty($filter) ? '&filter=' . urlencode($filter) : '' ?>" 
                                           class="pagination-item <?= $i === $page ? 'active' : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <a href="orders.php?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($statusFilter) ? '&status=' . urlencode($statusFilter) : '' ?><?= !empty($dateFrom) ? '&date_from=' . urlencode($dateFrom) : '' ?><?= !empty($dateTo) ? '&date_to=' . urlencode($dateTo) : '' ?><?= !empty($filter) ? '&filter=' . urlencode($filter) : '' ?>" class="pagination-item">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="pagination-item disabled"><i class="fas fa-chevron-right"></i></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de détail de commande -->
    <div class="modal" id="orderDetailModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Détail de la commande <span id="orderReference"></span></h3>
                    <button type="button" class="modal-close">&times;</button>
                </div>
                <div class="modal-body" id="orderDetailContent">
                    <div class="text-center p-5">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Chargement des détails...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour les détails de commande -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Détail de la commande</h2>
                <button type="button" class="close" onclick="closeOrderModal()">&times;</button>
            </div>
            <div class="modal-body" id="orderDetailsModal">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du filtre déroulant
            const filtersToggle = document.getElementById('filtersToggle');
            const filtersMenu = document.getElementById('filtersMenu');
            
            if (filtersToggle && filtersMenu) {
                filtersToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    filtersMenu.classList.toggle('show');
                });
                
                document.addEventListener('click', function(e) {
                    if (!filtersMenu.contains(e.target) && e.target !== filtersToggle) {
                        filtersMenu.classList.remove('show');
                    }
                });
                
                // Réinitialiser les filtres
                document.getElementById('resetFilters').addEventListener('click', function() {
                    window.location.href = 'orders.php';
                });
            }
            
            // Gestion des alertes
            const closeButtons = document.querySelectorAll('.alert-close');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert');
                    if (alert) {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }
                });
            });
            
            // Gestion du modal de détail de commande
            const orderModal = document.getElementById('orderDetailModal');
            const orderDetailContent = document.getElementById('orderDetailContent');
            const orderReference = document.getElementById('orderReference');
            const modalClose = document.querySelector('.modal-close');
            
            // Fermer le modal
            function closeModal() {
                orderModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            
            // Ouvrir le modal et charger les détails
            document.querySelectorAll('.view-order-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-id');
                    
                    // Réinitialiser le contenu et afficher le modal
                    orderDetailContent.innerHTML = `
                        <div class="text-center p-5">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p>Chargement des détails...</p>
                        </div>
                    `;
                    orderModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    
                    // Charger les détails de la commande via AJAX
                    fetch(`api/get-order-details.php?id=${orderId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erreur réseau');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                throw new Error(data.error);
                            }
                            
                            // Mettre à jour la référence
                            orderReference.textContent = data.reference;
                            
                            // Formater le HTML pour le contenu du modal
                            const orderItems = data.items.map(item => {
                                return `
                                    <div class="order-item">
                                        <img src="../${item.image || 'assets/img/products/placeholder.jpg'}" alt="${item.nom_produit}" class="item-image">
                                        <div class="item-details">
                                            <div class="item-name">${item.nom_produit}</div>
                                            <div class="item-reference">${item.reference_produit || 'N/A'}</div>
                                        </div>
                                        <div class="item-price">
                                            <div class="item-quantity">${item.quantite} × ${parseFloat(item.prix_unitaire).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} €</div>
                                            <div class="item-total">${parseFloat(item.prix_total).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} €</div>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                            
                            // Déterminer la classe CSS du statut
                            let statusClass = '';
                            let statusLabel = '';
                            
                            switch (data.statut) {
                                case 'en_attente':
                                    statusClass = 'status-pending';
                                    statusLabel = 'En attente';
                                    break;
                                case 'payee':
                                    statusClass = 'status-paid';
                                    statusLabel = 'Payée';
                                    break;
                                case 'en_preparation':
                                    statusClass = 'status-preparing';
                                    statusLabel = 'En préparation';
                                    break;
                                case 'expediee':
                                    statusClass = 'status-shipped';
                                    statusLabel = 'Expédiée';
                                    break;
                                case 'livree':
                                    statusClass = 'status-delivered';
                                    statusLabel = 'Livrée';
                                    break;
                                case 'annulee':
                                    statusClass = 'status-cancelled';
                                    statusLabel = 'Annulée';
                                    break;
                                case 'remboursee':
                                    statusClass = 'status-refunded';
                                    statusLabel = 'Remboursée';
                                    break;
                            }
                            
                            // Générer le HTML pour les options du select de statut
                            const statusOptions = [
                                {value: 'en_attente', label: 'En attente'},
                                {value: 'payee', label: 'Payée'},
                                {value: 'en_preparation', label: 'En préparation'},
                                {value: 'expediee', label: 'Expédiée'},
                                {value: 'livree', label: 'Livrée'},
                                {value: 'annulee', label: 'Annulée'},
                                {value: 'remboursee', label: 'Remboursée'}
                            ].map(status => {
                                return `<option value="${status.value}" ${data.statut === status.value ? 'selected' : ''}>${status.label}</option>`;
                            }).join('');
                            
                            // Construire le HTML complet
                            orderDetailContent.innerHTML = `
                                <div class="order-detail-header">
                                    <div class="order-info">
                                        <h4 class="info-heading">Informations commande</h4>
                                        <ul class="info-list">
                                            <li>
                                                <span class="label">Référence:</span>
                                                <span class="value">${data.reference}</span>
                                            </li>
                                            <li>
                                                <span class="label">Date:</span>
                                                <span class="value">${new Date(data.date_commande).toLocaleString('fr-FR')}</span>
                                            </li>
                                            <li>
                                                <span class="label">Statut:</span>
                                                <span class="value">
                                                    <span class="status-badge ${statusClass}">${statusLabel}</span>
                                                </span>
                                            </li>
                                            <li>
                                                <span class="label">Mode paiement:</span>
                                                <span class="value">${data.mode_paiement || 'Non spécifié'}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="order-customer">
                                        <h4 class="info-heading">Informations client</h4>
                                        <ul class="info-list">
                                            <li>
                                                <span class="label">Client:</span>
                                                <span class="value">${data.client_nom}</span>
                                            </li>
                                            <li>
                                                <span class="label">Email:</span>
                                                <span class="value">${data.client_email}</span>
                                            </li>
                                            <li>
                                                <span class="label">Téléphone:</span>
                                                <span class="value">${data.client_telephone || 'Non spécifié'}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="order-status-update">
                                    <form action="orders.php" method="POST" class="order-status-form">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="${data.id}">
                                        <input type="hidden" name="old_status" value="${data.statut}">
                                        <select name="status" class="form-control">
                                            ${statusOptions}
                                        </select>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Mettre à jour le statut
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="customer-section">
                                    <div class="address-card">
                                        <h4><i class="fas fa-shipping-fast"></i> Adresse de livraison</h4>
                                        <p>${data.adresse_livraison.replace(/\n/g, '<br>')}</p>
                                    </div>
                                    <div class="address-card">
                                        <h4><i class="fas fa-file-invoice"></i> Adresse de facturation</h4>
                                        <p>${data.adresse_facturation.replace(/\n/g, '<br>')}</p>
                                    </div>
                                </div>
                                
                                <div class="order-items">
                                    <h4 class="info-heading">Articles commandés</h4>
                                    ${orderItems}
                                </div>
                                
                                <div class="order-summary">
                                    <div class="summary-row">
                                        <span>Total produits:</span>
                                        <span>${parseFloat(data.total_produits).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} €</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>TVA:</span>
                                        <span>${parseFloat(data.total_taxe).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} €</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Frais de livraison:</span>
                                        <span>${parseFloat(data.frais_livraison).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} €</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Total:</span>
                                        <span>${parseFloat(data.total).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} €</span>
                                    </div>
                                </div>
                                
                                <div class="notes-section">
                                    <h4 class="info-heading">Notes</h4>
                                    <p>${data.notes || 'Aucune note pour cette commande.'}</p>
                                    <form action="orders.php" method="POST" class="note-form">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="add_note">
                                        <input type="hidden" name="order_id" value="${data.id}">
                                        <textarea name="note" placeholder="Ajouter une note..." class="form-control"></textarea>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Ajouter
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <a href="invoice.php?id=${data.id}" class="btn btn-outline-primary" target="_blank">
                                        <i class="fas fa-file-invoice"></i> Voir la facture
                                    </a>
                                </div>
                            `;
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            orderDetailContent.innerHTML = `
                                <div class="text-center p-5">
                                    <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                                    <p class="mt-3">Erreur lors du chargement des détails de la commande.</p>
                                    <p class="text-muted">${error.message}</p>
                                </div>
                            `;
                        });
                });
            });
            
            // Gestionnaires d'événements pour fermer le modal
            if (modalClose) {
                modalClose.addEventListener('click', closeModal);
            }
            
            window.addEventListener('click', function(e) {
                if (e.target === orderModal) {
                    closeModal();
                }
            });
            
            // Fermer le modal avec la touche Echap
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && orderModal.style.display === 'flex') {
                    closeModal();
                }
            });
        });
    </script>
    <script src="js/orders.js"></script>
</body>
</html>