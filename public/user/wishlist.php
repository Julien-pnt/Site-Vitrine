<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/auth/login.html');
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

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Nombre de produits par page
$offset = ($page - 1) * $limit;

// Récupérer les produits favoris de l'utilisateur
$stmt = $conn->prepare("
    SELECT p.*, f.date_ajout
    FROM favoris f
    JOIN produits p ON f.produit_id = p.id
    WHERE f.utilisateur_id = ?
    ORDER BY f.date_ajout DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$userId, $limit, $offset]);
$wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total de favoris pour la pagination
$stmt = $conn->prepare("SELECT COUNT(*) FROM favoris WHERE utilisateur_id = ?");
$stmt->execute([$userId]);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// Titre de la page
$pageTitle = "Mes favoris";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | Elixir du Temps</title>
    
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components/header.css">
    <link rel="stylesheet" href="../assets/css/components/footer.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- En-tête -->
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <!-- Barre latérale -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Contenu principal -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <h1>Mes favoris</h1>
                <p>Les produits que vous avez ajoutés à votre liste de souhaits</p>
            </div>
            
            <?php if (empty($wishlistItems)): ?>
                <div class="empty-state-container">
                    <div class="empty-state">
                        <i class="fas fa-heart empty-icon"></i>
                        <h2>Aucun favori</h2>
                        <p>Vous n'avez pas encore ajouté de produits à vos favoris.</p>
                        <a href="../pages/products/Montres.html" class="btn-primary">Découvrir nos montres</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="wishlist-actions">
                    <button id="clear-wishlist" class="btn-secondary">
                        <i class="fas fa-trash-alt"></i> Vider ma liste
                    </button>
                </div>
                
                <div class="wishlist-grid">
                    <?php foreach ($wishlistItems as $item): ?>
                        <div class="wishlist-item" data-id="<?php echo $item['id']; ?>">
                            <div class="wishlist-item-image">
                                <a href="../pages/products/product-details.php?id=<?php echo $item['id']; ?>">
                                    <img src="../assets/img/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['nom']); ?>">
                                </a>
                                <button class="remove-from-wishlist" data-id="<?php echo $item['id']; ?>">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="wishlist-item-details">
                                <h3 class="item-title">
                                    <a href="../pages/products/product-details.php?id=<?php echo $item['id']; ?>">
                                        <?php echo htmlspecialchars($item['nom']); ?>
                                    </a>
                                </h3>
                                
                                <div class="item-reference">
                                    <?php if (!empty($item['reference'])): ?>
                                        Réf: <?php echo htmlspecialchars($item['reference']); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-price">
                                    <?php if (!empty($item['prix_promo']) && $item['prix_promo'] < $item['prix']): ?>
                                        <span class="old-price"><?php echo number_format($item['prix'], 2, ',', ' '); ?> €</span>
                                        <span class="current-price"><?php echo number_format($item['prix_promo'], 2, ',', ' '); ?> €</span>
                                    <?php else: ?>
                                        <span class="current-price"><?php echo number_format($item['prix'], 2, ',', ' '); ?> €</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-stock">
                                    <?php if ($item['stock'] > 0): ?>
                                        <span class="in-stock">En stock</span>
                                    <?php else: ?>
                                        <span class="out-of-stock">Rupture de stock</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-actions">
                                    <?php if ($item['stock'] > 0): ?>
                                        <button class="add-to-cart" data-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-shopping-cart"></i> Ajouter au panier
                                        </button>
                                    <?php else: ?>
                                        <button class="notify-stock" data-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-bell"></i> M'alerter
                                        </button>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-date">
                                    Ajouté le <?php echo date('d/m/Y', strtotime($item['date_ajout'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="pagination-item">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="pagination-item">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- Modal de confirmation pour vider la liste -->
    <div id="confirm-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Confirmation</h3>
            <p>Êtes-vous sûr de vouloir vider votre liste de favoris ?</p>
            <div class="modal-actions">
                <button id="confirm-clear" class="btn-danger">Confirmer</button>
                <button id="cancel-clear" class="btn-secondary">Annuler</button>
            </div>
        </div>
    </div>
    
    <!-- Pied de page -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/gestion-cart.js"></script>
    <script src="../assets/js/video-background.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/wishlist.js"></script>
</body>
</html>