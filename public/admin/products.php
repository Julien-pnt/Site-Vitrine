<?php
// Initialisation de la session et vérification d'authentification admin
session_start();
require_once '../../php/config/database.php';
require_once '../../php/utils/auth.php';

// Redirection si l'utilisateur n'est pas connecté en tant qu'admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../pages/auth/login.html?error=unauthorized&redirect=admin');
    exit;
}

// Gestion des paramètres de filtrage et de pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Construire la requête SQL de base
$query = "SELECT p.*, c.nom as categorie_nom FROM produits p 
          LEFT JOIN categories c ON p.categorie_id = c.id WHERE 1=1";
$countQuery = "SELECT COUNT(*) as count FROM produits WHERE 1=1";
$params = [];

// Ajouter les conditions de filtrage
if ($filter === 'low-stock') {
    $query .= " AND p.stock <= p.stock_alerte";
    $countQuery .= " AND stock <= stock_alerte";
} elseif ($filter === 'out-of-stock') {
    $query .= " AND p.stock = 0";
    $countQuery .= " AND stock = 0";
} elseif ($filter === 'active') {
    $query .= " AND p.visible = 1";
    $countQuery .= " AND visible = 1";
} elseif ($filter === 'inactive') {
    $query .= " AND p.visible = 0";
    $countQuery .= " AND visible = 0";
}

if ($categoryId > 0) {
    $query .= " AND p.categorie_id = ?";
    $countQuery .= " AND categorie_id = ?";
    $params[] = $categoryId;
}

if (!empty($search)) {
    $query .= " AND (p.nom LIKE ? OR p.reference LIKE ?)";
    $countQuery .= " AND (nom LIKE ? OR reference LIKE ?)";
    $searchParam = "%{$search}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

// Finaliser la requête avec tri et pagination
$query .= " ORDER BY p.id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Exécuter les requêtes
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total de produits correspondant aux critères
$countParams = array_slice($params, 0, -2); // Exclure limit et offset
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($countParams);
$totalProducts = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
$totalPages = ceil($totalProducts / $limit);

// Récupérer la liste des catégories pour le filtre
$categoriesStmt = $pdo->query("SELECT id, nom FROM categories ORDER BY nom");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar de navigation (même code que dans index.php) -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <a href="index.php">
                    <!-- Utilisez la classe sidebar-logo pour contraindre la taille -->
                    <img src="../assets/img/layout/logo.png" alt="Elixir du Temps" class="sidebar-logo">
                    <span>Administration</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-heading">Navigation</h3>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                        <li class="active"><a href="products.php"><i class="fas fa-watch"></i> Produits</a></li>
                        <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
                        <li><a href="collections.php"><i class="fas fa-layer-group"></i> Collections</a></li>
                        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                        <li><a href="users/index.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                        <li><a href="reviews.php"><i class="fas fa-star"></i> Avis Clients</a></li>
                        <li><a href="pages.php"><i class="fas fa-file-alt"></i> Pages</a></li>
                        <li><a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                    </ul>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <a href="../pages/Accueil.html" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
                <a href="../../php/api/auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-search">
                    <form action="products.php" method="GET">
                        <input type="search" name="search" placeholder="Rechercher un produit..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="header-user">
                    <span>Gestion des produits</span>
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </header>

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
                        window.location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue. Veuillez réessayer.');
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
                        window.location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue. Veuillez réessayer.');
                });
            }
        }
    </script>
</body>
</html>