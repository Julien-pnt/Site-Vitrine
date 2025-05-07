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

// Fonction pour générer l'indicateur de stock
function generateStockIndicator($productId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT stock, stock_alerte FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $stockInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$stockInfo) {
        return '<div class="stock-indicator out-of-stock"><i class="fas fa-times-circle"></i> Indisponible</div>';
    }
    
    $stock = $stockInfo['stock'];
    $stockAlerte = $stockInfo['stock_alerte'];
    
    if ($stock <= 0) {
        return '<div class="stock-indicator out-of-stock"><i class="fas fa-times-circle"></i> Rupture de stock</div>';
    } elseif ($stock <= $stockAlerte) {
        return '<div class="stock-indicator low-stock"><i class="fas fa-exclamation-circle"></i> Stock limité</div>';
    } else {
        return '<div class="stock-indicator in-stock"><i class="fas fa-check-circle"></i> En stock</div>';
    }
}

// Fonction pour vérifier si un produit est disponible
function isProductAvailable($productId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT stock FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $stock = $stmt->fetchColumn();
    
    return ($stock > 0);
}

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

        /* AMÉLIORATIONS DE LA LISTE DE SOUHAITS */

        /* Style amélioré pour la liste avec produits */
        .wishlist-items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Correction pour la section vide */
        .wishlist-items {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
        }

        /* Animation d'entrée progressive des éléments */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatAnimation {
            0% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0); }
        }

        /* Style amélioré des cartes produit */
        .wishlist-item {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin-bottom: 20px;
        }

        .wishlist-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
            border-color: rgba(212, 175, 55, 0.3);
        }

        .wishlist-item-image {
            position: relative;
            height: 240px;
            overflow: hidden;
            background: linear-gradient(to right, #f9f9f9, #f1f1f1);
        }

        .wishlist-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s cubic-bezier(0.19, 1, 0.22, 1);
        }

        .wishlist-item:hover .wishlist-item-image img {
            transform: scale(1.07);
        }

        .wishlist-item-details {
            padding: 22px;
        }

        .item-title {
            font-family: var(--font-primary);
            font-size: 1.15rem;
            margin-bottom: 15px;
            line-height: 1.4;
            font-weight: 600;
        }

        .item-title a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .item-title a:hover {
            color: var(--primary-color);
        }

        .item-price {
            font-family: var(--font-primary);
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 16px;
        }

        .item-meta {
            margin-top: 15px;
            margin-bottom: 20px;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }

        .date-added {
            font-size: 0.85rem;
            color: #777;
            font-style: italic;
        }

        /* Amélioration de l'état vide */
        .empty-wishlist {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 60px 30px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            border: 1px solid var(--border-color);
            animation: fadeIn 0.8s ease-out forwards;
        }

        .empty-wishlist i,
        .empty-wishlist h2,
        .empty-wishlist p,
        .empty-wishlist a {
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .empty-wishlist h2 {
            text-align: center;
            margin: 0 auto 15px;
            color: var(--secondary-color);
            font-family: var(--font-primary);
        }

        .empty-wishlist p {
            font-size: 1.2rem;
            color: var(--dark-gray);
            margin: 0 auto 30px;
            line-height: 1.6;
            text-align: center;
            max-width: 90%;
        }

        .empty-wishlist i {
            font-size: 5rem;
            color: rgba(212, 175, 55, 0.3);
            margin-bottom: 25px;
            display: block;
            animation: floatAnimation 3s ease-in-out infinite;
        }

        .empty-wishlist .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
        }

        .empty-wishlist .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
        }

        .empty-wishlist .btn-primary i {
            font-size: 1rem;
            margin: 0;
            animation: none;
        }

        /* Style pour les images manquantes */
        .no-image {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f5f5, #eeeeee);
        }

        .no-image i {
            font-size: 3rem;
            color: rgba(0, 0, 0, 0.1);
        }

        /* Actions pour la liste */
        .wishlist-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .wishlist-count {
            font-size: 0.95rem;
            color: var(--medium-gray);
        }

        .wishlist-count strong {
            color: var(--secondary-color);
        }

        .wishlist-buttons {
            display: flex;
            gap: 10px;
        }

        /* ================ STYLE AMÉLIORÉ DE LA MODAL ================ */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            justify-content: center;
            align-items: center;
            overflow: auto;
        }

        .modal.show {
            display: flex;
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 0;
            position: relative;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .modal-content h3 {
            padding: 20px 30px;
            margin: 0;
            font-family: var(--font-primary);
            background-color: #f8f8f8;
            border-bottom: 1px solid #eaeaea;
            color: var(--secondary-color);
            font-size: 1.4rem;
            font-weight: 600;
        }

        .modal-content p {
            padding: 30px;
            margin: 0;
            font-size: 1.1rem;
            line-height: 1.6;
            color: var(--dark-gray);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            padding: 20px 30px;
            background-color: #f8f8f8;
            border-top: 1px solid #eaeaea;
            gap: 15px;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            color: #888;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .close-modal:hover {
            color: var(--danger-color);
        }

        /* ================ STYLE AMÉLIORÉ DES BOUTONS ================ */
        .btn-danger {
            background: linear-gradient(to bottom, #e74c3c, #c0392b);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(231, 76, 60, 0.2);
            font-size: 0.9rem;
        }

        .btn-danger:hover {
            background: linear-gradient(to bottom, #c0392b, #a93226);
            box-shadow: 0 6px 10px rgba(231, 76, 60, 0.3);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #ddd;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        /* Force certains styles spécifiques pour corriger les problèmes */
        #confirm-modal.modal {
            display: none !important; /* Pour éviter les conflits de style */
        }

        #confirm-modal.modal.show {
            display: flex !important;
        }

        /* Ajustement pour le responsive */
        @media (max-width: 576px) {
            .modal-content {
                width: 95%;
                margin: 10px;
            }
            
            .modal-actions {
                flex-direction: column;
            }
            
            .modal-actions button {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        /* Responsive fixes */
        @media (max-width: 992px) {
            .wishlist-items {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .wishlist-items {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .empty-wishlist {
                padding: 40px 20px;
            }
            
            .wishlist-actions {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .wishlist-buttons {
                width: 100%;
            }
            
            .wishlist-buttons button {
                flex: 1;
            }
        }

        /* Animation pour les cartes */
        .wishlist-item {
            animation: fadeIn 0.5s ease forwards;
            animation-delay: calc(var(--order) * 0.1s);
            opacity: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Indicateurs de stock */
        .stock-indicator {
            margin: 10px 0;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .stock-indicator i {
            margin-right: 6px;
        }

        .in-stock {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .low-stock {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .out-of-stock {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .add-to-cart-btn.disabled {
            opacity: 0.7;
            cursor: not-allowed;
            background: linear-gradient(to bottom, #6c757d, #5a6268) !important;
            box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2) !important;
        }

        .add-to-cart-btn.disabled:hover {
            transform: none;
            box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2) !important;
        }

        .stock-warning {
            color: #ffc107;
            font-style: italic;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        /* Amélioration du bouton Ajouter au panier */
        .add-to-cart-btn {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary-color), #c0a02c);
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .add-to-cart-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255,255,255,0.2), rgba(255,255,255,0));
            transition: left 0.5s ease;
            z-index: -1;
        }

        .add-to-cart-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.35);
            background: linear-gradient(135deg, #d4b347, var(--primary-color));
        }

        .add-to-cart-btn:hover:before {
            left: 100%;
        }

        .add-to-cart-btn:active {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }

        .add-to-cart-btn i {
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .add-to-cart-btn:hover i {
            transform: translateX(-3px);
        }

        /* Animation de succès */
        .add-to-cart-btn.success-animation {
            background: linear-gradient(135deg, #28a745, #1e7e34) !important;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.25) !important;
        }

        /* Style pour le bouton pendant le chargement */
        .add-to-cart-btn .fa-spinner {
            animation: spin 1.2s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Animation de vague pour l'effet d'ajout */
        .add-to-cart-btn:after {
            content: '';
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

        .add-to-cart-btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            20% {
                transform: scale(25, 25);
                opacity: 0.3;
            }
            100% {
                opacity: 0;
                transform: scale(40, 40);
            }
        }

        /* Style amélioré pour le bouton de suppression */
        .remove-from-wishlist {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #dc3545;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 10;
            opacity: 0;
            transform: scale(0.8) translateY(-5px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        .wishlist-item:hover .remove-from-wishlist {
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        .remove-from-wishlist i {
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .remove-from-wishlist:hover {
            background-color: #dc3545;
            color: white;
            box-shadow: 0 5px 12px rgba(220, 53, 69, 0.3);
            transform: scale(1.1);
        }

        .remove-from-wishlist:active {
            transform: scale(0.95);
        }

        /* Animation de suppression améliorée */
        @keyframes removeItem {
            0% {
                opacity: 1;
                transform: scale(1);
            }
            20% {
                opacity: 1;
                transform: scale(1.05);
            }
            100% {
                opacity: 0;
                transform: translateX(30px);
                height: 0;
                margin: 0;
                padding: 0;
            }
        }

        .wishlist-item.removing {
            animation: removeItem 0.5s ease forwards;
            overflow: hidden;
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
        
        <?php if ($wishlistItems && count($wishlistItems) > 0): ?>
            <div class="wishlist-actions">
                <div class="wishlist-count">
                    <strong><?php echo count($wishlistItems); ?></strong> article(s) dans votre liste de favoris
                </div>
                <div class="wishlist-buttons">
                    <button id="clear-wishlist" class="btn-danger">
                        <i class="fas fa-trash-alt"></i> Vider ma liste
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <div class="wishlist-items">
            <?php if ($wishlistItems && count($wishlistItems) > 0): ?>
                <?php foreach ($wishlistItems as $item): ?>
                    <div class="wishlist-item" data-product-id="<?php echo $item['id']; ?>">
                        <div class="wishlist-item-image">
                            <a href="../pages/products/product-detail.php?id=<?php echo $item['id']; ?>">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="../uploads/product/<?php echo htmlspecialchars(basename($item['image'])); ?>" 
                                         alt="<?php echo htmlspecialchars($item['nom']); ?>" 
                                         class="product-image" loading="lazy">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <button class="remove-from-wishlist" data-id="<?php echo $item['id']; ?>" aria-label="Supprimer des favoris">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="wishlist-item-details">
                            <h3 class="item-title">
                                <a href="../pages/products/product-detail.php?id=<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['nom']); ?>
                                </a>
                            </h3>
                            
                            <?php if (!empty($item['prix_promo'])): ?>
                                <p class="item-price">
                                    <span class="price-old"><?php echo number_format($item['prix'], 0, ',', ' '); ?> €</span> 
                                    <?php echo number_format($item['prix_promo'], 0, ',', ' '); ?> €
                                </p>
                            <?php else: ?>
                                <p class="item-price"><?php echo number_format($item['prix'], 0, ',', ' '); ?> €</p>
                            <?php endif; ?>
                            
                            <div class="item-meta">
                                <span class="date-added">Ajouté le <?php echo date('d/m/Y', strtotime($item['date_ajout'])); ?></span>
                            </div>

                            <?php echo generateStockIndicator($item['id']); ?>

                            <div class="item-actions">
                                <?php if (isProductAvailable($item['id'])): ?>
                                    <button class="add-to-cart-btn" data-product-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-shopping-cart"></i> Ajouter au panier
                                    </button>
                                <?php else: ?>
                                    <button class="add-to-cart-btn disabled" disabled>
                                        <i class="fas fa-times-circle"></i> Indisponible
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-wishlist">
                    <i class="far fa-heart"></i>
                    <h2>Votre liste de favoris est vide</h2>
                    <p>Vous n'avez pas encore ajouté d'articles à votre liste de favoris. Parcourez notre catalogue et ajoutez vos coups de cœur !</p>
                    <a href="../pages/products/Montres.php" class="btn-primary">
                        <i class="fas fa-search"></i> Explorer nos montres
                    </a>
                </div>
            <?php endif; ?>
        </div>
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
    // Ajoutez un délai d'animation pour chaque élément de la liste
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    if (wishlistItems) {
        wishlistItems.forEach((item, index) => {
            item.style.setProperty('--order', index);
        });
    }

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
            fetch('../../php/api/wishlist/clear_wishlist.php', {
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
                
                fetch('../../php/api/wishlist/remove_from_wishlist.php', {
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
                        wishlistItem.classList.add('removing');
                        
                        setTimeout(() => {
                            wishlistItem.remove();
                            
                            // Si la liste est vide, recharger la page
                            if (document.querySelectorAll('.wishlist-item').length === 0) {
                                window.location.reload();
                            }
                        }, 500);
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
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn:not(.disabled)');
    if (addToCartButtons) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                
                // Ajouter une classe pour l'animation de chargement
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout en cours...';
                this.disabled = true;
                
                fetch('../../php/api/cart/add_to_cart.php', {
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
                        // Animation de succès
                        this.innerHTML = '<i class="fas fa-check"></i> Ajouté au panier';
                        this.classList.add('success-animation');
                        
                        setTimeout(() => {
                            this.innerHTML = '<i class="fas fa-shopping-cart"></i> Ajouter au panier';
                            this.disabled = false;
                            this.classList.remove('success-animation');
                        }, 2000);
                    } else {
                        // Animation d'échec
                        this.innerHTML = '<i class="fas fa-exclamation-circle"></i> Erreur';
                        
                        setTimeout(() => {
                            this.innerHTML = '<i class="fas fa-shopping-cart"></i> Ajouter au panier';
                            this.disabled = false;
                        }, 2000);
                        
                        alert(data.message || 'Une erreur est survenue lors de l\'ajout au panier.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.innerHTML = '<i class="fas fa-exclamation-circle"></i> Erreur';
                    
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-shopping-cart"></i> Ajouter au panier';
                        this.disabled = false;
                    }, 2000);
                    
                    alert('Une erreur est survenue lors de l\'ajout au panier.');
                });
            });
        });
    }
});
</script>

</body>
</html>