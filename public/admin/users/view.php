<?php
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';
require_once '../../../php/models/User.php';
require_once '../../../php/utils/Logger.php';

// Créer le répertoire d'upload s'il n'existe pas
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/uploads/users/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Vérification de l'authentification admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /Site-Vitrine/public/pages/auth/login.php?redirect=admin/users');
    exit;
}

// Initialiser les objets
$userModel = new User($pdo);
$logger = new Logger($pdo);

// Vérifier l'ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID d'utilisateur invalide";
    $_SESSION['message_type'] = "danger";
    header('Location: index.php');
    exit;
}

$userId = (int)$_GET['id'];

// Récupérer l'utilisateur
$user = $userModel->getUserById($userId);

if (!$user) {
    $_SESSION['message'] = "Utilisateur non trouvé";
    $_SESSION['message_type'] = "danger";
    header('Location: index.php');
    exit;
}

// Définir une valeur par défaut pour 'actif' s'il n'existe pas
// Selon votre structure de base de données, la valeur par défaut est TRUE
if (!isset($user['actif'])) {
    // Définir comme actif par défaut (correspondant à votre DEFAULT TRUE dans la BD)
    $user['actif'] = 1;
}

// Ensuite, vous pouvez utiliser la variable directement sans vérification supplémentaire
$isActive = (bool)$user['actif'];

// Ajouter des valeurs par défaut pour les colonnes potentiellement manquantes
$user['photo'] = $user['photo'] ?? null;
$user['derniere_connexion'] = $user['derniere_connexion'] ?? null;

// S'assurer que les clés importantes existent toujours
$user['actif'] = $user['actif'] ?? 0; // 0 par défaut si non défini
$user['role'] = $user['role'] ?? 'client'; // client par défaut si non défini

// Logger l'accès
$logger->info('admin', 'view_user', [
    'user_id' => $userId,
    'entity_type' => 'user',
    'entity_id' => $userId,
    'details' => "Consultation des détails de l'utilisateur {$user['prenom']} {$user['nom']}"
]);

// Récupérer les logs d'activité de l'utilisateur
$userLogs = $logger->getUserActivity($userId, 10);

