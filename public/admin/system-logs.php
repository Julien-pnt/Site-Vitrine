<?php
require_once '../../php/config/database.php';
require_once '../../php/utils/auth.php';

// Créer le répertoire pour les avatars par défaut s'il n'existe pas
$avatarsDir = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/assets/img/avatars/';
if (!is_dir($avatarsDir)) {
    mkdir($avatarsDir, 0755, true);
}

// Copier l'image existante comme image par défaut si elle n'existe pas déjà
$defaultAdminImage = $avatarsDir . 'admin-default.jpg';
$defaultUserImage = $avatarsDir . 'user-default.jpg';

// Image source (l'image que vous utilisez actuellement)
$sourceImage = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/assets/img/layout/jb3.jpg';

// Copier l'image si elle existe et n'est pas déjà copiée
if (file_exists($sourceImage)) {
    if (!file_exists($defaultAdminImage)) {
        copy($sourceImage, $defaultAdminImage);
    }
    if (!file_exists($defaultUserImage)) {
        copy($sourceImage, $defaultUserImage);
    }
}

// Vérification de l'authentification
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /Site-Vitrine/public/pages/auth/login.php?redirect=admin');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Construire la requête avec les filtres
$filters = [];
$params = [];

// Filtres de recherche
if (isset($_GET['level']) && !empty($_GET['level'])) {
    $filters[] = "level = ?";
    $params[] = $_GET['level'];
}

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $filters[] = "category = ?";
    $params[] = $_GET['category'];
}

if (isset($_GET['action']) && !empty($_GET['action'])) {
    $filters[] = "action = ?";
    $params[] = $_GET['action'];
}

if (isset($_GET['user_type']) && !empty($_GET['user_type'])) {
    $filters[] = "user_type = ?";
    $params[] = $_GET['user_type'];
}

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $filters[] = "user_id = ?";
    $params[] = $_GET['user_id'];
}

if (isset($_GET['entity_type']) && !empty($_GET['entity_type'])) {
    $filters[] = "entity_type = ?";
    $params[] = $_GET['entity_type'];
}

