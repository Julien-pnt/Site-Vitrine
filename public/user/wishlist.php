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

// Pour le chemin relatif
$relativePath = "..";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes favoris | Elixir du Temps</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/layout/icon2.png" type="image/x-icon">
    
    <!-- Styles du tableau de bord -->
    <link rel="stylesheet" href="assets/css/dashboard-vars.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    
    <style>
        /* Styles spécifiques à la page des favoris */
        .dashboard-header {
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }
        
        .dashboard-header h1 {
            font-family: var(--font-primary);
            font-size: var(--font-size-xl);
            color: var(--secondary-color);
            margin-bottom: var(--spacing-xs);
            font-weight: 600;
        }
        
        .dashboard-header p {
            color: var(--medium-gray);
            font-size: var(--font-size-sm);
        }
        
        /* Actions en haut de la liste */
        .wishlist-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: var(--spacing-lg);
        }
        
        .btn-secondary {
            background-color: var(--light-gray);
            color: var(--dark-gray);
            border: 1px solid var(--border-color);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-sm);
            transition: var(--transition);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }
        
        .btn-secondary i {
            margin-right: var(--spacing-sm);
        }
        
        .btn-secondary:hover {
            background-color: var(--border-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: var(--light-color);
            border: none;
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--radius-sm);
            font-size: var(--font-size-sm);
            transition: var(--transition);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }
        
        .btn-danger:hover {
            background-color: #d32f2f;
            box-shadow: var(--shadow-sm);
        }
        
        /* Grille des favoris */
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .wishlist-item {
            background-color: var(--light-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: var(--transition);
            position: relative;
            animation: fadeIn 0.5s ease forwards;
        }
        
        .wishlist-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: rgba(212, 175, 55, 0.3);
        }
        
        .wishlist-item-image {
            position: relative;
            height: 220px;
            overflow: hidden;
            background-color: var(--light-gray);
        }
        
        .wishlist-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .wishlist-item:hover .wishlist-item-image img {
            transform: scale(1.05);
        }
        
        .remove-from-wishlist {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--dark-gray);
            transition: var(--transition);
            z-index: 1;
        }
        
        .remove-from-wishlist:hover {
            background-color: var(--danger-color);
            color: var(--light-color);
        }
        
        .wishlist-item-details {
            padding: var(--spacing-lg);
        }
        
        .item-title {
            font-family: var(--font-primary);
            font-size: var(--font-size-md);
            margin-bottom: var(--spacing-sm);
            line-height: 1.4;
            height: 2.8em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .item-title a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .item-title a:hover {
            color: var(--primary-color);
        }
        
        .item-reference {
            font-size: var(--font-size-xs);
            color: var(--medium-gray);
            margin-bottom: var(--spacing-sm);
        }
        
        .item-price {
            margin-bottom: var(--spacing-md);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-xs);
        }
        
        .old-price {
            color: var(--medium-gray);
            text-decoration: line-through;
            font-size: var(--font-size-sm);
        }
        
        .current-price {
            color: var(--secondary-color);
            font-weight: 600;
            font-size: var(--font-size-lg);
        }
        
        .item-stock {
            margin-bottom: var(--spacing-md);
            font-size: var(--font-size-xs);
        }
        
        .in-stock {
            color: var(--success-color);
            display: inline-flex;
            align-items: center;
        }
        
        .in-stock::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--success-color);
            margin-right: var(--spacing-xs);
        }
        
        .out-of-stock {
            color: var(--danger-color);
            display: inline-flex;
            align-items: center;
        }
        
        .out-of-stock::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--danger-color);
            margin-right: var(--spacing-xs);
        }
        
        .item-actions {
            margin-bottom: var(--spacing-md);
        }
        
        .add-to-cart, .notify-stock {
            width: 100%;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-sm);
            font-size: var(--font-size-sm);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            gap: var(--spacing-xs);
        }
        
        .add-to-cart {
            background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
            color: var(--light-color);
        }
        
        .add-to-cart:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--secondary-color));
            box-shadow: var(--shadow-sm);
        }
        
        .notify-stock {
            background-color: var(--light-gray);
            color: var(--dark-gray);
            border: 1px solid var(--border-color);
        }
        
        .notify-stock:hover {
            background-color: var(--border-color);
        }
        
        .item-date {
            font-size: var(--font-size-xs);
            color: var(--medium-gray);
            font-style: italic;
        }
        
        /* État vide */
        .empty-state-container {
            background-color: var(--light-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: var(--spacing-xl) var(--spacing-xl);
            text-align: center;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--light-gray);
            margin-bottom: var(--spacing-lg);
            opacity: 0.5;
        }
        
        .empty-state h2 {
            font-family: var(--font-primary);
            font-size: var(--font-size-lg);
            color: var(--secondary-color);
            margin-bottom: var(--spacing-md);
        }
        
        .empty-state p {
            color: var(--medium-gray);
            margin-bottom: var(--spacing-lg);
            max-width: 500px;
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
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: var(--spacing-xs);
            margin-top: var(--spacing-xl);
        }
        
        .pagination-item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: var(--light-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-sm);
            color: var(--dark-gray);
            transition: var(--transition);
        }
        
        .pagination-item:hover {
            background-color: var(--light-gray);
            border-color: var(--medium-gray);
        }
        
        .pagination-item.active {
            background-color: var(--primary-color);
            color: var(--light-color);
            border-color: var(--primary-color);
        }
        
        /* Modal de confirmation */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .modal.show {
            display: block;
            opacity: 1;
        }
        
        .modal-content {
            position: relative;
            background-color: var(--light-color);
            margin: 10% auto;
            padding: var(--spacing-xl);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 500px;
            transform: translateY(-50px);
            transition: transform 0.3s ease;
            animation: modalFadeIn 0.3s forwards;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal.show .modal-content {
            transform: translateY(0);
        }
        
        .close-modal {
            position: absolute;
            top: var(--spacing-md);
            right: var(--spacing-md);
            font-size: var(--font-size-lg);
            color: var(--medium-gray);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .close-modal:hover {
            color: var(--dark-gray);
        }
        
        .modal h3 {
            font-family: var(--font-primary);
            color: var(--secondary-color);
            margin-bottom: var(--spacing-md);
            font-size: var(--font-size-lg);
        }
        
        .modal p {
            margin-bottom: var(--spacing-lg);
            color: var(--dark-gray);
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: var(--spacing-md);
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .wishlist-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .wishlist-grid {
                grid-template-columns: 1fr;
            }
            
            .wishlist-actions {
                justify-content: center;
            }
            
            .modal-content {
                width: 90%;
                margin: 20% auto;
            }
            
            .modal-actions {
                flex-direction: column;
            }
            
            .modal-actions button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <!-- Inclure la sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Main Content -->
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
                    <p>Vous n'avez pas encore ajouté de produits à vos favoris. Parcourez notre collection de montres et ajoutez vos préférées à votre liste.</p>
                    <a href="../pages/Montres.php" class="btn-primary">
                        <i class="fas fa-shopping-bag"></i>&nbsp;Découvrir nos montres
                    </a>
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
                            <a href="../pages/product-details.php?id=<?php echo $item['id']; ?>">
                                <img src="../assets/img/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['nom']); ?>">
                            </a>
                            <button class="remove-from-wishlist" data-id="<?php echo $item['id']; ?>" aria-label="Supprimer des favoris">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="wishlist-item-details">
                            <h3 class="item-title">
                                <a href="../pages/product-details.php?id=<?php echo $item['id']; ?>">
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
        <p>Êtes-vous sûr de vouloir vider votre liste de favoris ? Cette action est irréversible.</p>
        <div class="modal-actions">
            <button id="cancel-clear" class="btn-secondary">Annuler</button>
            <button id="confirm-clear" class="btn-danger">
                <i class="fas fa-trash-alt"></i> Confirmer
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du modal de confirmation
    const modal = document.getElementById('confirm-modal');
    const clearBtn = document.getElementById('clear-wishlist');
    const confirmClearBtn = document.getElementById('confirm-clear');
    const cancelClearBtn = document.getElementById('cancel-clear');
    const closeModal = document.querySelector('.close-modal');
    
    // Fonction pour ouvrir le modal
    function openModal() {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    // Fonction pour fermer le modal
    function closeModalFunc() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // Ouvrir le modal lorsqu'on clique sur "Vider ma liste"
    if (clearBtn) {
        clearBtn.addEventListener('click', openModal);
    }
    
    // Fermer le modal lorsqu'on clique sur "Annuler"
    if (cancelClearBtn) {
        cancelClearBtn.addEventListener('click', closeModalFunc);
    }
    
    // Fermer le modal lorsqu'on clique sur la croix
    if (closeModal) {
        closeModal.addEventListener('click', closeModalFunc);
    }
    
    // Vider la liste lorsqu'on clique sur "Confirmer"
    if (confirmClearBtn) {
        confirmClearBtn.addEventListener('click', function() {
            // Envoyer la requête pour vider la liste
            fetch('../php/api/wishlist/clear_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ user_id: <?php echo $userId; ?> })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Rediriger vers la même page pour afficher la liste vide
                    window.location.reload();
                } else {
                    alert('Une erreur est survenue lors de la suppression de vos favoris.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de la suppression de vos favoris.');
            });
            
            closeModalFunc();
        });
    }
    
    // Supprimer un produit des favoris
    const removeButtons = document.querySelectorAll('.remove-from-wishlist');
    if (removeButtons) {
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                
                fetch('../php/api/wishlist/remove_from_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        user_id: <?php echo $userId; ?>,
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Animer la suppression de l'élément
                        const wishlistItem = this.closest('.wishlist-item');
                        wishlistItem.style.opacity = '0';
                        setTimeout(() => {
                            wishlistItem.remove();
                            
                            // Si la liste est vide, recharger la page
                            if (document.querySelectorAll('.wishlist-item').length === 0) {
                                window.location.reload();
                            }
                        }, 300);
                    } else {
                        alert('Une erreur est survenue lors de la suppression du produit de vos favoris.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de la suppression du produit de vos favoris.');
                });
            });
        });
    }
    
    // Ajouter un produit au panier
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    if (addToCartButtons) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                
                fetch('../php/api/cart/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        product_id: productId,
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Changer le texte du bouton temporairement
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check"></i> Ajouté au panier';
                        this.disabled = true;
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }, 2000);
                    } else {
                        alert(data.message || 'Une erreur est survenue lors de l\'ajout au panier.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de l\'ajout au panier.');
                });
            });
        });
    }
});
</script>

</body>
</html>