// Formater les dates pour l'affichage
$dateCreation = new DateTime($user['date_creation'] ?? 'now');
$dateDerniereConnexion = !empty($user['derniere_connexion']) ? new DateTime($user['derniere_connexion']) : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails utilisateur - Administration</title>
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/users.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="../js/header.js" defer></script>
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

        .user-details-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 0;
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
            background: var(--primary-light);
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .page-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .btn-gold {
            background-color: var(--primary-color);
            color: #fff;
            border: 1px solid var(--primary-color);
        }

        .btn-gold:hover {
            background-color: #c49e30;
            border-color: #c49e30;
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            color: #495057;
            border: 1px solid #ced4da;
        }

        .btn-outline:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
        }
        
        .btn-outline-secondary {
            color: var(--secondary-color);
            border-color: var(--secondary-color);
            background: transparent;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .close-alert {
            background: none;
            border: none;
            color: inherit;
            font-size: 1.25rem;
            cursor: pointer;
            opacity: 0.5;
            transition: var(--transition);
        }

        .close-alert:hover {
            opacity: 1;
        }

        /* User profile */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .user-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            background: #f5f5f7;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-avatar-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f0f0f0;
            color: #aaa;
        }

        .avatar-placeholder i {
            font-size: 2.5rem;
        }

        .user-info-large {
            flex: 1;
        }

        .user-info-large h2 {
            margin: 0 0 0.75rem 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #212529;
        }

        .role-badge, .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.25rem 0.75rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-admin {
            background: rgba(13, 110, 253, 0.15);
            color: #0d6efd;
        }

        .role-client {
            background: rgba(32, 201, 151, 0.15);
            color: #20c997;
        }

        .status-active {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-inactive {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        /* Info grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
        }

        .info-item {
            margin-bottom: 0.5rem;
        }

        .info-label {
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        .info-value {
            font-size: 1rem;
        }

        .info-value a {
            color: #0d6efd;
            text-decoration: none;
            transition: var(--transition);
        }

        .info-value a:hover {
            color: #0a58ca;
            text-decoration: underline;
        }

        .not-defined {
            color: #6c757d;
            font-style: italic;
        }

        /* Activity timeline */
        .activity-timeline {
            position: relative;
            padding-left: 1.25rem;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
        }

        .activity-item {
            position: relative;
            padding-bottom: 1.5rem;
            padding-left: 2rem;
        }

        .activity-item:last-child {
            padding-bottom: 0;
        }

        .activity-icon {
            position: absolute;
            left: -0.6rem;
            top: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.875rem;
            z-index: 1;
        }

        .bg-primary { background-color: #0d6efd; }
        .bg-success { background-color: #28a745; }
        .bg-info { background-color: #17a2b8; }
        .bg-warning { background-color: #ffc107; }
        .bg-danger { background-color: #dc3545; }

        .activity-content {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .activity-title {
            font-weight: 600;
            color: #212529;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .activity-description {
            margin-bottom: 0.75rem;
            color: #495057;
            font-size: 0.925rem;
        }

        .activity-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
            color: #6c757d;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #6c757d;
        }

        .mt-3 {
            margin-top: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .user-profile {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .page-actions {
                width: 100%;
            }
            
            .btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Utiliser le template de sidebar -->
        <?php 
        // Définit la racine relative pour les liens dans la sidebar
        $admin_root = '../';
        include '../templates/sidebar.php'; 
        ?>

        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header
            $admin_root = '../';
            
            // Personnaliser la recherche pour la page utilisateurs
            $search_placeholder = "Rechercher un utilisateur...";
            $search_action = "index.php";
            $search_param = "search";
            $search_value = isset($_GET['search']) ? $_GET['search'] : '';
            
            include '../templates/header.php'; 
            ?>

            <div class="content">
                <div class="page-header">
                    <h1>
                        <i class="fas fa-user"></i> Profil de <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                    </h1>
                    <div class="page-actions">
                        <a href="edit.php?id=<?= $userId ?>" class="btn btn-gold">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>

                <!-- Message d'alerte (succès/erreur) -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                        <?= $_SESSION['message'] ?>
                        <button type="button" class="close-alert">&times;</button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <div class="user-details-container">
                    <!-- Profil utilisateur -->
                    <div class="card user-profile-card">
                        <div class="card-header">
                            <h2><i class="fas fa-id-card"></i> Profil</h2>
                        </div>
                        <div class="card-body">
                            <div class="user-profile">
                                <div class="user-avatar-large">
                                    <?php if(!empty($user['photo'])): ?>
                                        <?php 
                                        // Déterminer le bon chemin de l'image
                                        $imageSrc = '';
                                        if (strpos($user['photo'], '/') === 0) {
                                            // Chemin absolu commence par /
                                            $imageSrc = $user['photo'];
                                            // Vérifier si le fichier existe physiquement
                                            $physical_path = $_SERVER['DOCUMENT_ROOT'] . $imageSrc;
                                            $imageExists = file_exists($physical_path);
                                        } else {
                                            // Chemin relatif vers le dossier uploads/users
                                            $imageSrc = "/Site-Vitrine/public/uploads/users/" . $user['photo'];
                                            // Vérifier si le fichier existe physiquement
                                            $physical_path = $_SERVER['DOCUMENT_ROOT'] . $imageSrc;
                                            $imageExists = file_exists($physical_path);
                                        }
                                        
                                        // Si l'image existe physiquement, l'afficher, sinon montrer l'icône par défaut
                                        if ($imageExists):
                                        ?>
                                            <img src="<?= htmlspecialchars($imageSrc) ?>" alt="Photo de profil">
                                        <?php else: ?>
                                            <div class="avatar-placeholder">
                                                <i class="fas fa-user"></i>
                                                <small style="font-size: 10px; display: block">Image non trouvée</small>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="user-info-large">
                                    <h2><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>
                                    <span class="role-badge role-<?= $user['role'] ?>"><?= ucfirst(htmlspecialchars($user['role'])) ?></span>
                                    <span class="status-badge status-<?= $isActive ? 'active' : 'inactive' ?>">
                                        <?= $isActive ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de contact -->
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-address-book"></i> Informations de contact</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">
                                        <a href="mailto:<?= htmlspecialchars($user['email']) ?>">
                                            <?= htmlspecialchars($user['email']) ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Téléphone</div>
                                    <div class="info-value">
                                        <?php if(!empty($user['telephone'])): ?>
                                            <a href="tel:<?= htmlspecialchars($user['telephone']) ?>">
                                                <?= htmlspecialchars($user['telephone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="not-defined">Non défini</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations du compte -->
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-user-shield"></i> Informations du compte</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Date de création</div>
                                    <div class="info-value">
                                        <?= $dateCreation->format('d/m/Y à H:i') ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Dernière connexion</div>
                                    <div class="info-value">
                                        <?= $dateDerniereConnexion ? $dateDerniereConnexion->format('d/m/Y à H:i') : '<span class="not-defined">Jamais connecté</span>' ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Statut</div>
                                    <div class="info-value">
                                        <span class="status-badge status-<?= $isActive ? 'active' : 'inactive' ?>">
                                            <?= $isActive ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Rôle</div>
                                    <div class="info-value">
                                        <span class="role-badge role-<?= $user['role'] ?>"><?= ucfirst(htmlspecialchars($user['role'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-map-marker-alt"></i> Adresse</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Adresse</div>
                                    <div class="info-value">
                                        <?= !empty($user['adresse']) ? htmlspecialchars($user['adresse']) : '<span class="not-defined">Non définie</span>' ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Code postal</div>
                                    <div class="info-value">
                                        <?= !empty($user['code_postal']) ? htmlspecialchars($user['code_postal']) : '<span class="not-defined">Non défini</span>' ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Ville</div>
                                    <div class="info-value">
                                        <?= !empty($user['ville']) ? htmlspecialchars($user['ville']) : '<span class="not-defined">Non définie</span>' ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Pays</div>
                                    <div class="info-value">
                                        <?= !empty($user['pays']) ? htmlspecialchars($user['pays']) : '<span class="not-defined">Non défini</span>' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activité récente -->
                    <div class="card" style="grid-column: 1 / -1;">
                        <div class="card-header">
                            <h2><i class="fas fa-history"></i> Activité récente</h2>
                        </div>
                        <div class="card-body">
                            <?php if (empty($userLogs)): ?>
                                <p class="text-center text-muted">Aucune activité enregistrée pour cet utilisateur</p>
                            <?php else: ?>
                                <div class="activity-timeline">
                                    <?php foreach ($userLogs as $log): ?>
                                        <?php 
                                            $logDate = new DateTime($log['created_at'] ?? $log['date_creation'] ?? 'now');
                                            $logDetails = is_string($log['details']) ? json_decode($log['details'], true) : $log['details'];
                                            
                                            // Déterminer l'icône basée sur le type et l'action
                                            $iconClass = 'fa-circle-info'; // Icône par défaut

                                            if ((isset($log['category']) && $log['category'] === 'auth') || (isset($log['type']) && $log['type'] === 'auth')) {
                                                $iconClass = (isset($log['action']) && $log['action'] === 'login') ? 'fa-sign-in-alt' : 'fa-sign-out-alt';
                                            } elseif ((isset($log['category']) && $log['category'] === 'user') || (isset($log['type']) && $log['type'] === 'user')) {
                                                $iconClass = 'fa-user-edit';
                                            } elseif ((isset($log['category']) && $log['category'] === 'order') || (isset($log['type']) && $log['type'] === 'order')) {
                                                $iconClass = 'fa-shopping-cart';
                                            } elseif ((isset($log['category']) && $log['category'] === 'admin') || (isset($log['type']) && $log['type'] === 'admin')) {
                                                $iconClass = 'fa-tools';
                                            }

                                            // Déterminer la classe de couleur basée sur le type
                                            $colorClass = 'info'; // Couleur par défaut

                                            if (($log['category'] ?? $log['type'] ?? '') === 'auth') {
                                                $colorClass = 'success';
                                            } elseif (($log['level'] ?? $log['type'] ?? '') === 'error') {
                                                $colorClass = 'danger';
                                            } elseif (($log['category'] ?? $log['type'] ?? '') === 'admin') {
                                                $colorClass = 'primary';
                                            } elseif (($log['category'] ?? $log['type'] ?? '') === 'order') {
                                                $colorClass = 'warning';
                                            }
                                        ?>
                                        <div class="activity-item">
                                            <div class="activity-icon bg-<?= $colorClass ?>">
                                                <i class="fas <?= $iconClass ?>"></i>
                                            </div>
                                            <div class="activity-content">
                                                <div class="activity-header">
                                                    <span class="activity-title">
                                                        <?= ucfirst(str_replace('_', ' ', $log['action'] ?? $log['type'] ?? 'action')) ?>
                                                    </span>
                                                    <span class="activity-time">
                                                        <?= $logDate->format('d/m/Y à H:i') ?>
                                                    </span>
                                                </div>
                                                <div class="activity-description">
                                                    <?php 
                                                    // Afficher les détails de l'activité de manière lisible
                                                    if (is_array($logDetails)) {
                                                        if (isset($logDetails['details'])) {
                                                            echo htmlspecialchars($logDetails['details']);
                                                        } elseif (isset($logDetails['message'])) {
                                                            echo htmlspecialchars($logDetails['message']);
                                                        } else {
                                                            foreach ($logDetails as $key => $value) {
                                                                if ($key !== 'user_id' && $key !== 'ip' && !is_array($value)) {
                                                                    echo "<div><strong>".htmlspecialchars(ucfirst($key)).":</strong> ".htmlspecialchars($value)."</div>";
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        echo htmlspecialchars($log['details'] ?? 'Aucun détail disponible');
                                                    }
                                                    ?>
                                                </div>
                                                <div class="activity-meta">
                                                    <span class="activity-ip" title="Adresse IP">
                                                        <i class="fas fa-network-wired"></i> <?= htmlspecialchars($log['ip_address'] ?? $log['ip'] ?? 'Inconnue') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <?php if (count($userLogs) >= 10): ?>
                                    <div class="text-center mt-3">
                                        <a href="user-activity.php?id=<?= $userId ?>" class="btn btn-sm btn-outline-secondary">
                                            Voir toutes les activités <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fermer les alertes
            const closeButtons = document.querySelectorAll('.close-alert');
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
        });
    </script>
</body>
</html>