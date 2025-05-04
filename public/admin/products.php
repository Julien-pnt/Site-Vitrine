<?php
// Correction du chemin d'accès à la base de données
require_once '../../php/config/database.php';
require_once '../../php/functions/security.php';

// Vérifier si l'utilisateur est connecté et est administrateur
session_start();
if(!isLoggedIn() || !isAdmin()) {
    header('Location: ../../php/api/auth/login.php');
    exit();
}

// Paramètres de pagination et filtrage
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Connexion à la base de données en utilisant la classe Database
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Construction de la requête de base
$query = "SELECT p.*, c.nom as categorie_nom
          FROM produits p
          LEFT JOIN categories c ON p.categorie_id = c.id
          WHERE 1=1";
$countQuery = "SELECT COUNT(*) FROM produits p WHERE 1=1";
$params = [];

// Appliquer les filtres
if (!empty($search)) {
    $query .= " AND (p.nom LIKE :search OR p.description LIKE :search OR p.reference LIKE :search)";
    $countQuery .= " AND (p.nom LIKE :search OR p.description LIKE :search OR p.reference LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($categoryId > 0) {
    $query .= " AND p.categorie_id = :category_id";
    $countQuery .= " AND p.categorie_id = :category_id";
    $params[':category_id'] = $categoryId;
}

switch ($filter) {
    case 'low-stock':
        $query .= " AND p.stock <= p.stock_alerte AND p.stock > 0";
        $countQuery .= " AND p.stock <= p.stock_alerte AND p.stock > 0";
        break;
    case 'out-of-stock':
        $query .= " AND p.stock = 0";
        $countQuery .= " AND p.stock = 0";
        break;
    case 'active':
        $query .= " AND p.visible = 1";
        $countQuery .= " AND p.visible = 1";
        break;
    case 'inactive':
        $query .= " AND p.visible = 0";
        $countQuery .= " AND p.visible = 0";
        break;
}

// Ajouter tri et pagination
$query .= " ORDER BY p.date_creation DESC LIMIT :limit OFFSET :offset";
$params[':limit'] = $limit;
$params[':offset'] = $offset;

// Exécuter les requêtes
try {
    // Récupérer les produits
    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        if ($key == ':limit' || $key == ':offset') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
    }
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer le nombre total de produits
    $countStmt = $pdo->prepare($countQuery);
    foreach ($params as $key => $value) {
        if ($key != ':limit' && $key != ':offset') {
            $countStmt->bindValue($key, $value, PDO::PARAM_STR);
        }
    }
    $countStmt->execute();
    $totalProducts = $countStmt->fetchColumn();
    $totalPages = ceil($totalProducts / $limit);
    
    // Récupérer toutes les catégories pour le filtre
    $categoriesStmt = $pdo->query("SELECT id, nom FROM categories ORDER BY nom");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur lors de la récupération des données: " . $e->getMessage());
}

