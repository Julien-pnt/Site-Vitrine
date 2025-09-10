<?php
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';
require_once '../../../php/models/User.php';
require_once '../../../php/utils/Logger.php';

// Vérification de l'authentification admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /Site-Vitrine/public/pages/auth/login.php?redirect=admin/users');
    exit;
}

// Initialiser les objets
$userModel = new User($pdo);
$logger = new Logger($pdo);

// Logger l'accès à la page
$logger->info('admin', 'access_users_list', [
    'details' => 'Accès à la liste des utilisateurs'
]);

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15; // nombre d'utilisateurs par page
$offset = ($page - 1) * $limit;

// Construire les filtres pour la requête
$filters = [];

// Filtre par rôle
if (isset($_GET['role']) && !empty($_GET['role'])) {
    $filters['role'] = $_GET['role'];
}

// Filtre par statut
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'active') {
        $filters['actif'] = 1;
    } elseif ($_GET['status'] === 'inactive') {
        $filters['actif'] = 0;
    }
}

// Recherche par nom, prénom ou email
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Tri
$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'date_creation';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

// Récupérer les utilisateurs
$users = $userModel->getAll($offset, $limit, $filters, $sortField, $sortOrder);
$totalUsers = $userModel->countAll($filters);
$totalPages = ceil($totalUsers / $limit);

// Récupérer les rôles disponibles pour le filtre
$roles = $userModel->getRoles();

