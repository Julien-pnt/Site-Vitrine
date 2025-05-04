<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/auth/login.php');
    exit;
}

// Connexion à la base de données
require_once '../../php/config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Récupérer les informations de l'utilisateur
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Après avoir récupéré les informations de l'utilisateur, mettre à jour la dernière connexion
if ($user) {
    // Mettre à jour la date de dernière connexion
    $stmtUpdate = $conn->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
    $stmtUpdate->execute([$userId]);
    
    // Si l'utilisateur n'a jamais été connecté auparavant, définir la date actuelle
    if (empty($user['derniere_connexion'])) {
        $user['derniere_connexion'] = date('Y-m-d H:i:s');
    }
}

// Récupérer les commandes récentes
$stmtOrders = $conn->prepare("
    SELECT * FROM commandes 
    WHERE utilisateur_id = ? 
    ORDER BY date_commande DESC 
    LIMIT 5
");
$stmtOrders->execute([$userId]);
$recentOrders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// Pour le chemin relatif
$relativePath = "..";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord | Elixir du Temps</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/layout/icon2.png" type="image/x-icon">
    
    <!-- Styles du tableau de bord uniquement -->
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <style>
        :root {
            --primary-color: #d4af37;
            --primary-light: rgba(212, 175, 55, 0.1);
            --primary-dark: #b8973a;
            --secondary-color: #1a1a1a;
            --secondary-light: #2a2a2a;
            --light-color: #fff;
            --light-gray: #f9f9f9;
            --medium-gray: #888;
            --dark-gray: #333;
            --border-color: rgba(0, 0, 0, 0.08);
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --info-color: #2196F3;
            --shadow-xs: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-sm: 0 2px 6px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 28px rgba(0,0,0,0.12);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;
            --font-primary: 'Playfair Display', serif;
            --font-secondary: 'Raleway', sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-md: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.5rem;
            --font-size-2xl: 2rem;
            --spacing-xs: 4px;
            --spacing-sm: 8px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-secondary);
            background-color: var(--light-gray);
            color: var(--dark-gray);
            line-height: 1.6;
            font-weight: 400;
            letter-spacing: 0.01em;
        }
        
        /* Layout dashboard */
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }
        
        /* Content area */
        .dashboard-content {
            flex: 1;
            margin-left: 280px;
            padding: var(--spacing-xl);
            max-width: 100%;
        }
        
        .dashboard-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-xl);
            background-color: var(--light-color);
            padding: var(--spacing-lg) var(--spacing-xl);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }
        
        .welcome-message h1 {
            font-family: var(--font-primary);
            font-size: var(--font-size-xl);
            color: var(--secondary-color);
            margin-bottom: var(--spacing-xs);
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        
        .last-connection {
            font-size: var(--font-size-sm);
            color: var(--medium-gray);
            font-style: italic;
        }
        
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }
        
        .btn-return {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--secondary-color);
            font-size: var(--font-size-sm);
            transition: var(--transition);
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            background-color: var(--light-color);
            font-weight: 500;
        }
        
        .btn-return i {
            margin-right: var(--spacing-sm);
            font-size: var(--font-size-sm);
        }
        
        .btn-return:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
            border-color: var(--primary-light);
            box-shadow: var(--shadow-xs);
        }
        
        /* User dropdown */
        .user-dropdown {
            position: relative;
            z-index: 100;
        }
        
        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            cursor: pointer;
            background: none;
            border: 1px solid var(--border-color);
            padding: var(--spacing-xs) var(--spacing-md);
            border-radius: 30px;
            transition: var(--transition);
            background-color: var(--light-color);
        }
        
        .user-dropdown-toggle:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }
        
        .avatar-mini {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: var(--light-color);
            margin-right: var(--spacing-sm);
            font-weight: 600;
            font-size: var(--font-size-sm);
            overflow: hidden;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8), 0 0 0 4px rgba(212, 175, 55, 0.2);
        }
        
        .user-name {
            font-weight: 500;
            margin-right: var(--spacing-sm);
            color: var(--secondary-color);
        }
        
        .user-dropdown-toggle i {
            font-size: var(--font-size-xs);
            color: var(--medium-gray);
            transition: var(--transition);
        }
        
        .user-dropdown-toggle.active i {
            transform: rotate(180deg);
        }
        
        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 250px;
            background-color: var(--light-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .user-dropdown-toggle.active + .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .user-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -5px;
            right: 20px;
            width: 10px;
            height: 10px;
            background-color: var(--light-color);
            transform: rotate(45deg);
            border-top: 1px solid var(--border-color);
            border-left: 1px solid var(--border-color);
        }

        .user-info-header {
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
            background-color: var(--light-gray);
        }

        .user-fullname {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: var(--spacing-xs);
            font-size: var(--font-size-md);
        }

        .user-email {
            font-size: var(--font-size-sm);
            color: var(--medium-gray);
        }

        .dropdown-menu-item {
            display: flex;
            align-items: center;
            padding: var(--spacing-md) var(--spacing-lg);
            color: var(--dark-gray);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .dropdown-menu-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0;
            background-color: var(--primary-light);
            z-index: -1;
            transition: var(--transition);
        }

        .dropdown-menu-item:hover::after {
            width: 100%;
        }

        .dropdown-menu-item:active {
            background-color: var(--primary-light);
        }

        .dropdown-menu-item i {
            width: 20px;
            margin-right: var(--spacing-md);
            text-align: center;
            color: var(--medium-gray);
            transition: var(--transition);
        }

        .dropdown-menu-item:hover i {
            color: var(--primary-color);
        }

        .dropdown-divider {
            height: 1px;
            margin: var(--spacing-xs) var(--spacing-md);
            background-color: var(--border-color);
        }

        .dropdown-menu-item.logout {
            color: var(--danger-color);
        }

        .dropdown-menu-item.logout i {
            color: var(--danger-color);
        }

        .dropdown-menu-item.logout:hover::after {
            background-color: rgba(244, 67, 54, 0.1);
        }
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .dashboard-card {
            background-color: var(--light-color);
            padding: var(--spacing-xl);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border-color);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: rgba(212, 175, 55, 0.3);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: var(--spacing-lg);
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(212, 175, 55, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: var(--spacing-lg);
            color: var(--primary-color);
            font-size: var(--font-size-lg);
            flex-shrink: 0;
            box-shadow: var(--shadow-xs);
            border: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .card-content {
            flex: 1;
            min-width: 0;
        }
        
        .card-title {
            font-size: var(--font-size-sm);
            color: var(--medium-gray);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--spacing-xs);
            font-weight: 600;
        }
        
        .card-value {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            color: var(--secondary-color);
            margin: 0;
            font-family: var(--font-primary);
        }
        
        .card-footer {
            margin-top: auto;
            padding-top: var(--spacing-lg);
            display: flex;
            justify-content: flex-end;
        }
        
        .card-link {
            font-size: var(--font-size-sm);
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        
        .card-link i {
            margin-left: var(--spacing-xs);
            transition: var(--transition);
            font-size: var(--font-size-xs);
        }
        
        .card-link:hover {
            color: var(--primary-dark);
        }
        
        .card-link:hover i {
            transform: translateX(3px);
        }
        
        /* Sections */
        .dashboard-section {
            background-color: var(--light-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: var(--spacing-xl);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg) var(--spacing-xl);
            border-bottom: 1px solid var(--border-color);
            background-color: var(--light-gray);
        }
        
        .section-title {
            font-size: var(--font-size-lg);
            margin: 0;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            font-family: var(--font-primary);
            font-weight: 600;
        }
        
        .section-title i {
            color: var(--primary-color);
            margin-right: var(--spacing-sm);
        }
        
        .section-action {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: var(--primary-color);
            font-size: var(--font-size-sm);
            font-weight: 500;
            transition: var(--transition);
        }
        
        .section-action i {
            margin-left: var(--spacing-xs);
            transition: var(--transition);
            font-size: var(--font-size-xs);
        }
        
        .section-action:hover {
            color: var(--primary-dark);
        }
        
        .section-action:hover i {
            transform: translateX(3px);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-sm);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .btn-outline i {
            margin-right: var(--spacing-sm);
        }
        
        .btn-outline:hover {
            background-color: var(--primary-color);
            color: var(--light-color);
        }
        
        /* Orders table */
        .orders-container {
            padding: 0 var(--spacing-xl) var(--spacing-xl);
            overflow-x: auto;
        }
        
        .orders-table {
            width: 100%;
            min-width: 800px;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: var(--spacing-md);
        }
        
        .orders-table th {
            text-align: left;
            padding: var(--spacing-md) var(--spacing-md);
            color: var(--medium-gray);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            font-size: var(--font-size-sm);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .orders-table td {
            padding: var(--spacing-lg) var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
            font-size: var(--font-size-sm);
        }
        
        .orders-table tbody tr {
            transition: var(--transition);
        }
        
        .orders-table tbody tr:hover {
            background-color: var(--light-gray);
        }
        
        .orders-table tr:last-child td {
            border-bottom: none;
        }
        
        .order-reference {
            font-weight: 600;
            color: var(--secondary-color);
            font-family: var(--font-primary);
            font-size: var(--font-size-md) !important;
        }
        
        .order-total {
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: var(--spacing-xs) var(--spacing-md);
            border-radius: 30px;
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-badge::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: var(--spacing-sm);
        }
        
        .status-en-attente {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }
        
        .status-en-attente::before {
            background-color: var(--warning-color);
        }
        
        .status-validée {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }
        
        .status-validée::before {
            background-color: var(--success-color);
        }
        
        .status-expediee {
            background-color: rgba(33, 150, 243, 0.1);
            color: var(--info-color);
        }
        
        .status-expediee::before {
            background-color: var(--info-color);
        }
        
        .status-livree {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }
        
        .status-livree::before {
            background-color: var(--success-color);
        }
        
        .status-annulee {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
        }
        
        .status-annulee::before {
            background-color: var(--danger-color);
        }
        
        .btn-view {
            display: inline-flex;
            align-items: center;
            padding: var(--spacing-xs) var(--spacing-md);
            background-color: var(--light-gray);
            color: var(--dark-gray);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-xs);
            transition: var(--transition);
            border: 1px solid var(--border-color);
            font-weight: 500;
        }
        
        .btn-view i {
            margin-right: var(--spacing-xs);
            font-size: var(--font-size-xs);
        }
        
        .btn-view:hover {
            background-color: var(--primary-color);
            color: var(--light-color);
            border-color: var(--primary-color);
        }
        
        /* Empty state */
        .empty-state {
            padding: var(--spacing-xl) !important;
            text-align: center;
        }
        
        .empty-state-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: var(--spacing-xl) 0;
        }
        
        .empty-icon {
            font-size: var(--font-size-2xl);
            color: var(--medium-gray);
            margin-bottom: var(--spacing-lg);
            opacity: 0.5;
            background-color: var(--light-gray);
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .empty-state-content p {
            color: var(--medium-gray);
            margin-bottom: var(--spacing-lg);
            font-size: var(--font-size-md);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
            color: var(--light-color);
            border: none;
            padding: var(--spacing-md) var(--spacing-xl);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-sm);
            transition: var(--transition);
            font-weight: 500;
            box-shadow: var(--shadow-sm);
            display: inline-flex;
            align-items: center;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--secondary-color));
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        
        /* Profile section */
        .profile-container {
            padding: var(--spacing-xl);
        }
        
        .profile-card {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-xl);
        }
        
        .profile-avatar {
            flex-shrink: 0;
        }
        
        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-2xl);
            font-weight: 600;
            overflow: hidden;
            box-shadow: 0 0 0 4px white, 0 0 0 6px rgba(212, 175, 55, 0.3);
        }
        
        .profile-info {
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-xl);
            min-width: 0;
        }
        
        .info-column {
            flex: 1;
            min-width: 250px;
        }
        
        .info-item {
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            display: block;
            font-size: var(--font-size-sm);
            color: var(--medium-gray);
            margin-bottom: var(--spacing-xs);
            font-weight: 500;
        }
        
        .info-label i {
            width: 16px;
            margin-right: var(--spacing-xs);
            color: var(--primary-color);
        }
        
        .info-value {
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        .address-block p {
            margin: 0 0 var(--spacing-xs) 0;
        }
        
        .no-address {
            color: var(--medium-gray);
            font-style: italic;
        }
        
        .btn-small {
            display: inline-block;
            padding: var(--spacing-xs) var(--spacing-md);
            background-color: var(--light-gray);
            color: var(--dark-gray);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-xs);
            margin-top: var(--spacing-sm);
            transition: var(--transition);
            border: 1px solid var(--border-color);
            font-weight: 500;
        }
        
        .btn-small:hover {
            background-color: var(--primary-color);
            color: var(--light-color);
            border-color: var(--primary-color);
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .dashboard-sidebar {
                width: 70px;
                z-index: 100;
            }
            
            .sidebar-header {
                padding: var(--spacing-md);
            }
            
            .sidebar-logo {
                height: 30px;
            }
            
            .sidebar-nav-item span {
                display: none;
            }
            
            .sidebar-nav-item {
                padding: var(--spacing-md);
                justify-content: center;
            }
            
            .sidebar-nav-item i {
                margin-right: 0;
            }
            
            .btn-logout span {
                display: none;
            }
            
            .dashboard-content {
                margin-left: 70px;
            }
            
            .profile-card {
                flex-direction: column;
            }
            
            .profile-avatar {
                margin: 0 auto var(--spacing-lg);
            }
            
            .info-column {
                min-width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-content {
                padding: var(--spacing-md);
            }
            
            .dashboard-topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--spacing-md);
            }
            
            .topbar-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--spacing-md);
            }
            
            .section-action, .btn-outline {
                align-self: flex-end;
            }
            
            .user-dropdown-menu {
                width: 240px;
                right: -10px;
            }
        }
        
        @media (max-width: 576px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card-icon {
                margin-bottom: var(--spacing-md);
                margin-right: 0;
            }
            
            .avatar-circle {
                width: 90px;
                height: 90px;
                font-size: var(--font-size-xl);
            }
        }
        
        /* Animation keyframes */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .dashboard-cards {
            animation: fadeIn 0.6s ease;
        }
        
        .dashboard-card:nth-child(1) {
            animation: slideInUp 0.4s ease forwards;
        }
        
        .dashboard-card:nth-child(2) {
            animation: slideInUp 0.5s ease forwards;
        }
        
        .dashboard-card:nth-child(3) {
            animation: slideInUp 0.6s ease forwards;
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <!-- Inclure la sidebar -->
    <?php require_once 'includes/sidebar.php'; ?>
    
    <!-- Main Content -->
    <main class="dashboard-content">
        <!-- Top Bar -->
        <div class="dashboard-topbar">
            <div class="welcome-message">
                <h1>Bienvenue, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
                <p class="last-connection">Dernière connexion: <?php 
                    echo !empty($user['derniere_connexion']) 
                        ? date('d/m/Y à H:i', strtotime($user['derniere_connexion'])) 
                        : 'Première connexion';
                ?></p>
            </div>
            
            <div class="topbar-actions">
                <a href="<?php echo $relativePath; ?>/pages/Accueil.php" class="btn-return">
                    <i class="fas fa-home"></i> Retour au site
                </a>
                
                <div class="user-dropdown">
                    <button class="user-dropdown-toggle">
                        <div class="avatar-mini">
                            <?php if (!empty($user['photo'])): ?>
                                <img src="<?php echo $relativePath; ?>/uploads/users/<?php echo htmlspecialchars($user['photo']); ?>" 
                                     alt="<?php echo htmlspecialchars($user['prenom']); ?>"
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <img src="<?php echo $relativePath; ?>/uploads/users/default.png" 
                                     alt="Photo de profil par défaut"
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php endif; ?>
                        </div>
                        <span class="user-name"><?php echo htmlspecialchars($user['prenom']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    
                    <div class="user-dropdown-menu">
                        <div class="user-info-header">
                            <div class="user-fullname"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></div>
                            <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        
                        <?php if ($user['role'] === 'admin' || $user['role'] === 'manager'): ?>
                        <a href="../admin/" class="dropdown-menu-item">
                            <i class="fas fa-cog"></i>
                            <span>Administration</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <?php endif; ?>
                        
                        <a href="../php/api/auth/logout.php" class="dropdown-menu-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Commandes</h3>
                        <p class="card-value"><?php echo count($recentOrders); ?></p>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="orders.php" class="card-link">
                        Voir tout <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Favoris</h3>
                        <p class="card-value">0</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="wishlist.php" class="card-link">
                        Voir tout <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Adresses</h3>
                        <p class="card-value">1</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="addresses.php" class="card-link">
                        Gérer <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders Section -->
        <section class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-history"></i> Commandes récentes
                </h2>
                <a href="orders.php" class="section-action">
                    Voir tout <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="orders-container">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-state-content">
                                        <i class="fas fa-shopping-cart empty-icon"></i>
                                        <p>Vous n'avez pas encore passé de commande</p>
                                        <a href="../pages/Montres.php" class="btn-primary">Découvrir nos montres</a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td class="order-reference">#<?php echo $order['reference'] ?? $order['id']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($order['date_commande'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($order['statut']); ?>">
                                            <?php echo $order['statut']; ?>
                                        </span>
                                    </td>
                                    <td class="order-total"><?php echo number_format($order['total'], 2, ',', ' '); ?> €</td>
                                    <td>
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-view">
                                            <i class="fas fa-eye"></i> Détails
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <!-- Profile Summary Section -->
        <section class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-user-circle"></i> Mes informations
                </h2>
                <a href="profile.php" class="btn-outline">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            </div>
            
            <div class="profile-container">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <div class="avatar-circle">
                            <?php if (!empty($user['photo'])): ?>
                                <img src="<?php echo $relativePath; ?>/uploads/users/<?php echo htmlspecialchars($user['photo']); ?>" 
                                     alt="<?php echo htmlspecialchars($user['prenom']); ?>"
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <img src="<?php echo $relativePath; ?>/uploads/users/default.png" 
                                     alt="Photo de profil par défaut"
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="profile-info">
                        <div class="info-column">
                            <div class="info-item">
                                <span class="info-label"><i class="fas fa-user"></i> Nom complet</span>
                                <span class="info-value"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="fas fa-envelope"></i> Email</span>
                                <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="fas fa-phone"></i> Téléphone</span>
                                <span class="info-value"><?php echo !empty($user['telephone']) ? htmlspecialchars($user['telephone']) : 'Non renseigné'; ?></span>
                            </div>
                        </div>
                        
                        <div class="info-column">
                            <div class="info-item">
                                <span class="info-label"><i class="fas fa-map-marker-alt"></i> Adresse</span>
                                <div class="info-value address-block">
                                    <?php if (!empty($user['adresse'])): ?>
                                        <p><?php echo htmlspecialchars($user['adresse']); ?></p>
                                        <p><?php echo htmlspecialchars($user['code_postal'] . ' ' . $user['ville']); ?></p>
                                        <p><?php echo htmlspecialchars($user['pays']); ?></p>
                                    <?php else: ?>
                                        <p class="no-address">Aucune adresse enregistrée</p>
                                        <a href="addresses.php" class="btn-small">Ajouter une adresse</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Script simple sans dépendances externes -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle user dropdown
    const userDropdownToggle = document.querySelector('.user-dropdown-toggle');
    const userDropdownMenu = document.querySelector('.user-dropdown-menu');
    
    if (userDropdownToggle && userDropdownMenu) {
        userDropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.toggle('active');
        });
        
        // Fermer le menu si on clique ailleurs sur la page
        document.addEventListener('click', function(e) {
            if (userDropdownToggle.classList.contains('active') && 
                !userDropdownMenu.contains(e.target) && 
                !userDropdownToggle.contains(e.target)) {
                userDropdownToggle.classList.remove('active');
            }
        });
    }
});
</script>

</body>
</html>