if (isset($_GET['entity_id']) && !empty($_GET['entity_id'])) {
    $filters[] = "entity_id = ?";
    $params[] = $_GET['entity_id'];
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters[] = "(details LIKE ? OR context LIKE ?)";
    $searchTerm = "%" . $_GET['search'] . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (isset($_GET['date_start']) && !empty($_GET['date_start'])) {
    $filters[] = "created_at >= ?";
    $params[] = $_GET['date_start'] . ' 00:00:00';
}

if (isset($_GET['date_end']) && !empty($_GET['date_end'])) {
    $filters[] = "created_at <= ?";
    $params[] = $_GET['date_end'] . ' 23:59:59';
}

if (isset($_GET['ip_address']) && !empty($_GET['ip_address'])) {
    $filters[] = "ip_address = ?";
    $params[] = $_GET['ip_address'];
}

$whereClause = "";
if (!empty($filters)) {
    $whereClause = " WHERE " . implode(" AND ", $filters);
}

// Récupérer les données
try {
    // Compter le nombre total de logs
    $countSql = "SELECT COUNT(*) FROM system_logs" . $whereClause;
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalLogs = $countStmt->fetchColumn();
    $totalPages = ceil($totalLogs / $limit);
    
    // Récupérer les logs avec la pagination
    $sql = "SELECT l.*, 
                  COALESCE(a.prenom, u.prenom, 'Système') as user_firstname,
                  COALESCE(a.nom, u.nom, '') as user_lastname,
                  COALESCE(a.email, u.email, '') as user_email
           FROM system_logs l
           LEFT JOIN utilisateurs a ON l.user_id = a.id AND l.user_type = 'admin'
           LEFT JOIN utilisateurs u ON l.user_id = u.id AND l.user_type = 'customer'
           $whereClause
           ORDER BY l.created_at DESC
           LIMIT $offset, $limit";
           
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les valeurs distinctes pour les filtres
    $levels = $pdo->query("SELECT DISTINCT level FROM system_logs ORDER BY level")->fetchAll(PDO::FETCH_COLUMN);
    $categories = $pdo->query("SELECT DISTINCT category FROM system_logs ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
    $actions = $pdo->query("SELECT DISTINCT action FROM system_logs ORDER BY action")->fetchAll(PDO::FETCH_COLUMN);
    $entityTypes = $pdo->query("SELECT DISTINCT entity_type FROM system_logs WHERE entity_type IS NOT NULL ORDER BY entity_type")->fetchAll(PDO::FETCH_COLUMN);
    $userTypes = $pdo->query("SELECT DISTINCT user_type FROM system_logs ORDER BY user_type")->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des logs: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journaux système - Administration</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #d4af37;
            --primary-light: rgba(212, 175, 55, 0.1);
            --primary-dark: #b39130;
            --text-color: #333;
            --text-light: #6c757d;
            --bg-light: #f8f9fa;
            --bg-dark: #212529;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --radius: 8px;
            --transition: all 0.25s ease;
        }

        /* Base styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            color: var(--text-color);
            background-color: #f5f5f7;
            line-height: 1.5;
        }

        .admin-container {
            display: flex; /* Changer grid pour flex */
            min-height: 100vh;
            width: 100%;
        }

        .main-content {
            margin-left: 250px; /* Même valeur que la largeur du sidebar */
            flex: 1;
            padding: 0; /* Enlever le padding général */
            background-color: #f5f5f7;
            min-height: 100vh;
        }

        .dashboard {
            padding: 1.5rem; /* Ajouter du padding ici à la place */
        }

        .dashboard h1 {
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--text-color);
            font-size: 1.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .dashboard h1 i {
            color: var(--primary-color);
            background: var(--primary-light);
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        /* Sidebar styles */
        .sidebar {
            background-color: var(--bg-dark);
            color: white;
            padding: 1.5rem 0; /* Réduire légèrement le padding */
            position: fixed; /* Fixed au lieu de sticky */
            top: 0;
            left: 0;
            width: 250px; /* Largeur fixe */
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .sidebar-brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .sidebar-brand h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
        }

        .sidebar-nav {
            padding: 0 1rem;
        }

        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 0.75rem 0.5rem;
            margin-top: 1rem;
        }

        .sidebar-nav a {
            color: #f8f9fa;
            position: relative;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .sidebar-nav a:hover {
            background-color: rgba(212, 175, 55, 0.1);
            color: var(--primary-color);
        }

        .sidebar-nav a.active {
            background-color: rgba(212, 175, 55, 0.15);
            color: var(--primary-color);
            font-weight: 500;
        }

        .sidebar-nav a i {
            margin-right: 0.75rem;
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        /* Filters section */
        .filter-form {
            background-color: white;
            border-radius: var (--radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-weight: 500;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .filter-group input,
        .filter-group select {
            border: 1px solid #ddd;
            border-radius: var(--radius);
            padding: 0.6rem 0.75rem;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
            outline: none;
        }

        .filter-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn {
            font-weight: 500;
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            font-size: 0.9rem;
        }

        .btn i {
            font-size: 0.875rem;
        }

        .btn.secondary {
            background-color: #f0f0f0;
            color: var(--text-color);
        }

        .btn.secondary:hover {
            background-color: #e0e0e0;
        }

        .reset-filters {
            background-color: transparent;
            border: 1px solid #ddd;
            color: var (--text-light);
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .reset-filters:hover {
            background-color: #f8f9fa;
        }

        .apply-filters {
            background-color: var(--primary-color);
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var (--transition);
            border: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .apply-filters:hover {
            background-color: var(--primary-dark);
        }

        .export-csv {
            background-color: var(--bg-light);
            color: var(--text-color);
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .export-csv:hover {
            background-color: #e9ecef;
        }

        /* Table styles */
        .table-container {
            background-color: white;
            border-radius: var (--radius);
            box-shadow: var(--shadow);
            overflow: auto;
            margin-bottom: 2rem;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .data-table thead th {
            background-color: #f8f9fa;
            color: var(--text-color);
            font-weight: 600;
            text-align: left;
            padding: 1rem;
            font-size: 0.9rem;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid #eee;
        }

        .data-table tbody tr {
            transition: var(--transition);
        }

        .data-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.015);
        }

        .data-table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .empty-table {
            text-align: center;
            padding: 3rem 1rem !important;
            font-style: italic;
            color: var(--text-light);
        }

        /* Level badges */
        .level-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.25rem 0.75rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .level-debug { background: #e9ecef; color: #495057; }
        .level-info { background: #e6f4ff; color: #0d6efd; }
        .level-notice { background: #e8f8f0; color: #20c997; }
        .level-warning { background: #fff8e6; color: #fd7e14; }
        .level-error { background: #feebee; color: #dc3545; }
        .level-critical { background: #fdd9d7; color: #dc3545; }
        .level-alert { background: #dc3545; color: white; }
        .level-emergency { background: #901C1C; color: white; }

        /* Entity pills */
        .entity-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f0f0f0;
            color: var(--text-color);
            padding: 0.2rem 0.6rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .user-type-admin { background: #e6f4ff; color: #0d6efd; }
        .user-type-customer { background: #e8f8f0; color: #20c997; }
        .user-type-system { background: #e9ecef; color: #495057; }
        .user-type-guest { background: #feebee; color: #dc3545; }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 0.25rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination a {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            height: 2.25rem;
            padding: 0 0.75rem;
            border-radius: var(--radius);
            background-color: white;
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .pagination a:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .pagination a.active {
            background-color: var(--primary-color);
            color: white;
        }

        /* Modal styles */
        .log-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .log-modal.show {
            opacity: 1;
        }

        .log-modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 12px;
            width: 85%;
            max-width: 1100px;
            max-height: 85vh;
            overflow-y: auto;
            transform: translateY(-20px);
            transition: transform 0.3s ease-out;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .log-modal.show .log-modal-content {
            transform: translateY(0);
        }

        .log-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .log-modal-header h2 {
            margin: 0;
            color: var(--text-color);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .log-modal-close {
            color: var(--text-light);
            font-size: 1.75rem;
            font-weight: 300;
            cursor: pointer;
            transition: var(--transition);
        }

        .log-modal-close:hover {
            color: var(--danger);
        }

        .log-modal-tabs {
            display: flex;
            border-bottom: 1px solid #eee;
            margin-bottom: 1.5rem;
            gap: 1.5rem;
        }

        .log-modal-tab {
            padding: 0.75rem 0.5rem;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-light);
            position: relative;
            transition: var(--transition);
        }

        .log-modal-tab:hover {
            color: var(--text-color);
        }

        .log-modal-tab.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .log-modal-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
        }

        .log-modal-section {
            display: none;
        }

        .log-modal-section.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-details-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modal-details-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            width: 160px;
            vertical-align: top;
            color: var(--text-light);
            font-weight: 500;
            border-bottom: 1px solid #f0f0f0;
        }

        .modal-details-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            word-break: break-word;
        }

        .modal-details-table tr:last-child th,
        .modal-details-table tr:last-child td {
            border-bottom: none;
        }

        .json-viewer {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 8px;
            font-family: 'JetBrains Mono', 'Fira Code', 'Roboto Mono', monospace;
            font-size: 0.9rem;
            white-space: pre-wrap;
            overflow-x: auto;
            color: #24292e;
            line-height: 1.6;
        }

        .diff-viewer {
            display: flex;
            gap: 1.5rem;
        }

        @media (max-width: 992px) {
            .diff-viewer {
                flex-direction: column;
            }
        }

        .diff-column {
            flex: 1;
        }

        .diff-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        .changes {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 8px;
            margin-top: 1.5rem;
        }

        .change-item {
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }

        .change-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .change-field {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .change-old {
            color: var(--danger);
            text-decoration: line-through;
            margin-bottom: 0.25rem;
            padding-left: 1rem;
            position: relative;
        }

        .change-old::before {
            content: '-';
            position: absolute;
            left: 0;
        }

        .change-new {
            color: var(--success);
            padding-left: 1rem;
            position: relative;
        }

        .change-new::before {
            content: '+';
            position: absolute;
            left: 0;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background-color: #f0f0f0;
            color: var(--text-color);
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-icon:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        /* Alert styles */
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--radius);
            font-size: 0.95rem;
        }

        .alert-error {
            background-color: var(--danger);
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .admin-container {
                grid-template-columns: 220px 1fr;
            }
        }

        @media (max-width: 992px) {
            .admin-container {
                grid-template-columns: auto 1fr;
            }
            
            .sidebar {
                width: 70px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .sidebar-logo h2,
            .sidebar-heading,
            .sidebar-nav a span {
                display: none;
            }
            
            .sidebar-nav a {
                justify-content: center;
                padding: 0.75rem;
            }
            
            .sidebar-nav a i {
                margin-right: 0;
            }
            
            .sidebar-logo {
                justify-content: center;
                padding: 0;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .filter-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            
            .log-modal-content {
                width: 95%;
                padding: 1.5rem;
                margin: 2% auto;
            }
            
            .log-modal-tabs {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .log-modal-tab {
                padding: 0.5rem;
            }
            
            .modal-details-table th {
                width: 120px;
            }
        }

        /* Header styles */
        .main-header {
            background-color: white;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-search {
            position: relative;
            max-width: 400px;
            width: 100%;
        }

        .header-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .header-search input {
            width: 100%;
            padding: 0.6rem 0.75rem 0.6rem 2.5rem;
            border: 1px solid #eee;
            border-radius: 30px;
            font-size: 0.9rem;
        }

        .header-search input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.1);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header-icon {
            position: relative;
            color: var(--text-color);
            font-size: 1.25rem;
        }

        .header-icon .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--primary-color);
            color: white;
            font-size: 10px;
            font-weight: 600;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            position: relative;
            padding: 0.5rem;
            border-radius: var(--radius);
        }

        .user-dropdown:hover {
            background-color: #f8f9fa;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .username {
            font-weight: 500;
        }

        .user-dropdown i {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 200px;
            background-color: white;
            border-radius: var(--radius);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 0.5rem;
            margin-top: 0.5rem;
            display: none;
            z-index: 100;
        }

        .user-dropdown:hover .dropdown-menu {
            display: block;
            animation: fadeIn 0.2s ease-out;
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            color: var(--text-color);
            text-decoration: none;
            border-radius: 4px;
            gap: 0.75rem;
        }

        .dropdown-menu a:hover {
            background-color: #f8f9fa;
        }

        .dropdown-menu a.logout {
            color: var(--danger);
        }

        .dropdown-menu a.logout:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }

        @media (max-width: 768px) {
            .header-search {
                display: none;
            }
            
            .main-header {
                padding: 0.75rem 1rem;
            }
            
            .username {
                display: none;
            }
            
            .user-dropdown i {
                display: none;
            }
        }

        /* Ajouter ou modifier ces styles dans la section CSS */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .logo-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            margin-bottom: 5%;
        }

        /* Ajoutez ces styles au bas de votre section CSS */
        .dropdown-menu {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            top: calc(100% + 5px);
            right: 0;
            width: 200px;
            background-color: white;
            border-radius: var(--radius);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 0.5rem;
            z-index: 1000;
            transition: all 0.2s ease-in-out;
            transform: translateY(-10px);
        }

        .user-dropdown:hover .dropdown-menu {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        /* Ajoutez cette classe pour créer une zone de survol étendue qui empêche le menu de disparaître */
        .user-dropdown {
            position: relative;
        }

        .user-dropdown::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            height: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar avec navigation moderne -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="../assets/img/layout/Aubry-ladmin.png" alt="Elixir du Temps" class="logo-img">
                <h2>Administration</h2>
            </div>
            
            <nav class="sidebar-nav">
                <div class="sidebar-heading">Tableau de bord</div>
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Accueil</span>
                </a>
                <a href="analytics.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Analytiques</span>
                </a>
                
                <div class="sidebar-heading">Catalogue</div>
                <a href="products.php">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
                <a href="categories.php">
                    <i class="fas fa-tags"></i>
                    <span>Catégories</span>
                </a>
                <a href="collections.php">
                    <i class="fas fa-layer-group"></i>
                    <span>Collections</span>
                </a>
                
                <div class="sidebar-heading">Ventes</div>
                <a href="orders.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Commandes</span>
                </a>
                <a href="customers.php">
                    <i class="fas fa-users"></i>
                    <span>Clients</span>
                </a>
                
                <div class="sidebar-heading">Système</div>
                <a href="users.php">
                    <i class="fas fa-user-shield"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
                <a href="system-logs.php" class="active">
                    <i class="fas fa-shield-alt"></i>
                    <span>Journaux</span>
                </a>
            </nav>
        </aside>
        
        <main class="main-content">
            <header class="main-header">
                <div class="header-content">
                    <div class="header-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Recherche rapide..." id="quick-search">
                    </div>
                    <div class="header-actions">
                        <a href="#" class="header-icon" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="badge">3</span>
                        </a>
                        <a href="#" class="header-icon" title="Messages">
                            <i class="fas fa-envelope"></i>
                            <span class="badge">5</span>
                        </a>
                        <?php
                        // Récupérer les informations de l'utilisateur connecté
                        $userId = $_SESSION['user_id'] ?? 0;
                        $userInfo = null;

                        if ($userId > 0) {
                            try {
                                $userStmt = $pdo->prepare("SELECT nom, prenom, email, photo, role FROM utilisateurs WHERE id = ?");
                                $userStmt->execute([$userId]);
                                $userInfo = $userStmt->fetch();
                            } catch (PDOException $e) {
                                // Gérer silencieusement l'erreur
                            }
                        }
                        ?>
                        <div class="user-dropdown">
                            <?php if ($userInfo && !empty($userInfo['photo'])): ?>
                                <!-- Utiliser la photo de la base de données si elle existe -->
                                <img src="../uploads/users/<?= htmlspecialchars($userInfo['photo']) ?>" alt="<?= htmlspecialchars($userInfo['prenom']) ?>" class="avatar">
                            <?php else: ?>
                                <!-- Image par défaut basée sur le rôle -->
                                <?php $defaultImage = ($userInfo && $userInfo['role'] == 'admin') ? 'user-default.png' : 'user-default.png'; ?>
                                <img src="../assets/img/avatars/<?= $defaultImage ?>" alt="Avatar" class="avatar">
                            <?php endif; ?>
                            <span class="username"><?= $userInfo ? htmlspecialchars($userInfo['prenom']) : 'Utilisateur' ?></span>
                            <i class="fas fa-chevron-down"></i>
                            <div class="dropdown-menu">
                                <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
                                <a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a>
                                <a href="../pages/auth/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <div class="dashboard">
                <h1><i class="fas fa-shield-alt"></i> Journaux système</h1>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <!-- Filtres avancés -->
                <div class="filter-form">
                    <form method="GET" action="system-logs.php">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="search">Recherche</label>
                                <input type="text" id="search" name="search" placeholder="Rechercher..." value="<?= $_GET['search'] ?? '' ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="level">Niveau</label>
                                <select id="level" name="level">
                                    <option value="">Tous les niveaux</option>
                                    <?php foreach ($levels as $level): ?>
                                        <option value="<?= $level ?>" <?= (isset($_GET['level']) && $_GET['level'] == $level) ? 'selected' : '' ?>>
                                            <?= ucfirst($level) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="category">Catégorie</label>
                                <select id="category" name="category">
                                    <option value="">Toutes les catégories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category ?>" <?= (isset($_GET['category']) && $_GET['category'] == $category) ? 'selected' : '' ?>>
                                            <?= ucfirst($category) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="action">Action</label>
                                <select id="action" name="action">
                                    <option value="">Toutes les actions</option>
                                    <?php foreach ($actions as $action): ?>
                                        <option value="<?= $action ?>" <?= (isset($_GET['action']) && $_GET['action'] == $action) ? 'selected' : '' ?>>
                                            <?= ucfirst(str_replace('_', ' ', $action)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="user_type">Type d'utilisateur</label>
                                <select id="user_type" name="user_type">
                                    <option value="">Tous les types</option>
                                    <?php foreach ($userTypes as $type): ?>
                                        <option value="<?= $type ?>" <?= (isset($_GET['user_type']) && $_GET['user_type'] == $type) ? 'selected' : '' ?>>
                                            <?= ucfirst($type) ?>
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
                        </div>
                        
                        <div class="filter-advanced" id="advanced-filters" style="display: none;">
                            <div class="filter-group">
                                <label for="entity_type">Type d'entité</label>
                                <select id="entity_type" name="entity_type">
                                    <option value="">Toutes les entités</option>
                                    <?php foreach ($entityTypes as $type): ?>
                                        <option value="<?= $type ?>" <?= (isset($_GET['entity_type']) && $_GET['entity_type'] == $type) ? 'selected' : '' ?>>
                                            <?= ucfirst($type) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="entity_id">ID de l'entité</label>
                                <input type="text" id="entity_id" name="entity_id" value="<?= $_GET['entity_id'] ?? '' ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="ip_address">Adresse IP</label>
                                <input type="text" id="ip_address" name="ip_address" value="<?= $_GET['ip_address'] ?? '' ?>">
                            </div>
                        </div>
                        
                        <div class="filter-buttons">
                            <button type="button" id="toggle-advanced" class="btn secondary">
                                <span id="advanced-text">Afficher les filtres avancés</span>
                                <i class="fas fa-chevron-down" id="advanced-icon"></i>
                            </button>
                            <button type="reset" class="reset-filters" onclick="window.location='system-logs.php'">Réinitialiser</button>
                            <button type="submit" class="apply-filters">Appliquer les filtres</button>
                            <a href="export.php?type=system_logs<?= !empty($_GET) ? '&' . http_build_query($_GET) : '' ?>" class="export-csv">
                                <i class="fas fa-file-csv"></i> Exporter CSV
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Tableau des logs -->
                <div class="table-container">
                    <table class="data-table log-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Niveau</th>
                                <th>Utilisateur</th>
                                <th>Catégorie/Action</th>
                                <th>Entité</th>
                                <th>Détails</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                    <td>
                                        <span class="level-badge level-<?= $log['level'] ?>">
                                            <?= $log['level'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($log['user_id']): ?>
                                            <span class="entity-pill user-type-<?= $log['user_type'] ?>">
                                                <?= $log['user_type'] ?>
                                            </span>
                                            <?= htmlspecialchars($log['user_firstname'] . ' ' . $log['user_lastname']) ?>
                                        <?php else: ?>
                                            <span class="entity-pill user-type-<?= $log['user_type'] ?>">
                                                <?= ucfirst($log['user_type']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars(ucfirst($log['category'])) ?></strong> / 
                                        <?= htmlspecialchars(str_replace('_', ' ', $log['action'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($log['entity_type']): ?>
                                            <span class="entity-pill">
                                                <?= htmlspecialchars(ucfirst($log['entity_type'])) ?> #<?= htmlspecialchars($log['entity_id']) ?>
                                            </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="log-details">
                                        <?= htmlspecialchars($log['details'] ?? '-') ?>
                                    </td>
                                    <td>
                                        <button class="btn-icon view-log" data-id="<?= $log['id'] ?>" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="7" class="empty-table">Aucun log trouvé</td>
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
                
                <!-- Modal pour afficher les détails d'un log -->
                <div id="logModal" class="log-modal">
                    <div class="log-modal-content">
                        <div class="log-modal-header">
                            <h2>Détails du log</h2>
                            <span class="log-modal-close">&times;</span>
                        </div>
                        
                        <div class="log-modal-tabs">
                            <div class="log-modal-tab active" data-tab="overview">Vue d'ensemble</div>
                            <div class="log-modal-tab" data-tab="before-after">Avant/Après</div>
                            <div class="log-modal-tab" data-tab="context">Contexte</div>
                            <div class="log-modal-tab" data-tab="request">Requête</div>
                        </div>
                        
                        <div class="log-modal-section active" data-section="overview">
                            <table class="modal-details-table">
                                <tr>
                                    <th>Date:</th>
                                    <td id="modal-date"></td>
                                </tr>
                                <tr>
                                    <th>Niveau:</th>
                                    <td id="modal-level"></td>
                                </tr>
                                <tr>
                                    <th>Utilisateur:</th>
                                    <td id="modal-user"></td>
                                </tr>
                                <tr>
                                    <th>Catégorie:</th>
                                    <td id="modal-category"></td>
                                </tr>
                                <tr>
                                    <th>Action:</th>
                                    <td id="modal-action"></td>
                                </tr>
                                <tr>
                                    <th>Entité:</th>
                                    <td id="modal-entity"></td>
                                </tr>
                                <tr>
                                    <th>Détails:</th>
                                    <td id="modal-details"></td>
                                </tr>
                                <tr>
                                    <th>IP:</th>
                                    <td id="modal-ip"></td>
                                </tr>
                                <tr>
                                    <th>Session ID:</th>
                                    <td id="modal-session"></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="log-modal-section" data-section="before-after">
                            <div class="diff-viewer">
                                <div class="diff-column">
                                    <div class="diff-title">État avant</div>
                                    <div id="modal-before-state" class="json-viewer"></div>
                                </div>
                                <div class="diff-column">
                                    <div class="diff-title">État après</div>
                                    <div id="modal-after-state" class="json-viewer"></div>
                                </div>
                            </div>
                            <h3>Changements détectés</h3>
                            <div id="modal-changes" class="changes"></div>
                        </div>
                        
                        <div class="log-modal-section" data-section="context">
                            <div id="modal-context" class="json-viewer"></div>
                        </div>
                        
                        <div class="log-modal-section" data-section="request">
                            <table class="modal-details-table">
                                <tr>
                                    <th>Méthode HTTP:</th>
                                    <td id="modal-http-method"></td>
                                </tr>
                                <tr>
                                    <th>URL:</th>
                                    <td id="modal-url"></td>
                                </tr>
                                <tr>
                                    <th>User Agent:</th>
                                    <td id="modal-user-agent"></td>
                                </tr>
                                <tr>
                                    <th>Temps d'exécution:</th>
                                    <td id="modal-execution-time"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle filtres avancés
            const toggleBtn = document.getElementById('toggle-advanced');
            const advancedFilters = document.getElementById('advanced-filters');
            const advancedText = document.getElementById('advanced-text');
            const advancedIcon = document.getElementById('advanced-icon');
            
            if (toggleBtn && advancedFilters) {
                toggleBtn.addEventListener('click', function() {
                    const isVisible = advancedFilters.style.display !== 'none';
                    advancedFilters.style.display = isVisible ? 'none' : 'flex';
                    advancedText.textContent = isVisible ? 
                        'Afficher les filtres avancés' : 'Masquer les filtres avancés';
                    advancedIcon.className = isVisible ? 
                        'fas fa-chevron-down' : 'fas fa-chevron-up';
                });
            }
            
            // Gestion de la modal
            const modal = document.getElementById('logModal');
            const closeBtn = document.querySelector('.log-modal-close');
            const modalTabs = document.querySelectorAll('.log-modal-tab');
            
            // Fermer la modal quand on clique sur le X
            if (closeBtn && modal) {
                closeBtn.addEventListener('click', function() {
                    modal.classList.remove('show');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                });
            }
            
            // Fermer la modal quand on clique en dehors
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                }
            });
            
            // Gestion des onglets
            modalTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Masquer tous les onglets et sections
                    modalTabs.forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.log-modal-section').forEach(s => s.classList.remove('active'));
                    
                    // Activer l'onglet cliqué
                    tab.classList.add('active');
                    const tabName = tab.getAttribute('data-tab');
                    document.querySelector(`.log-modal-section[data-section="${tabName}"]`).classList.add('active');
                });
            });
            
            // Charger les données du log dans la modal
            document.querySelectorAll('.view-log').forEach(btn => {
                btn.addEventListener('click', function() {
                    const logId = this.getAttribute('data-id');
                    
                    // Afficher la modal avant de charger les données
                    modal.style.display = 'block';
                    // Force reflow pour déclencher l'animation
                    void modal.offsetWidth;
                    modal.classList.add('show');
                    
                    // Charger les données via AJAX
                    fetch(`ajax/get-log.php?id=${logId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                                return;
                            }
                            
                            // Remplir la modal avec les données
                            document.getElementById('modal-date').textContent = formatDate(data.created_at);
                            
                            const levelBadge = document.createElement('span');
                            levelBadge.className = `level-badge level-${data.level}`;
                            levelBadge.textContent = data.level;
                            document.getElementById('modal-level').innerHTML = '';
                            document.getElementById('modal-level').appendChild(levelBadge);
                            
                            // Utilisateur
                            let userHtml = '';
                            if (data.user_id) {
                                const userTypePill = document.createElement('span');
                                userTypePill.className = `entity-pill user-type-${data.user_type}`;
                                userTypePill.textContent = data.user_type;
                                userHtml = `${userTypePill.outerHTML} ${data.user_firstname} ${data.user_lastname}`;
                                if (data.user_email) {
                                    userHtml += ` <small>(${data.user_email})</small>`;
                                }
                            } else {
                                const systemPill = document.createElement('span');
                                systemPill.className = `entity-pill user-type-${data.user_type}`;
                                systemPill.textContent = data.user_type.charAt(0).toUpperCase() + data.user_type.slice(1);
                                userHtml = systemPill.outerHTML;
                            }
                            document.getElementById('modal-user').innerHTML = userHtml;
                            
                            document.getElementById('modal-category').textContent = data.category.charAt(0).toUpperCase() + data.category.slice(1);
                            document.getElementById('modal-action').textContent = data.action.replace(/_/g, ' ');
                            
                            // Entité
                            if (data.entity_type) {
                                document.getElementById('modal-entity').textContent = `${data.entity_type.charAt(0).toUpperCase() + data.entity_type.slice(1)} #${data.entity_id}`;
                            } else {
                                document.getElementById('modal-entity').textContent = '-';
                            }
                            
                            document.getElementById('modal-details').textContent = data.details || '-';
                            document.getElementById('modal-ip').textContent = data.ip_address || '-';
                            document.getElementById('modal-session').textContent = data.session_id || '-';
                            
                            // État avant/après
                            const beforeState = data.before_state ? JSON.parse(data.before_state) : null;
                            const afterState = data.after_state ? JSON.parse(data.after_state) : null;
                            
                            document.getElementById('modal-before-state').textContent = beforeState ? 
                                JSON.stringify(beforeState, null, 2) : 'Aucune donnée';
                            document.getElementById('modal-after-state').textContent = afterState ? 
                                JSON.stringify(afterState, null, 2) : 'Aucune donnée';
                            
                            // Changements
                            const changesContainer = document.getElementById('modal-changes');
                            changesContainer.innerHTML = '';
                            
                            if (data.context) {
                                const contextObj = JSON.parse(data.context);
                                if (contextObj.changes) {
                                    Object.entries(contextObj.changes).forEach(([field, change]) => {
                                        const changeItem = document.createElement('div');
                                        changeItem.className = 'change-item';
                                        
                                        changeItem.innerHTML = `
                                            <div class="change-field">${field}</div>
                                            <div class="change-old">Avant: ${formatValue(change.old)}</div>
                                            <div class="change-new">Après: ${formatValue(change.new)}</div>
                                        `;
                                        
                                        changesContainer.appendChild(changeItem);
                                    });
                                } else {
                                    changesContainer.textContent = 'Aucun changement détecté';
                                }
                                
                                // Contexte JSON
                                document.getElementById('modal-context').textContent = 
                                    JSON.stringify(contextObj, null, 2);
                            } else {
                                document.getElementById('modal-context').textContent = 'Aucun contexte disponible';
                                changesContainer.textContent = 'Aucun changement détecté';
                            }
                            
                            // Infos requête
                            document.getElementById('modal-http-method').textContent = data.http_method || '-';
                            document.getElementById('modal-url').textContent = data.request_url || '-';
                            document.getElementById('modal-user-agent').textContent = data.user_agent || '-';
                            document.getElementById('modal-execution-time').textContent = 
                                data.execution_time ? `${(data.execution_time * 1000).toFixed(2)} ms` : '-';
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Erreur lors du chargement des détails du log');
                        });
                });
            });
            
            // Fonctions utilitaires
            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
            
            function formatValue(value) {
                if (value === null || value === undefined) {
                    return '<em>null</em>';
                }
                
                if (typeof value === 'object') {
                    return JSON.stringify(value);
                }
                
                return String(value);
            }
            
            // Syntaxe highlighting pour JSON
            document.querySelectorAll('.json-viewer').forEach(viewer => {
                const content = viewer.textContent;
                if (content && content !== 'Aucune donnée') {
                    try {
                        const json = JSON.parse(content);
                        viewer.innerHTML = syntaxHighlight(JSON.stringify(json, null, 2));
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                    }
                }
            });
            
            function syntaxHighlight(json) {
                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    let cls = 'number';
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'key';
                            match = match.replace(/"/, '<span style="color:#5B2C6F;">').replace(/":\s*$/, '</span>:');
                            return match;
                        } else {
                            cls = 'string';
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'boolean';
                    } else if (/null/.test(match)) {
                        cls = 'null';
                    }
                    
                    const colors = {
                        'string': '#2C7873',
                        'number': '#1E3F66',
                        'boolean': '#7D3C98',
                        'null': '#566573'
                    };
                    
                    return `<span style="color:${colors[cls]};">${match}</span>`;
                });
            }
        });

        // Ajouter ce code avant la fin du script existant
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du menu déroulant de profil utilisateur
            const userDropdown = document.querySelector('.user-dropdown');
            const dropdownMenu = userDropdown.querySelector('.dropdown-menu');
            
            if (userDropdown && dropdownMenu) {
                userDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
                    
                    // Animation
                    if (dropdownMenu.style.display === 'block') {
                        setTimeout(() => {
                            dropdownMenu.style.opacity = '1';
                            dropdownMenu.style.transform = 'translateY(0)';
                        }, 10);
                    } else {
                        dropdownMenu.style.opacity = '0';
                        dropdownMenu.style.transform = 'translateY(-10px)';
                    }
                });
                
                // Fermer le menu quand on clique ailleurs
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target)) {
                        dropdownMenu.style.display = 'none';
                        dropdownMenu.style.opacity = '0';
                        dropdownMenu.style.transform = 'translateY(-10px)';
                    }
                });
            }
        });
    </script>
</body>
</html>