// Créer un token CSRF pour les formulaires
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - Admin Elixir du Temps</title>
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/admin.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/users.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/header.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="../js/header.js" defer></script>
    <style>
        /* Améliorations de style pour la page utilisateurs */
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f3f3f3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #555;
            border: 2px solid rgba(212, 175, 55, 0.2);
            overflow: hidden;
            font-size: 14px;
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .data-table thead th {
            background: #f8f9fa;
            padding: 15px;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            text-align: left;
            position: relative;
        }
        
        .data-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .data-table tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.05);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            z-index: 2;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .role-badge, .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .role-admin {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .role-manager {
            background-color: rgba(255, 193, 7, 0.15);
            color: #e0a800;
        }
        
        .role-client {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        
        .status-active {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status-inactive {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s ease;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 15px;
        }
        
        .btn-icon:first-child {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }
        
        .btn-icon:nth-child(2) {
            color: #fd7e14;
            background-color: rgba(253, 126, 20, 0.1);
        }
        
        .btn-icon.delete-user {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 25px;
            animation: fadeIn 0.5s ease;
        }
        
        .card-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-header h2 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
            color: #212529;
        }
        
        .card-body {
            padding: 20px;
            background: white;
        }
        
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }
        
        .form-group input, .form-group select {
            padding: 10px 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 100%;
            transition: all 0.2s ease;
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.25);
            outline: none;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #d4af37, #e6c863);
            color: white;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #c4a030, #d6b853);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(212, 175, 55, 0.25);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #ced4da;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.2);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .page-header h1 {
            font-size: 2rem;
            margin: 0;
            color: #212529;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-header h1 i {
            color: #d4af37;
        }
        
        .page-actions {
            display: flex;
            gap: 10px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            position: relative;
            animation: slideDown 0.4s ease;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid #28a745;
            color: #28a745;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            color: #dc3545;
        }
        
        .close-alert {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 25px;
        }
        
        .pagination a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 4px;
            text-decoration: none;
            color: #495057;
            background: white;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }
        
        .pagination a:hover {
            background: #f8f9fa;
            border-color: #d4af37;
        }
        
        .pagination a.active {
            background: #d4af37;
            color: white;
            border-color: #d4af37;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }
        
        .modal.show {
            display: flex;
            animation: fadeIn 0.3s ease;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: zoomIn 0.3s ease-out;
        }
        
        .modal-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #212529;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            padding: 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes zoomIn {
            from { 
                opacity: 0; 
                transform: scale(0.9);
            }
            to { 
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .filter-container {
                flex-direction: column;
            }
            
            .form-actions {
                margin-left: 0;
                margin-top: 15px;
                width: 100%;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .page-actions {
                width: 100%;
            }
            
            .data-table th:nth-child(1),
            .data-table td:nth-child(1),
            .data-table th:nth-child(6),
            .data-table td:nth-child(6) {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .data-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .btn-icon {
                width: 32px;
                height: 32px;
            }
            
            .card-header {
                padding: 15px;
            }
            
            .card-body {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Utilisation du template de sidebar -->
        <?php 
        $admin_root = '../';
        include '../templates/sidebar.php'; 
        ?>

        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header (nous sommes dans un sous-dossier)
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
                    <h1><i class="fas fa-users"></i> Gestion des utilisateurs</h1>
                    <div class="page-actions">
                        <a href="create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvel utilisateur
                        </a>
                        <button id="exportBtn" class="btn btn-secondary">
                            <i class="fas fa-file-export"></i> Exporter
                        </button>
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
                
                <!-- Carte de filtres -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-filter"></i> Filtres</h2>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="" id="filter-form">
                            <div class="filter-container">
                                <div class="form-group">
                                    <label for="search">Recherche</label>
                                    <input type="text" id="search" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Nom, email...">
                                </div>
                                <div class="form-group">
                                    <label for="role">Rôle</label>
                                    <select id="role" name="role">
                                        <option value="">Tous les rôles</option>
                                        <?php foreach($roles as $role): ?>
                                        <option value="<?= htmlspecialchars($role) ?>" <?= isset($_GET['role']) && $_GET['role'] === $role ? 'selected' : '' ?>>
                                            <?= ucfirst(htmlspecialchars($role)) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="status">Statut</label>
                                    <select id="status" name="status">
                                        <option value="">Tous les statuts</option>
                                        <option value="active" <?= isset($_GET['status']) && $_GET['status'] === 'active' ? 'selected' : '' ?>>Actif</option>
                                        <option value="inactive" <?= isset($_GET['status']) && $_GET['status'] === 'inactive' ? 'selected' : '' ?>>Inactif</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="sort">Trier par</label>
                                    <select id="sort" name="sort">
                                        <option value="date_creation" <?= $sortField === 'date_creation' ? 'selected' : '' ?>>Date d'inscription</option>
                                        <option value="nom" <?= $sortField === 'nom' ? 'selected' : '' ?>>Nom</option>
                                        <option value="derniere_connexion" <?= $sortField === 'derniere_connexion' ? 'selected' : '' ?>>Dernière connexion</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="order">Ordre</label>
                                    <select id="order" name="order">
                                        <option value="desc" <?= $sortOrder === 'DESC' ? 'selected' : '' ?>>Décroissant</option>
                                        <option value="asc" <?= $sortOrder === 'ASC' ? 'selected' : '' ?>>Croissant</option>
                                    </select>
                                </div>
                                <div class="form-group form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filtrer
                                    </button>
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-sync-alt"></i> Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Liste des utilisateurs -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Liste des utilisateurs (<?= number_format($totalUsers) ?>)</h2>
                    </div>
                    <div class="card-body">
                        <?php if (count($users) > 0): ?>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Rôle</th>
                                            <th>Statut</th>
                                            <th>Date d'inscription</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($users as $user): ?>
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar">
                                                        <?php if(!empty($user['photo'])): ?>
                                                            <img src="../../uploads/users/<?= htmlspecialchars($user['photo']) ?>" alt="<?= htmlspecialchars($user['prenom']) ?>">
                                                        <?php else: ?>
                                                            <?= strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1)) ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <span class="role-badge role-<?= htmlspecialchars(strtolower($user['role'])) ?>">
                                                    <?= htmlspecialchars(ucfirst($user['role'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= $user['actif'] ? 'active' : 'inactive' ?>">
                                                    <?= $user['actif'] ? 'Actif' : 'Inactif' ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($user['date_creation'])) ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="view.php?id=<?= $user['id'] ?>" class="btn-icon" title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit.php?id=<?= $user['id'] ?>" class="btn-icon" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if($user['id'] != $_SESSION['user_id']): ?>
                                                        <button type="button" class="btn-icon delete-user" 
                                                                data-id="<?= $user['id'] ?>" 
                                                                data-name="<?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>"
                                                                title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if($totalPages > 1): ?>
                            <div class="pagination">
                                <?php 
                                // Générer les paramètres de requête pour la pagination
                                $queryParams = $_GET;
                                unset($queryParams['page']); // Supprimer page des paramètres existants
                                $queryString = http_build_query($queryParams);
                                $queryString = $queryString ? '&' . $queryString : '';
                                ?>
                                
                                <?php if($page > 1): ?>
                                <a href="?page=<?= $page - 1 . $queryString ?>">&laquo;</a>
                                <?php endif; ?>
                                
                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $startPage + 4);
                                if($endPage - $startPage < 4) {
                                    $startPage = max(1, $endPage - 4);
                                }
                                
                                for($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                <a href="?page=<?= $i . $queryString ?>" <?= $i == $page ? 'class="active"' : '' ?>>
                                    <?= $i ?>
                                </a>
                                <?php endfor; ?>
                                
                                <?php if($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 . $queryString ?>">&raquo;</a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <h3>Aucun utilisateur trouvé</h3>
                                <p>Modifiez vos critères de recherche ou créez un nouvel utilisateur.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal de confirmation de suppression -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-danger"></i> Confirmer la suppression</h3>
                <button type="button" class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userName"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal">Annuler</button>
                <form action="delete.php" method="POST" id="deleteForm">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        // Script amélioré pour la gestion des utilisateurs
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du modal de suppression
            const deleteModal = document.getElementById('deleteModal');
            const deleteButtons = document.querySelectorAll('.delete-user');
            const closeModalButtons = document.querySelectorAll('.close-modal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteUserId = document.getElementById('deleteUserId');
            const userName = document.getElementById('userName');
            
            // Ouvrir le modal de suppression
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    
                    deleteUserId.value = id;
                    userName.textContent = name;
                    
                    deleteModal.classList.add('show');
                });
            });
            
            // Fermer le modal
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    deleteModal.classList.remove('show');
                });
            });
            
            // Fermer le modal en cliquant à l'extérieur
            deleteModal.addEventListener('click', function(e) {
                if (e.target === deleteModal) {
                    deleteModal.classList.remove('show');
                }
            });
            
            // Fermer les alertes
            const closeAlerts = document.querySelectorAll('.close-alert');
            closeAlerts.forEach(button => {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert');
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                });
            });
            
            // Auto-submit du formulaire lorsqu'un tri est changé
            document.getElementById('role').addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
            
            document.getElementById('status').addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
            
            document.getElementById('sort').addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
            
            document.getElementById('order').addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
            
            // Exportation
            document.getElementById('exportBtn').addEventListener('click', function() {
                // Récupérer les paramètres de filtre actuels
                const urlParams = new URLSearchParams(window.location.search);
                
                // Construire l'URL d'export avec les mêmes filtres
                let exportUrl = 'export.php?';
                if (urlParams.has('role')) exportUrl += 'role=' + urlParams.get('role') + '&';
                if (urlParams.has('status')) exportUrl += 'status=' + urlParams.get('status') + '&';
                if (urlParams.has('search')) exportUrl += 'search=' + urlParams.get('search') + '&';
                
                // Ajouter un token CSRF
                exportUrl += 'csrf_token=<?= $_SESSION['csrf_token'] ?>';
                
                window.location.href = exportUrl;
            });
            
            // Animation des lignes du tableau lorsqu'elles deviennent visibles
            const tableRows = document.querySelectorAll('.data-table tbody tr');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });
            
            tableRows.forEach((row, index) => {
                row.style.opacity = 0;
                row.style.transform = 'translateY(20px)';
                row.style.transition = `all 0.3s ease ${index * 0.05}s`;
                observer.observe(row);
            });
        });
    </script>
</body>
</html>