// S'assurer que $products est au moins un tableau vide si aucun résultat
if ($products === false) {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - Admin Elixir du Temps</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/tables.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/header.js" defer></script>
</head>
<body>
    <div class="admin-container">
        <!-- Utiliser le template de sidebar -->
        <?php 
        // Définit la racine relative pour les liens dans la sidebar
        $admin_root = '';
        include 'templates/sidebar.php'; 
        ?>

        <!-- Contenu principal -->
        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header
            $admin_root = '';
            
            // Personnaliser la recherche pour la page produits
            $search_placeholder = "Rechercher un produit...";
            $search_action = "products.php";
            $search_param = "search";
            $search_value = $search;
            
            include 'templates/header.php'; 
            ?>

            <div class="content-wrapper">
                <div class="content-header">
                    <h1>Produits</h1>
                    <a href="add-product.php" class="btn-primary"><i class="fas fa-plus"></i> Nouveau produit</a>
                </div>

                <div class="filter-container">
                    <div class="filter-group">
                        <label for="category-filter">Catégorie:</label>
                        <select id="category-filter" onchange="applyFilters()">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $categoryId == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status-filter">Statut:</label>
                        <select id="status-filter" onchange="applyFilters()">
                            <option value="" <?= $filter === '' ? 'selected' : '' ?>>Tous</option>
                            <option value="low-stock" <?= $filter === 'low-stock' ? 'selected' : '' ?>>Stock bas</option>
                            <option value="out-of-stock" <?= $filter === 'out-of-stock' ? 'selected' : '' ?>>Rupture de stock</option>
                            <option value="active" <?= $filter === 'active' ? 'selected' : '' ?>>Actifs</option>
                            <option value="inactive" <?= $filter === 'inactive' ? 'selected' : '' ?>>Inactifs</option>
                        </select>
                    </div>
                </div>

                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Référence</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Stock</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($products) > 0): ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td class="product-image">
                                            <?php if ($product['image']): ?>
                                                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>">
                                            <?php else: ?>
                                                <div class="no-image"><i class="fas fa-image"></i></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="product-name"><?= htmlspecialchars($product['nom']) ?></td>
                                        <td><?= htmlspecialchars($product['reference']) ?></td>
                                        <td><?= htmlspecialchars($product['categorie_nom'] ?? 'Non catégorisé') ?></td>
                                        <td class="product-price">
                                            <?= number_format($product['prix'], 2, ',', ' ') ?> €
                                            <?php if ($product['prix_promo']): ?>
                                                <span class="price-promo"><?= number_format($product['prix_promo'], 2, ',', ' ') ?> €</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="product-stock <?= $product['stock'] <= $product['stock_alerte'] ? ($product['stock'] === 0 ? 'stock-out' : 'stock-low') : '' ?>">
                                            <?= $product['stock'] ?>
                                            <span class="stock-alert-threshold">/ <?= $product['stock_alerte'] ?></span>
                                        </td>
                                        <td>
                                            <span class="status-badge <?= $product['visible'] ? 'status-active' : 'status-inactive' ?>">
                                                <?= $product['visible'] ? 'Actif' : 'Inactif' ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn-edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="toggleProductStatus(<?= $product['id'] ?>, <?= $product['visible'] ? 0 : 1 ?>)" 
                                                    class="btn-toggle <?= $product['visible'] ? 'active' : 'inactive' ?>" 
                                                    title="<?= $product['visible'] ? 'Désactiver' : 'Activer' ?>">
                                                <i class="fas <?= $product['visible'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                            </button>
                                            <button onclick="deleteProduct(<?= $product['id'] ?>)" class="btn-delete" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="empty-table">Aucun produit trouvé</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1&limit=<?= $limit ?>&filter=<?= urlencode($filter) ?>&category=<?= $categoryId ?>&search=<?= urlencode($search) ?>" class="page-link">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&filter=<?= urlencode($filter) ?>&category=<?= $categoryId ?>&search=<?= urlencode($search) ?>" class="page-link">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php
                        // Afficher un nombre limité de liens de page
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        if ($startPage > 1) {
                            echo '<span class="page-ellipsis">...</span>';
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <a href="?page=<?= $i ?>&limit=<?= $limit ?>&filter=<?= urlencode($filter) ?>&category=<?= $categoryId ?>&search=<?= urlencode($search) ?>" 
                               class="page-link <?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; 
                        
                        if ($endPage < $totalPages) {
                            echo '<span class="page-ellipsis">...</span>';
                        }
                        ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&filter=<?= urlencode($filter) ?>&category=<?= $categoryId ?>&search=<?= urlencode($search) ?>" class="page-link">
                                <i class="fas fa-angle-right"></i>
                            </a>
                            <a href="?page=<?= $totalPages ?>&limit=<?= $limit ?>&filter=<?= urlencode($filter) ?>&category=<?= $categoryId ?>&search=<?= urlencode($search) ?>" class="page-link">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Container pour les notifications toast -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Scripts -->
    <script>
        function applyFilters() {
            const categoryFilter = document.getElementById('category-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            const searchParam = new URLSearchParams(window.location.search).get('search') || '';
            
            let url = 'products.php?page=1';
            if (categoryFilter) url += '&category=' + categoryFilter;
            if (statusFilter) url += '&filter=' + statusFilter;
            if (searchParam) url += '&search=' + encodeURIComponent(searchParam);
            
            window.location.href = url;
        }

        function toggleProductStatus(productId, newStatus) {
            if (confirm(newStatus ? 'Activer ce produit?' : 'Désactiver ce produit?')) {
                fetch('api/toggle-product-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: productId, visible: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Succès', newStatus ? 'Le produit a été activé' : 'Le produit a été désactivé', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToast('Erreur', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur', 'Une erreur est survenue. Veuillez réessayer.', 'error');
                });
            }
        }

        function deleteProduct(productId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit? Cette action est irréversible.')) {
                fetch('api/delete-product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Succès', 'Le produit a été supprimé', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToast('Erreur', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur', 'Une erreur est survenue. Veuillez réessayer.', 'error');
                });
            }
        }

        // Fonction pour afficher des notifications toast
        function showToast(title, message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <div class="toast-close" onclick="dismissToast(this)">
                    <i class="fas fa-times"></i>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                dismissToast(toast.querySelector('.toast-close'));
            }, 5000);
        }
        
        function dismissToast(closeButton) {
            const toast = closeButton.closest('.toast');
            toast.style.animation = 'slideOut 0.3s ease forwards';
            
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
        
        // Vérifier s'il y a un message dans l'URL (pour les redirections)
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            const status = urlParams.get('status');
            
            if (message) {
                showToast(status === 'error' ? 'Erreur' : 'Succès', message, status || 'success');
                
                // Nettoyer l'URL
                const url = new URL(window.location);
                url.searchParams.delete('message');
                url.searchParams.delete('status');
                window.history.replaceState({}, '', url);
            }
        });
    </script>
</body>
</html>