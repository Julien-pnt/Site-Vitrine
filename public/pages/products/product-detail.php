<?php
// Connexion à la base de données
require_once '../../../php/config/database.php';

// Vérification de l'état de connexion de l'utilisateur
session_start();
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$accountLink = $isLoggedIn ? '../account/dashboard.php' : '../account/login.php';
$accountText = $isLoggedIn ? 'Mon espace' : 'Connexion';

// Récupération de l'ID du produit depuis l'URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$productId) {
    // Rediriger vers la page des montres si aucun ID n'est fourni
    header('Location: ../Montres.html');
    exit();
}

try {
    // Récupération des informations du produit
    $stmt = $pdo->prepare("
        SELECT p.*, c.nom as categorie_nom, col.nom as collection_nom, col.slug as collection_slug
        FROM produits p
        LEFT JOIN categories c ON p.categorie_id = c.id
        LEFT JOIN collections col ON p.collection_id = col.id
        WHERE p.id = ? AND p.visible = TRUE
    ");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        // Produit non trouvé ou non visible
        header('Location: ../Montres.html');
        exit();
    }
    
    // Transformation des images supplémentaires en tableau
    $additionalImages = [];
    if (!empty($product['images_supplementaires'])) {
        $additionalImages = explode(',', $product['images_supplementaires']);
    }
    
    // Formatage du prix
    $formattedPrice = number_format($product['prix'], 2, ',', ' ');
    $hasPromo = !empty($product['prix_promo']) && $product['prix_promo'] < $product['prix'];
    $formattedPromoPrice = $hasPromo ? number_format($product['prix_promo'], 2, ',', ' ') : '';
    
    // Récupération des produits de la même collection (pour les recommandations)
    $stmtRelated = $pdo->prepare("
        SELECT id, nom, slug, prix, prix_promo, image
        FROM produits 
        WHERE collection_id = ? AND visible = TRUE AND id != ?
        ORDER BY RAND()
        LIMIT 4
    ");
    $stmtRelated->execute([$product['collection_id'], $productId]);
    $relatedProducts = $stmtRelated->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // En cas d'erreur
    die("Erreur lors de la récupération des informations du produit: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nom']); ?> - Elixir du Temps</title>
    <meta name="description" content="<?php echo htmlspecialchars($product['description_courte']); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="product">
    <meta property="og:title" content="<?php echo htmlspecialchars($product['nom']); ?> - Elixir du Temps">
    <meta property="og:description" content="<?php echo htmlspecialchars($product['description_courte']); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($product['image']); ?>">
    
    <!-- Ressources -->
    <link rel="stylesheet" href="../../../assets/css/main.css">
    <link rel="shortcut icon" href="../../../assets/img/layout/icon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Style spécifique à la page produit */
        .product-detail {
            display: flex;
            flex-direction: column;
            padding: 4rem 0;
        }
        
        .product-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        @media (max-width: 992px) {
            .product-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
        
        /* Gallery */
        .product-gallery {
            position: relative;
        }
        
        .main-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
            aspect-ratio: 1 / 1;
            background-color: #f8f8f8;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .gallery-thumbnails {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
            gap: 0.5rem;
        }
        
        .gallery-thumbnail {
            width: 80px;
            height: 80px;
            border-radius: 4px;
            cursor: pointer;
            object-fit: cover;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }
        
        .gallery-thumbnail:hover, .gallery-thumbnail.active {
            border-color: #d4af37;
            transform: translateY(-2px);
        }
        
        /* Product info */
        .product-info {
            display: flex;
            flex-direction: column;
        }
        
        .product-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #121212;
        }
        
        .product-reference {
            font-family: 'Raleway', sans-serif;
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .product-price-container {
            display: flex;
            align-items: baseline;
            margin: 1rem 0;
        }
        
        .product-price {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: bold;
            color: #121212;
        }
        
        .price-old {
            font-size: 1.25rem;
            text-decoration: line-through;
            color: #888;
            margin-right: 1rem;
        }
        
        .price-promo {
            color: #d44c4c;
        }
        
        .product-description {
            font-family: 'Raleway', sans-serif;
            line-height: 1.7;
            font-size: 1.05rem;
            color: #333;
            margin: 1.5rem 0;
        }
        
        /* Stock info */
        .stock-indicator {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
            margin: 1rem 0;
        }
        
        .stock-indicator i {
            margin-right: 0.5rem;
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
        
        /* Actions */
        .product-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .product-actions button {
            padding: 0.75rem 1.5rem;
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .add-to-cart-btn {
            background-color: #d4af37;
            color: white;
            border: none;
            flex-grow: 1;
        }
        
        .add-to-cart-btn:hover {
            background-color: #c19b26;
            transform: translateY(-2px);
        }
        
        .add-to-cart-btn.disabled {
            background-color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .add-to-wishlist-btn {
            background-color: white;
            color: #121212;
            border: 1px solid #ddd;
            width: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .add-to-wishlist-btn:hover {
            border-color: #d4af37;
            color: #d4af37;
        }
        
        .add-to-wishlist-btn.active {
            color: #d4af37;
        }

        /* Styles améliorés pour le bouton favoris */
        .add-to-wishlist-btn {
            background-color: #f8f8f8;
            color: #777;
            border: 1px solid #ddd;
            width: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }

        .add-to-wishlist-btn:hover {
            border-color: #d4af37;
            color: #d4af37;
            background-color: #faf7ea;
        }

        .add-to-wishlist-btn.active {
            color: #d4af37;
            background-color: #faf7ea;
            border-color: #d4af37;
        }

        .add-to-wishlist-btn.active svg {
            fill: #d4af37;
        }

        /* Animation du cœur lorsqu'il est ajouté aux favoris */
        @keyframes heartBeat {
            0% { transform: scale(1); }
            15% { transform: scale(1.25); }
            30% { transform: scale(1); }
            45% { transform: scale(1.15); }
            60% { transform: scale(1); }
        }

        .add-to-wishlist-btn.active svg {
            animation: heartBeat 0.8s;
        }

        /* Amélioration du style du cœur */
        .heart-icon {
            transition: transform 0.3s ease, fill 0.3s ease;
            color: #777;
        }

        .add-to-wishlist-btn:hover .heart-icon {
            color: #d4af37;
            transform: scale(1.1);
        }

        .add-to-wishlist-btn.active .heart-icon {
            fill: #d4af37;
            color: #d4af37;
        }

        /* Animation plus prononcée au clic */
        @keyframes heartPulse {
            0% { transform: scale(1); }
            25% { transform: scale(1.3); }
            50% { transform: scale(0.95); }
            75% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .add-to-wishlist-btn:active .heart-icon {
            animation: heartPulse 0.5s ease-in-out;
        }

        /* Correction pour rendre le cœur visible */
        .heart-icon {
            stroke: #333;      /* Couleur du contour plus foncée */
            stroke-width: 2;   /* Contour plus épais */
            width: 24px;       /* Légèrement plus grand */
            height: 24px;      /* Légèrement plus grand */
        }

        /* Assurer que le SVG est bien centré et visible */
        .add-to-wishlist-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0; /* Fond légèrement plus foncé pour contraste */
        }

        /* État actif plus visible */
        .add-to-wishlist-btn.active .heart-icon {
            fill: #d4af37;
            stroke: #d4af37;
        }

        /* Style du bouton favoris avec Font Awesome */
        .add-to-wishlist-btn {
            background-color: #f0f0f0 !important; 
            color: #333 !important;
            border: 1px solid #ddd !important;
            width: 50px !important;
            height: 42px !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            transition: all 0.3s ease !important;
        }

        .add-to-wishlist-btn:hover {
            border-color: #d4af37 !important;
            color: #d4af37 !important;
            background-color: #faf7ea !important;
        }

        .add-to-wishlist-btn.active {
            color: #d4af37 !important;
            background-color: #faf7ea !important;
            border-color: #d4af37 !important;
        }

        .add-to-wishlist-btn i {
            font-size: 18px !important;
        }

        .add-to-wishlist-btn:hover i {
            transform: scale(1.2) !important;
        }

        /* Animation au clic */
        @keyframes heartPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        .add-to-wishlist-btn:active i {
            animation: heartPulse 0.4s ease-in-out !important;
        }

        .add-to-wishlist-btn.active i {
            animation: heartPulse 0.8s !important;
        }

        /* Specs */
        .product-specs {
            background-color: #f9f9f9;
            padding: 3rem 0;
            margin-top: 4rem;
        }
        
        .specs-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .specs-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .spec-item {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .spec-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .spec-label {
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .spec-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            color: #121212;
        }
        
        /* Related */
        .related-products {
            padding: 4rem 0;
        }
        
        .related-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .related-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .related-item {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .related-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .related-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .related-info {
            padding: 1.5rem;
        }
        
        .related-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        
        .related-price {
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            color: #121212;
        }
        
        .view-product {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background-color: #d4af37;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .view-product:hover {
            background-color: #c19b26;
            transform: translateY(-2px);
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .product-container, .specs-container, .related-container {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .specs-container {
            animation-delay: 0.1s;
        }
        
        .related-container {
            animation-delay: 0.2s;
        }
        
        /* Notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 4px;
            font-family: 'Raleway', sans-serif;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-10px);
            animation: notification-show 0.3s forwards;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        @keyframes notification-show {
            to { opacity: 1; transform: translateY(0); }
        }
        
        .notification.success {
            background-color: #28a745;
            color: white;
        }
        
        .notification.error {
            background-color: #dc3545;
            color: white;
        }
        
        .notification.warning {
            background-color: #ffc107;
            color: #212529;
        }

        /* Navigation controls */
        .navigation-controls {
            max-width: 1400px;
            margin: 1rem auto 0;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .back-button, .account-access {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: #333;
            font-family: 'Raleway', sans-serif;
            font-weight: 500;
            transition: all 0.2s ease;
            background-color: #f8f8f8;
            border-radius: 4px;
        }
        
        .back-button:hover, .account-access:hover {
            background-color: #eaeaea;
            transform: translateY(-2px);
        }
        
        .back-button i, .account-access i {
            margin-right: 0.5rem;
            font-size: 16px;
        }
        
        .account-access {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
        }
        
        .account-access:hover {
            background-color: #d4af37;
            color: white;
            border-color: #d4af37;
        }
        
        @media (max-width: 768px) {
            .navigation-controls {
                padding: 0 1rem;
                margin-top: 0.5rem;
            }
        }

        /* Amélioration des boutons principaux */
        .add-to-cart-btn {
            background: linear-gradient(to bottom, #d4af37, #c0992a);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.85rem 1.5rem;
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            flex-grow: 1;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }

        .add-to-cart-btn:hover {
            background: linear-gradient(to bottom, #e0bb4a, #d4af37);
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(212, 175, 55, 0.3);
        }

        .add-to-cart-btn:active {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(212, 175, 55, 0.3);
        }

        .add-to-cart-btn.disabled {
            background: linear-gradient(to bottom, #999, #777);
            cursor: not-allowed;
            opacity: 0.7;
            box-shadow: none;
        }

        /* Style amélioré du bouton favoris */
        .add-to-wishlist-btn {
            background: white !important;
            color: #333 !important;
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            width: 50px !important;
            height: 42px !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05) !important;
        }

        .add-to-wishlist-btn:hover {
            border-color: #d4af37 !important;
            color: #d4af37 !important;
            background-color: #faf7ea !important;
            transform: translateY(-3px) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
        }

        .add-to-wishlist-btn.active {
            color: #d4af37 !important;
            background-color: #faf7ea !important;
            border-color: #d4af37 !important;
        }

        .add-to-wishlist-btn i {
            font-size: 20px !important;
        }

        /* Boutons de navigation améliorés */
        .navigation-controls {
            max-width: 1400px;
            margin: 1rem auto 0;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-button, .account-access {
            display: flex;
            align-items: center;
            padding: 0.65rem 1.2rem;
            text-decoration: none;
            color: #333;
            font-family: 'Raleway', sans-serif;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background-color: white;
            border: 1px solid #eaeaea;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .back-button:hover, .account-access:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .back-button {
            color: #555;
        }

        .back-button:hover {
            background-color: #f8f8f8;
            color: #333;
        }

        .account-access {
            color: #333;
            background: linear-gradient(to bottom, #f8f8f8, #f0f0f0);
            border-color: #ddd;
        }

        .account-access:hover {
            background: linear-gradient(to bottom, #d4af37, #c0992a);
            color: white;
            border-color: #c0992a;
        }

        .back-button i, .account-access i {
            margin-right: 0.6rem;
            font-size: 16px;
            transition: transform 0.2s ease;
        }

        .back-button:hover i {
            transform: translateX(-3px);
        }

        .account-access:hover i {
            transform: scale(1.1);
        }

        /* Amélioration des boutons "Voir le produit" */
        .view-product {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.65rem 1.2rem;
            background: linear-gradient(to bottom, #d4af37, #c0992a);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            text-align: center;
        }

        .view-product:hover {
            background: linear-gradient(to bottom, #e0bb4a, #d4af37);
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(212, 175, 55, 0.3);
        }

        .view-product:active {
            transform: translateY(-1px);
        }

        /* Styles pour les notifications */
        .notifications-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .notification {
            padding: 12px 20px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            color: white;
            font-family: 'Raleway', sans-serif;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .notification.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .notification.success {
            background-color: #28a745;
        }
        
        .notification.error {
            background-color: #dc3545;
        }
        
        .notification.warning {
            background-color: #ffc107;
            color: #333;
        }
        
        .notification.info {
            background-color: #17a2b8;
        }
        
        /* Style pour le compteur de panier */
        .cart-count {
            display: none;
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #d4af37;
            color: white;
            font-size: 12px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            justify-content: center;
            align-items: center;
        }

        /* Styles pour le dropdown du panier */
        .cart-icon {
            position: relative;
            cursor: pointer;
            display: inline-block;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #d4af37;
            color: white;
            font-size: 12px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .cart-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            padding: 1rem;
            display: none;
            z-index: 1000;
        }
        
        .cart-dropdown.show {
            display: block !important;
        }
        
        .cart-dropdown-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .cart-item-image {
            width: 50px;
            height: 50px;
            overflow: hidden;
            border-radius: 4px;
            margin-right: 0.75rem;
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-item-info {
            flex-grow: 1;
        }
        
        .cart-item-info h4 {
            margin: 0;
            font-size: 0.9rem;
        }
        
        .cart-item-price {
            font-size: 0.8rem;
            color: #666;
        }
        
        .cart-item-remove {
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            padding: 0.25rem;
        }
        
        .cart-item-remove:hover {
            color: #d4af37;
        }
        
        .cart-dropdown-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin: 1rem 0;
        }
        
        .cart-dropdown-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .cart-dropdown-button {
            padding: 0.5rem 1rem;
            text-decoration: none;
            text-align: center;
            border-radius: 4px;
            flex-grow: 1;
            font-size: 0.9rem;
        }
        
        .cart-dropdown-button.primary {
            background: linear-gradient(to bottom, #d4af37, #c0992a);
            color: white;
        }
        
        .cart-dropdown-button.secondary {
            background-color: #f5f5f5;
            color: #333;
        }
    </style>
</head>
<body>
    <header class="header">
        <!-- Insérer ici le header de votre site -->
    </header>

    <!-- Ajouter après le header -->
    <div class="cart-container">
        <div class="cart-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span class="cart-badge">0</span>
            
            <div class="cart-dropdown">
                <div class="cart-dropdown-header">
                    <h3>Mon Panier</h3>
                </div>
                <div class="cart-dropdown-items">
                    <!-- Le panier sera rempli dynamiquement via JavaScript -->
                </div>
                <div class="cart-dropdown-empty">Votre panier est vide</div>
                <div class="cart-dropdown-total">
                    <span>Total:</span>
                    <span class="cart-dropdown-total-value">0,00 €</span>
                </div>
                <div class="cart-dropdown-buttons">
                    <a href="../products/panier.php" class="cart-dropdown-button secondary">Voir le panier</a>
                    <a href="../Montres.html" class="cart-dropdown-button primary">Découvrir nos montres</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation controls with back button and account access -->
    <div class="navigation-controls">
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <a href="<?php echo $accountLink; ?>" class="account-access">
            <i class="fas fa-user"></i> <?php echo $accountText; ?>
        </a>
    </div>
    
    <!-- Product detail section -->
    <section class="product-detail">
        <div class="product-container">
            <!-- Product gallery -->
            <div class="product-gallery">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>" class="main-image" id="main-product-image">
                
                <?php if (!empty($additionalImages)): ?>
                <div class="gallery-thumbnails">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>" class="gallery-thumbnail active" onclick="changeImage(this.src)">
                    
                    <?php foreach ($additionalImages as $img): ?>
                    <img src="<?php echo htmlspecialchars(trim($img)); ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>" class="gallery-thumbnail" onclick="changeImage(this.src)">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Product info -->
            <div class="product-info">
                <h1 class="product-title"><?php echo htmlspecialchars($product['nom']); ?></h1>
                <p class="product-reference">Réf. <?php echo htmlspecialchars($product['reference']); ?></p>
                
                <div class="product-price-container">
                    <?php if ($hasPromo): ?>
                        <span class="product-price price-old"><?php echo $formattedPrice; ?> €</span>
                        <span class="product-price price-promo"><?php echo $formattedPromoPrice; ?> €</span>
                    <?php else: ?>
                        <span class="product-price"><?php echo $formattedPrice; ?> €</span>
                    <?php endif; ?>
                </div>
                
                <!-- Stock indicator -->
                <?php
                $stockClass = 'in-stock';
                $stockIcon = 'fa-check-circle';
                $stockMessage = 'En stock';
                $disableAddToCart = false;
                
                if ($product['stock'] <= 0) {
                    $stockClass = 'out-of-stock';
                    $stockIcon = 'fa-times-circle';
                    $stockMessage = 'Rupture de stock';
                    $disableAddToCart = true;
                } elseif ($product['stock'] <= $product['stock_alerte']) {
                    $stockClass = 'low-stock';
                    $stockIcon = 'fa-exclamation-circle';
                    $stockMessage = "Plus que {$product['stock']} en stock";
                }
                ?>
                <div class="stock-indicator <?php echo $stockClass; ?>">
                    <i class="fas <?php echo $stockIcon; ?>"></i> <?php echo $stockMessage; ?>
                </div>
                
                <div class="product-description">
                    <?php echo nl2br(htmlspecialchars($product['description_courte'])); ?>
                </div>
                
                <div class="product-actions">
                    <button class="add-to-cart-btn <?php echo $disableAddToCart ? 'disabled' : ''; ?>" 
                            data-product-id="<?php echo $product['id']; ?>"
                            <?php echo $disableAddToCart ? 'disabled' : ''; ?>>
                        Ajouter au panier
                    </button>
                    <button class="add-to-wishlist-btn" data-product-id="<?php echo $product['id']; ?>" aria-label="Ajouter aux favoris">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Specifications section -->
    <section class="product-specs">
        <div class="specs-container">
            <h2 class="specs-title">Caractéristiques techniques</h2>
            <div class="specs-grid">
                <?php if (!empty($product['categorie_nom'])): ?>
                <div class="spec-item">
                    <p class="spec-label">Catégorie</p>
                    <p class="spec-value"><?php echo htmlspecialchars($product['categorie_nom']); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($product['collection_nom'])): ?>
                <div class="spec-item">
                    <p class="spec-label">Collection</p>
                    <p class="spec-value"><?php echo htmlspecialchars($product['collection_nom']); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($product['poids'])): ?>
                <div class="spec-item">
                    <p class="spec-label">Poids</p>
                    <p class="spec-value"><?php echo number_format($product['poids'], 3, ',', ' '); ?> kg</p>
                </div>
                <?php endif; ?>
                
                <?php if ($product['nouveaute']): ?>
                <div class="spec-item">
                    <p class="spec-label">Statut</p>
                    <p class="spec-value">Nouveauté</p>
                </div>
                <?php endif; ?>
                
                <!-- Description détaillée -->
                <div class="spec-item" style="grid-column: 1 / -1;">
                    <p class="spec-label">Description détaillée</p>
                    <div class="spec-value" style="white-space: pre-line;">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Related products section -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="related-products">
        <div class="related-container">
            <h2 class="related-title">Produits similaires</h2>
            <div class="related-grid">
                <?php foreach ($relatedProducts as $related): ?>
                <div class="related-item">
                    <img src="<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['nom']); ?>" class="related-image">
                    <div class="related-info">
                        <h3 class="related-name"><?php echo htmlspecialchars($related['nom']); ?></h3>
                        <?php 
                        $relatedHasPromo = !empty($related['prix_promo']) && $related['prix_promo'] < $related['prix'];
                        $relatedPrice = number_format($related['prix'], 2, ',', ' ');
                        $relatedPromoPrice = $relatedHasPromo ? number_format($related['prix_promo'], 2, ',', ' ') : '';
                        ?>
                        <p class="related-price">
                            <?php if ($relatedHasPromo): ?>
                                <span class="price-old"><?php echo $relatedPrice; ?> €</span>
                                <span class="price-promo"><?php echo $relatedPromoPrice; ?> €</span>
                            <?php else: ?>
                                <?php echo $relatedPrice; ?> €
                            <?php endif; ?>
                        </p>
                        <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="view-product">Voir le produit</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Footer -->
    <footer class="footer">
        <!-- Insérer ici le footer de votre site -->
    </footer>
    
    <script src="../../../assets/js/cart.js"></script>
    <script>
        // Changer l'image principale quand on clique sur une vignette
        function changeImage(src) {
            document.getElementById('main-product-image').src = src;
            
            // Mettre à jour la classe active
            document.querySelectorAll('.gallery-thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
                if (thumb.src === src) {
                    thumb.classList.add('active');
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter au panier - utilise les fonctions du fichier cart.js
            const addToCartBtn = document.querySelector('.add-to-cart-btn');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function() {
                    if (this.classList.contains('disabled')) return;
                    
                    const productId = this.getAttribute('data-product-id');
                    const productName = document.querySelector('.product-title').textContent;
                    const productImage = document.getElementById('main-product-image').src;
                    
                    // Prix (normal ou promotionnel)
                    let productPrice = <?php echo $hasPromo ? $product['prix_promo'] : $product['prix']; ?>;
                    
                    // Utiliser la fonction checkProductStock de cart.js
                    // Si elle existe, sinon utiliser le fetch local
                    if (typeof checkProductStock === 'function') {
                        checkProductStock(productId, productName, productPrice, productImage);
                    } else {
                        // Vérifier le stock avant d'ajouter au panier
                        fetch(`../../php/api/products/check-stock.php?id=${productId}`)
                            .then(response => response.json())
                            .then(data => handleStockResponse(data, productId, productName, productPrice, productImage))
                            .catch(error => {
                                console.error("Erreur lors de la vérification du stock:", error);
                                if (typeof showNotification === 'function') {
                                    showNotification("Impossible de vérifier le stock. Veuillez réessayer.", 'error');
                                }
                            });
                    }
                });
            }
            
            function handleStockResponse(data, productId, productName, productPrice, productImage) {
                if (data.error) {
                    if (typeof showNotification === 'function') {
                        showNotification(data.error, 'error');
                    }
                    return;
                }
                
                if (data.stock > 0) {
                    // Ajouter au panier avec information de stock
                    if (typeof addToCart === 'function') {
                        addToCart({
                            id: productId,
                            name: productName,
                            price: productPrice,
                            image: productImage,
                            quantity: 1,
                            availableStock: data.stock,
                            stockAlerte: data.stock_alerte || 5
                        });
                        
                        showNotification('Produit ajouté au panier !', 'success');
                    }
                } else {
                    if (typeof showNotification === 'function') {
                        showNotification(`"${productName}" est en rupture de stock`, 'error');
                    }
                }
            }

            // Ajouter aux favoris (juste visuel pour l'instant)
            const wishlistBtn = document.querySelector('.add-to-wishlist-btn');
            if (wishlistBtn) {
                wishlistBtn.addEventListener('click', function() {
                    this.classList.toggle('active');
                    const productName = document.querySelector('.product-title').textContent;
                    if (typeof showNotification === 'function') {
                        showNotification(`"${productName}" ${this.classList.contains('active') ? 'ajouté aux' : 'retiré des'} favoris`, 'success');
                    }
                });
            }
            
            // Initialiser l'affichage du panier
            if (typeof updateCartDisplay === 'function') {
                updateCartDisplay();
            }
            
            // Configuration du dropdown du panier
            const cartIcon = document.querySelector('.cart-icon');
            if (cartIcon) {
                cartIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdown = this.querySelector('.cart-dropdown');
                    if (dropdown) {
                        dropdown.classList.toggle('show');
                    }
                });
                
                document.addEventListener('click', function() {
                    const dropdown = document.querySelector('.cart-dropdown.show');
                    if (dropdown) {
                        dropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>
    <div class="notifications-container"></div>
    <script src="../../assets/js/cart.js" defer></script>
</body>
</html>