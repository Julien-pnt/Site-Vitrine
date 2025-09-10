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

// Vérifier si la table promotions existe
$tableExists = false;
try {
    $stmt = $pdo->query("SELECT 1 FROM promotions LIMIT 1");
    $tableExists = true;
} catch (PDOException $e) {
    // Si erreur, c'est probablement que la table n'existe pas
    if (strpos($e->getMessage(), "doesn't exist") !== false) {
        $notification = [
            'type' => 'error',
            'message' => "La table 'promotions' n'existe pas encore. Veuillez l'initialiser en exécutant le script SQL."
        ];
    }
}

// Messages de notifications
$notification = [];

// Traitement des actions CRUD sur les promotions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation CSRF (à implémenter)
    
    // Action: Ajouter une nouvelle promotion
    if (isset($_POST['action']) && $_POST['action'] === 'add_promotion') {
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_SPECIAL_CHARS);
        $value = filter_input(INPUT_POST, 'value', FILTER_VALIDATE_FLOAT);
        $min_purchase = filter_input(INPUT_POST, 'min_purchase', FILTER_VALIDATE_FLOAT) ?: 0;
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $usage_limit = filter_input(INPUT_POST, 'usage_limit', FILTER_VALIDATE_INT) ?: null;
        $products = isset($_POST['products']) ? implode(',', $_POST['products']) : null;
        $collections = isset($_POST['collections']) ? implode(',', $_POST['collections']) : null;
        $active = isset($_POST['active']) ? 1 : 0;
        
        if (!empty($code) && !empty($type) && $value > 0) {
            try {
                $stmt = $pdo->prepare("INSERT INTO promotions (code, description, type, value, min_purchase, 
                                      start_date, end_date, usage_limit, products, collections, active, created_at) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                                      
                $stmt->execute([
                    $code, $description, $type, $value, $min_purchase,
                    $start_date, $end_date, $usage_limit, $products, $collections, $active
                ]);
                
                $notification = [
                    'type' => 'success',
                    'message' => "La promotion \"$code\" a été créée avec succès."
                ];
            } catch (PDOException $e) {
                $notification = [
                    'type' => 'error',
                    'message' => "Erreur lors de la création de la promotion: " . $e->getMessage()
                ];
            }
        } else {
            $notification = [
                'type' => 'error',
                'message' => "Veuillez remplir tous les champs obligatoires."
            ];
        }
    }
    
    // Action: Modifier une promotion existante
    if (isset($_POST['action']) && $_POST['action'] === 'edit_promotion') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_SPECIAL_CHARS);
        $value = filter_input(INPUT_POST, 'value', FILTER_VALIDATE_FLOAT);
        $min_purchase = filter_input(INPUT_POST, 'min_purchase', FILTER_VALIDATE_FLOAT) ?: 0;
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $usage_limit = filter_input(INPUT_POST, 'usage_limit', FILTER_VALIDATE_INT) ?: null;
        $products = isset($_POST['products']) ? implode(',', $_POST['products']) : null;
        $collections = isset($_POST['collections']) ? implode(',', $_POST['collections']) : null;
        $active = isset($_POST['active']) ? 1 : 0;
        
        if ($id && !empty($code) && !empty($type) && $value > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE promotions 
                                      SET code = ?, description = ?, type = ?, value = ?, 
                                      min_purchase = ?, start_date = ?, end_date = ?, 
                                      usage_limit = ?, products = ?, collections = ?, active = ?
                                      WHERE id = ?");
                                      
                $stmt->execute([
                    $code, $description, $type, $value, $min_purchase,
                    $start_date, $end_date, $usage_limit, $products, $collections, $active, $id
                ]);
                
                $notification = [
                    'type' => 'success',
                    'message' => "La promotion \"$code\" a été mise à jour avec succès."
                ];
            } catch (PDOException $e) {
                $notification = [
                    'type' => 'error',
                    'message' => "Erreur lors de la mise à jour de la promotion: " . $e->getMessage()
                ];
            }
        } else {
            $notification = [
                'type' => 'error',
                'message' => "Données de promotion invalides."
            ];
        }
    }
    
    // Action: Supprimer une promotion
    if (isset($_POST['action']) && $_POST['action'] === 'delete_promotion') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if ($id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM promotions WHERE id = ?");
                $stmt->execute([$id]);
                
                $notification = [
                    'type' => 'success',
                    'message' => "La promotion a été supprimée avec succès."
                ];
            } catch (PDOException $e) {
                $notification = [
                    'type' => 'error',
                    'message' => "Erreur lors de la suppression de la promotion: " . $e->getMessage()
                ];
            }
        }
    }
}

