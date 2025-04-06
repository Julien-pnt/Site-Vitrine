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
    <title>Gestion des utilisateurs - Admin</title>
    <link rel="stylesheet" href="../css/admin.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/users.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Include sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <main class="main-content">
            <!-- Include header -->
            <?php include '../includes/header.php'; ?>

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
                        <h2>Filtres</h2>
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
                        <h2>Liste des utilisateurs (<?= number_format($totalUsers) ?>)</h2>
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
                                <a href="?page=<?= $page - 1 . $queryString ?>">&laquo; Précédent</a>
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
                                <a href="?page=<?= $page + 1 . $queryString ?>">Suivant &raquo;</a>
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
                <h3>Confirmer la suppression</h3>
                <button type="button" class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userName"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal">Annuler</button>
                <form action="delete.php" method="POST" id="deleteForm">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script src="../js/users.js"></script>
</body>
</html>