// Récupération des promotions
$promotions = [];
try {
    // Utilisation des noms de colonnes corrects de la base de données
    $stmt = $pdo->query("SELECT * FROM promotions ORDER BY created_at DESC");
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $notification = [
        'type' => 'error',
        'message' => "Erreur lors de la récupération des promotions: " . $e->getMessage()
    ];
    
    // Afficher des détails supplémentaires pour le débogage si erreur de colonne
    if (strpos($e->getMessage(), "Column not found") !== false) {
        // Récupérer la structure de la table pour le débogage
        try {
            $columns = $pdo->query("SHOW COLUMNS FROM promotions")->fetchAll(PDO::FETCH_COLUMN, 0);
            $notification['message'] .= "<br>Colonnes disponibles: " . implode(", ", $columns);
        } catch (Exception $e2) {
            // Si même cette requête échoue, la table n'existe probablement pas
            $notification['message'] .= "<br>La table 'promotions' n'existe peut-être pas.";
        }
    }
}

// Récupération des produits et collections pour le formulaire
$products = $collections = [];
try {
    $stmt = $pdo->query("SELECT id, nom as name, reference FROM produits ORDER BY nom");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT id, nom as name FROM collections ORDER BY nom");
    $collections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Silencieux
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Promotions - Elixir du Temps</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/tables.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Flatpickr pour les sélecteurs de date -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script>
    <script src="js/header.js" defer></script>
    
    <style>
        /* Styles modernisés pour la gestion des promotions - harmonisés avec sidebar.css */
        :root {
            /* Variables principales de sidebar.css - COULEUR EXACTE */
            --dark-bg: #212529; /* Couleur exacte de sidebar.css */
            --light-text: #f8f9fa;
            --gray-text: #adb5bd;
            --gold-color: #d4af37;
            
            /* Renommage des variables pour cohérence */
            --primary-color: var(--gold-color);
            --primary-hover: #c4a030;
            --dark-card: #2c3034; /* Légèrement plus clair que le fond pour le contraste */
            --text-light: var(--light-text);
            --text-muted: var(--gray-text);
            
            /* Autres variables inchangées */
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        /* Animation de fade in */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .dashboard {
            padding: 25px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .dashboard h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--text-light);
        }
        
        .dashboard p {
            color: var(--text-muted);
            margin-bottom: 30px;
        }
        
        /* Conteneur du formulaire avec effet de profondeur */
        .promotion-form {
            background-color: var(--dark-card);
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: var(--box-shadow);
            border-left: 4px solid var(--primary-color);
            animation: fadeIn 0.4s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        .promotion-form::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--info), var(--primary-color));
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }
        
        @keyframes gradientMove {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 0%; }
            100% { background-position: 0% 0%; }
        }
        
        .promotion-form h2 {
            margin-top: 0;
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        /* Grid layout amélioré pour les champs du formulaire */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 0;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        /* Labels flottants et focus sur les inputs */
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-light);
            transition: var(--transition);
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .form-group:focus-within label {
            color: var(--primary-color);
            opacity: 1;
        }
        
        .required {
            color: var(--danger);
            margin-left: 3px;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            background-color: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius);
            color: var(--text-light);
            font-size: 14px;
            transition: var(--transition);
        }
        
        input:hover, select:hover, textarea:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
            outline: none;
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        /* Boutons avec effets de survol */
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #e9c767);
            color: #121212;
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #c4a030, #d4af37);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(212, 175, 55, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #e74c3c);
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #c0392b, var(--danger));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
        
        /* Switch moderne pour le toggle de statut actif */
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-top: 25px;
            position: relative;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 0;
            height: 0;
            visibility: hidden;
            position: absolute;
        }
        
        .checkbox-group label {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            margin: 0;
            padding-left: 55px;
        }
        
        .checkbox-group label:before {
            content: '';
            width: 44px;
            height: 24px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 30px;
            position: absolute;
            left: 0;
            transition: var(--transition);
        }
        
        .checkbox-group label:after {
            content: '';
            width: 18px;
            height: 18px;
            background-color: white;
            border-radius: 50%;
            position: absolute;
            left: 3px;
            top: 3px;
            transition: var(--transition);
        }
        
        .checkbox-group input:checked + label:before {
            background-color: var(--primary-color);
        }
        
        .checkbox-group input:checked + label:after {
            left: 23px;
            background-color: white;
        }
        
        /* Badges de statut améliorés */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-badge::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }
        
        .status-active {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }
        
        .status-active::before {
            background-color: var(--success);
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
        }
        
        .status-inactive {
            background-color: rgba(108, 117, 125, 0.1);
            color: var(--text-muted);
        }
        
        .status-inactive::before {
            background-color: var(--text-muted);
        }
        
        .status-expired {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        .status-expired::before {
            background-color: var(--danger);
        }
        
        .status-upcoming {
            background-color: rgba(0, 123, 255, 0.1);
            color: var(--info);
        }
        
        .status-upcoming::before {
            background-color: var(--info);
        }
        
        /* Table des promotions améliorée */
        .table-container {
            background-color: var(--dark-card);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .data-table th {
            background-color: rgba(0, 0, 0, 0.2);
            color: var(--text-light);
            font-weight: 600;
            text-align: left;
            padding: 15px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }
        
        .data-table tr {
            transition: var(--transition);
        }
        
        .data-table tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }
        
        .data-table tr:hover td {
            transform: translateX(5px);
        }
        
        /* Style pour les actions sur les lignes du tableau */
        .actions-cell {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        
        .btn-edit, .btn-delete {
            background: none;
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-light);
        }
        
        .btn-edit {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .btn-edit:hover {
            background-color: rgba(255, 193, 7, 0.2);
            transform: scale(1.05);
        }
        
        .btn-delete {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        .btn-delete:hover {
            background-color: rgba(220, 53, 69, 0.2);
            transform: scale(1.05);
        }
        
        /* Modal de confirmation modernisé */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        
        .modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content {
            background-color: var(--dark-bg);
            border-radius: var(--border-radius);
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            transform: translateY(-30px);
            transition: all 0.3s;
            border-top: 4px solid var(--danger);
        }
        
        .modal.active .modal-content {
            transform: translateY(0);
        }
        
        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .close:hover {
            color: var(--text-light);
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }
        
        /* Toggle pour l'affichage du formulaire */
        .form-toggle {
            margin-bottom: 30px;
        }
        
        .form-toggle .btn {
            position: relative;
            overflow: hidden;
        }
        
        .form-toggle .btn:after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(100, 100);
                opacity: 0;
            }
        }
        
        .form-toggle .btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }
        
        /* Notification styles améliorés */
        .notification {
            padding: 16px 20px;
            margin-bottom: 25px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease-out;
        }
        
        .notification i {
            font-size: 22px;
            margin-right: 15px;
        }
        
        .notification-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid var(--success);
            color: var(--success);
        }
        
        .notification-error {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid var(--danger);
            color: var(--danger);
        }

        /* Style pour le flatpickr - sélecteurs de date */
        .flatpickr-calendar {
            background: var(--dark-card) !important;
            box-shadow: var(--box-shadow) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        
        .flatpickr-day {
            color: var(--text-light) !important;
        }
        
        .flatpickr-day.selected {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        
        .flatpickr-months .flatpickr-month,
        .flatpickr-weekdays {
            background: rgba(0, 0, 0, 0.2) !important;
        }
        
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            color: var(--text-light) !important;
        }
        
        /* Ripple effect for buttons */
        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            pointer-events: none;
            width: 100px;
            height: 100px;
            transform: translate(-50%, -50%) scale(0);
            animation: rippleEffect 0.6s linear;
        }
        
        @keyframes rippleEffect {
            to {
                transform: translate(-50%, -50%) scale(4);
                opacity: 0;
            }
        }
        
        /* Responsive improvements */
        @media (max-width: 992px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .actions button {
                width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard {
                padding: 15px;
            }
            
            .promotion-form {
                padding: 20px;
            }
            
            .data-table th, .data-table td {
                padding: 10px;
            }
            
            /* Simplify tables on mobile */
            .data-table th:nth-child(4),
            .data-table td:nth-child(4),
            .data-table th:nth-child(5),
            .data-table td:nth-child(5) {
                display: none;
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

        <!-- Contenu principal -->
        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header
            $admin_root = '';
            
            // Personnaliser la recherche pour la page promotions
            $search_placeholder = "Rechercher une promotion...";
            $search_action = "promotions.php";
            $search_param = "search";
            $search_value = isset($_GET['search']) ? $_GET['search'] : '';
            
            include 'templates/header.php'; 
            ?>

            <div class="dashboard">
                <h1>Gestion des Promotions</h1>
                <p>Créez et gérez des codes promotionnels pour vos clients.</p>
                
                <?php if (!empty($notification)): ?>
                <div class="notification notification-<?= $notification['type'] ?>">
                    <i class="fas fa-<?= $notification['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($notification['message']) ?>
                </div>
                <?php endif; ?>
                
                <div class="form-toggle">
                    <button id="toggleForm" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle promotion
                    </button>
                </div>
                
                <!-- Formulaire d'ajout/édition -->
                <div id="promotionForm" class="promotion-form" style="display: none;">
                    <h2 id="formTitle">Ajouter une promotion</h2>
                    
                    <form method="POST" id="promoForm">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" id="promotionId" name="id">
                        <input type="hidden" id="formAction" name="action" value="add_promotion">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="code">Code promotionnel <span class="required">*</span></label>
                                <input type="text" id="code" name="code" required placeholder="EX: SUMMER2023">
                            </div>
                            
                            <div class="form-group">
                                <label for="type">Type de réduction <span class="required">*</span></label>
                                <select id="type" name="type" required>
                                    <option value="percentage">Pourcentage (%)</option>
                                    <option value="fixed_amount">Montant fixe (€)</option>
                                    <option value="free_shipping">Livraison gratuite</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="value">Valeur <span class="required">*</span></label>
                                <input type="number" id="value" name="value" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="2" placeholder="Description courte de la promotion"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="start_date">Date de début</label>
                                <input type="text" id="start_date" name="start_date" class="flatpickr" placeholder="JJ/MM/AAAA">
                            </div>
                            
                            <div class="form-group">
                                <label for="end_date">Date de fin</label>
                                <input type="text" id="end_date" name="end_date" class="flatpickr" placeholder="JJ/MM/AAAA">
                            </div>
                            
                            <div class="form-group">
                                <label for="min_purchase">Montant minimum d'achat</label>
                                <input type="number" id="min_purchase" name="min_purchase" step="0.01" min="0" placeholder="0.00">
                            </div>
                            
                            <div class="form-group">
                                <label for="usage_limit">Limite d'utilisation</label>
                                <input type="number" id="usage_limit" name="usage_limit" min="0" placeholder="Illimité">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="products">Produits applicables</label>
                                <select id="products" name="products[]" multiple>
                                    <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['reference']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="collections">Collections applicables</label>
                                <select id="collections" name="collections[]" multiple>
                                    <?php foreach ($collections as $collection): ?>
                                    <option value="<?= $collection['id'] ?>"><?= htmlspecialchars($collection['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="active" name="active" checked>
                            <label for="active">Promotion active</label>
                        </div>
                        
                        <div class="actions">
                            <button type="button" id="cancelButton" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <span id="submitText">Ajouter</span></button>
                        </div>
                    </form>
                </div>
                
                <!-- Liste des promotions -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Valeur</th>
                                <th>Dates</th>
                                <th>Utilisation</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($promotions)): ?>
                                <tr>
                                    <td colspan="7" class="empty-table">Aucune promotion n'a été créée pour le moment</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($promotions as $promo): ?>
                                    <?php
                                    // Déterminer le statut de la promotion
                                    $status = 'inactive';
                                    $statusLabel = 'Inactif';
                                    
                                    if ($promo['active']) {
                                        $now = new DateTime();
                                        $startDate = !empty($promo['start_date']) ? new DateTime($promo['start_date']) : null;
                                        $endDate = !empty($promo['end_date']) ? new DateTime($promo['end_date']) : null;
                                        
                                        if (($startDate === null || $now >= $startDate) && ($endDate === null || $now <= $endDate)) {
                                            $status = 'active';
                                            $statusLabel = 'Actif';
                                        } elseif ($startDate && $now < $startDate) {
                                            $status = 'upcoming';
                                            $statusLabel = 'À venir';
                                        } elseif ($endDate && $now > $endDate) {
                                            $status = 'expired';
                                            $statusLabel = 'Expiré';
                                        }
                                    }
                                    
                                    // Formater le type de réduction
                                    switch ($promo['type']) {
                                        case 'percentage':
                                            $typeLabel = 'Pourcentage';
                                            $valueDisplay = $promo['value'] . '%';
                                            break;
                                        case 'fixed_amount':
                                            $typeLabel = 'Montant fixe';
                                            $valueDisplay = number_format($promo['value'], 2, ',', ' ') . ' €';
                                            break;
                                        case 'free_shipping':
                                            $typeLabel = 'Livraison gratuite';
                                            $valueDisplay = '-';
                                            break;
                                        default:
                                            $typeLabel = $promo['type'];
                                            $valueDisplay = $promo['value'];
                                    }
                                    
                                    // Formater les dates
                                    $dateRange = '';
                                    if (!empty($promo['start_date']) && !empty($promo['end_date'])) {
                                        $dateRange = date('d/m/Y', strtotime($promo['start_date'])) . ' - ' . date('d/m/Y', strtotime($promo['end_date']));
                                    } elseif (!empty($promo['start_date'])) {
                                        $dateRange = 'Depuis le ' . date('d/m/Y', strtotime($promo['start_date']));
                                    } elseif (!empty($promo['end_date'])) {
                                        $dateRange = "Jusqu'au " . date('d/m/Y', strtotime($promo['end_date']));
                                    } else {
                                        $dateRange = 'Illimité';
                                    }
                                    
                                    // Stocker les données JSON pour l'édition
                                    $promoData = htmlspecialchars(json_encode($promo), ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <tr data-promo='<?= $promoData ?>'>
                                        <td><strong><?= htmlspecialchars($promo['code']) ?></strong></td>
                                        <td><?= $typeLabel ?></td>
                                        <td><?= $valueDisplay ?></td>
                                        <td><?= $dateRange ?></td>
                                        <td>
                                            <?php if ($promo['usage_limit']): ?>
                                                <?= $promo['used_count'] ?? 0 ?>/<?= $promo['usage_limit'] ?>
                                            <?php else: ?>
                                                <?= $promo['used_count'] ?? 0 ?> (Illimité)
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="status-badge status-<?= $status ?>"><?= $statusLabel ?></span></td>
                                        <td>
                                            <div class="actions-cell">
                                                <button class="btn-edit" onclick="editPromotion(this)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-delete" onclick="confirmDelete(<?= $promo['id'] ?>, '<?= htmlspecialchars($promo['code']) ?>')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Formulaire de suppression caché -->
                <form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="delete_promotion">
                    <input type="hidden" id="deleteId" name="id">
                </form>
            </div>
        </main>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Confirmer la suppression</h2>
            <p>Êtes-vous sûr de vouloir supprimer la promotion <span id="deletePromoCode"></span> ?</p>
            <div class="modal-actions">
                <button id="cancelDelete" class="btn btn-secondary">Annuler</button>
                <button id="confirmDelete" class="btn btn-danger">Supprimer</button>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser Flatpickr pour les sélecteurs de date
            flatpickr(".flatpickr", {
                dateFormat: "Y-m-d",
                locale: "fr",
                altInput: true,
                altFormat: "d/m/Y",
                allowInput: true
            });
            
            // Toggle du formulaire
            const toggleBtn = document.getElementById('toggleForm');
            const formContainer = document.getElementById('promotionForm');
            const cancelBtn = document.getElementById('cancelButton');
            
            toggleBtn.addEventListener('click', function() {
                resetForm();
                document.getElementById('formTitle').textContent = 'Ajouter une promotion';
                document.getElementById('formAction').value = 'add_promotion';
                document.getElementById('submitText').textContent = 'Ajouter';
                formContainer.style.display = 'block';
                toggleBtn.style.display = 'none';
            });
            
            cancelBtn.addEventListener('click', function() {
                formContainer.style.display = 'none';
                toggleBtn.style.display = 'block';
            });
            
            // Gestion du modal de confirmation de suppression
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const cancelDelete = document.getElementById('cancelDelete');
            const confirmDeleteBtn = document.getElementById('confirmDelete');
            const closeModal = document.querySelector('.close');
            
            // Fermer le modal
            function closeDeleteModal() {
                deleteModal.classList.remove('active');
                setTimeout(() => {
                    deleteModal.style.display = 'none';
                }, 300);
            }
            
            cancelDelete.addEventListener('click', closeDeleteModal);
            closeModal.addEventListener('click', closeDeleteModal);
            
            window.onclick = function(event) {
                if (event.target == deleteModal) {
                    closeDeleteModal();
                }
            };
            
            // Confirmer la suppression
            confirmDeleteBtn.addEventListener('click', function() {
                deleteForm.submit();
            });
            
            // Animation des lignes du tableau lors du chargement
            const tableRows = document.querySelectorAll('.data-table tbody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.animation = `fadeIn 0.3s ease forwards ${index * 0.1}s`;
                setTimeout(() => {
                    row.style.opacity = '1';
                }, index * 100 + 300);
            });
            
            // Dropdown menu toggle
            const userDropdown = document.getElementById('userProfileDropdown');
            if (userDropdown) {
                const dropdownMenu = document.getElementById('userDropdownMenu');
                
                userDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('active');
                });
                
                document.addEventListener('click', function() {
                    dropdownMenu.classList.remove('active');
                });
                
                dropdownMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            // Animation de "ripple" pour les boutons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const x = e.clientX - e.target.getBoundingClientRect().left;
                    const y = e.clientY - e.target.getBoundingClientRect().top;
                    
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    ripple.style.left = `${x}px`;
                    ripple.style.top = `${y}px`;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });
        
        // Fonctions pour l'édition et la suppression
        function editPromotion(button) {
            const promoData = JSON.parse(button.closest('tr').dataset.promo);
            
            // Remplir le formulaire avec les données existantes
            document.getElementById('promotionId').value = promoData.id;
            document.getElementById('code').value = promoData.code;
            document.getElementById('description').value = promoData.description || '';
            document.getElementById('type').value = promoData.type;
            document.getElementById('value').value = promoData.value;
            document.getElementById('min_purchase').value = promoData.min_purchase || '';
            document.getElementById('usage_limit').value = promoData.usage_limit || '';
            document.getElementById('active').checked = promoData.active == 1;
            
            // Dates
            if (promoData.start_date) {
                document.querySelector('#start_date')._flatpickr.setDate(promoData.start_date);
            } else {
                document.querySelector('#start_date')._flatpickr.clear();
            }
            
            if (promoData.end_date) {
                document.querySelector('#end_date')._flatpickr.setDate(promoData.end_date);
            } else {
                document.querySelector('#end_date')._flatpickr.clear();
            }
            
            // Produits et collections (nécessitant Select2)
            if (promoData.products) {
                const productIds = promoData.products.split(',').map(id => id.trim());
                // Si vous utilisez Select2, vous devez l'initialiser et le configurer ici
            }
            
            if (promoData.collections) {
                const collectionIds = promoData.collections.split(',').map(id => id.trim());
                // Même chose pour les collections
            }
            
            // Mettre à jour le formulaire en mode édition
            document.getElementById('formTitle').textContent = 'Modifier la promotion';
            document.getElementById('formAction').value = 'edit_promotion';
            document.getElementById('submitText').textContent = 'Mettre à jour';
            
            // Afficher le formulaire et masquer le bouton d'ajout
            document.getElementById('promotionForm').style.display = 'block';
            document.getElementById('toggleForm').style.display = 'none';
            
            // Scroll jusqu'au formulaire
            document.getElementById('promotionForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        function confirmDelete(id, code) {
            const deleteModal = document.getElementById('deleteModal');
            
            document.getElementById('deleteId').value = id;
            document.getElementById('deletePromoCode').textContent = code;
            
            deleteModal.style.display = 'flex';
            setTimeout(() => {
                deleteModal.classList.add('active');
            }, 10);
        }
        
        function resetForm() {
            document.getElementById('promoForm').reset();
            document.getElementById('promotionId').value = '';
            
            // Réinitialiser flatpickr
            document.querySelector('#start_date')._flatpickr.clear();
            document.querySelector('#end_date')._flatpickr.clear();
            
            // Réinitialiser les sélecteurs multiples (si Select2 est utilisé)
            // $('#products').val(null).trigger('change');
            // $('#collections').val(null).trigger('change');
        }
    </script>
</body>
</html>