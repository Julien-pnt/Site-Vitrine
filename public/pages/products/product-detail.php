<?php
// Connexion à la base de données
require_once '../../../php/config/database.php';

// Vérification de l'état de connexion de l'utilisateur
session_start();

// DEBUG - Vérification de la session (à retirer une fois le problème résolu)
if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté, on vérifie le bon fonctionnement des sessions
    $_SESSION['test_session'] = true;
}

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$accountLink = $isLoggedIn ? '../../user/index.php' : '../auth/login.php';
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
    
    // Récupération des avis approuvés pour ce produit
    $stmtAvis = $pdo->prepare("
        SELECT a.*, u.nom as utilisateur_nom, u.prenom as utilisateur_prenom
        FROM avis a
        JOIN utilisateurs u ON a.utilisateur_id = u.id
        WHERE a.produit_id = ? AND a.statut = 'approuve'
        ORDER BY a.date_creation DESC
    ");
    $stmtAvis->execute([$productId]);
    $avis = $stmtAvis->fetchAll(PDO::FETCH_ASSOC);

    // Calcul de la note moyenne
    $noteMoyenne = 0;
    $nbAvis = count($avis);
    if ($nbAvis > 0) {
        $sommeNotes = 0;
        foreach ($avis as $unAvis) {
            $sommeNotes += $unAvis['note'];
        }
        $noteMoyenne = round($sommeNotes / $nbAvis, 1);
    }
    
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
            margin-bottom: 2rem;
        }
        
        .main-image {
            width: 100%;
            height: auto;
            border-radius: 12px;
            transition: all 0.5s ease;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .gallery-thumbnails {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
            gap: 0.8rem;
        }
        
        .gallery-thumbnail {
            width: 90px;
            height: 90px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        
        .gallery-thumbnail:hover {
            border-color: #d4af37;
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 8px 15px rgba(212, 175, 55, 0.2);
        }
        
        .gallery-thumbnail.active {
            border-color: #d4af37;
            box-shadow: 0 8px 15px rgba(212, 175, 55, 0.3);
        }
        
        /* Style pour l'affichage des images manquantes dans la galerie principale */
        .product-gallery .no-image {
            width: 100%;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .product-gallery .no-image i {
            font-size: 4rem;
            color: #dee2e6;
        }

        /* Product info */
        .product-info {
            display: flex;
            flex-direction: column;
        }
        
        .product-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: #111;
            line-height: 1.2;
            letter-spacing: 0.5px;
        }
        
        .product-reference {
            font-family: 'Raleway', sans-serif;
            font-size: 1rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 2rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }
        
        .product-price-container {
            display: flex;
            align-items: baseline;
            margin: 1.5rem 0;
        }
        
        .product-price {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        .price-old {
            font-size: 1.3rem;
            text-decoration: line-through;
            color: #999;
            margin-right: 1rem;
        }
        
        .price-promo {
            color: #c0392b;
            position: relative;
        }
        
        .price-promo::after {
            content: '';
            display: block;
            width: 30px;
            height: 2px;
            background-color: #c0392b;
            margin-top: 5px;
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
            background: linear-gradient(135deg, #d4af37, #a17b10);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 1rem 2rem;
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            flex-grow: 1;
        }
        
        .add-to-cart-btn:hover {
            background: linear-gradient(135deg, #e5c458, #d4af37);
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
        }
        
        .add-to-cart-btn:active {
            transform: translateY(-2px);
        }
        
        .add-to-cart-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }
        
        .add-to-cart-btn:hover::after {
            transform: translateX(100%);
        }
        
        .add-to-cart-btn.disabled {
            background: linear-gradient(to bottom, #999, #777);
            cursor: not-allowed;
            opacity: 0.7;
            box-shadow: none;
        }
        
        .add-to-wishlist-btn {
            width: 56px !important;
            height: 56px !important;
            border-radius: 50% !important;
            background: white !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        }
        
        .add-to-wishlist-btn:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 8px 15px rgba(0,0,0,0.15) !important;
        }
        
        .add-to-wishlist-btn i {
            font-size: 20px !important;
            color: #444 !important;
            transition: transform 0.3s ease, color 0.3s ease !important;
        }
        
        .add-to-wishlist-btn:hover i {
            color: #d4af37 !important;
            transform: scale(1.2) !important;
        }
        
        .add-to-wishlist-btn.active i {
            color: #d4af37 !important;
        }

        /* Amélioration du bouton favoris pour inclure le texte */
        .add-to-wishlist-btn {
            width: auto !important;
            padding: 0 15px !important;
            height: 42px !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .wishlist-text {
            display: none;
        }

        /* Afficher le texte seulement en mobile quand le bouton est pleine largeur */
        @media (max-width: 576px) {
            .wishlist-text {
                display: inline-block;
                font-size: 14px;
            }
        }

        .add-to-wishlist-btn.active i {
            color: #d4af37 !important;
        }

        /* Specs */
        .product-specs {
            background-color: #f5f5f5;
            padding: 4rem 0;
            margin-top: 5rem;
            position: relative;
        }

        .product-specs::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: #d4af37;
        }
        
        .specs-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .specs-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 2.5rem;
            text-align: center;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .specs-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: #d4af37;
        }
        
        .specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .spec-item {
            background-color: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            transition: all 0.4s ease;
            border-bottom: 3px solid transparent;
        }
        
        .spec-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-bottom-color: #d4af37;
        }
        
        .spec-label {
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .spec-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: #222;
            line-height: 1.6;
        }
        
        /* Related */
        .related-products {
            padding: 5rem 0;
            background-color: #f9f9f9;
        }
        
        .related-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .related-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
            padding-bottom: 0.8rem;
        }

        .related-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: #d4af37;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2.5rem;
        }
        
        .related-item {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            position: relative;
        }

        .related-item::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: #d4af37;
            transition: width 0.3s ease;
        }
        
        .related-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .related-item:hover::before {
            width: 100%;
        }
        
        .related-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .related-item:hover .related-image {
            transform: scale(1.05);
        }
        
        .related-info {
            padding: 1.8rem;
        }
        
        .related-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            margin-bottom: 0.8rem;
            font-weight: 700;
            color: #111;
        }
        
        .related-price {
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 1.2rem;
        }
        
        .view-product {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, #d4af37, #a17b10);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-transform: uppercase;
        }
        
        .view-product:hover {
            background: linear-gradient(135deg, #e5c458, #d4af37);
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(212, 175, 55, 0.3);
        }

        /* Style pour les images manquantes dans les produits similaires */
        .related-item .no-image {
            width: 100%;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .related-item .no-image i {
            font-size: 3rem;
            color: #dee2e6;
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
            margin: 2rem auto 1rem;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .back-button, .account-access {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            text-decoration: none;
            color: #333;
            font-family: 'Raleway', sans-serif;
            font-weight: 500;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            background-color: #f8f8f8;
            border-radius: 30px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        }
        
        .back-button:hover, .account-access:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
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

        /* ================ STYLE DES AVIS CLIENTS ================ */
        .product-reviews {
            padding: 5rem 0;
            background-color: #fcfcfc;
            position: relative;
        }

        .product-reviews::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: var(--color-gold);
        }

        .reviews-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .reviews-title {
            font-family: var(--font-heading);
            font-size: 2.2rem;
            margin-bottom: 2.5rem;
            text-align: center;
            position: relative;
            padding-bottom: 0.8rem;
        }

        .reviews-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: var(--color-gold);
        }

        /* Section résumé des avis */
        .reviews-summary {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .average-rating {
            background-color: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            text-align: center;
            min-width: 280px;
            transition: transform 0.3s ease;
        }

        .average-rating:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .rating-number {
            font-family: var(--font-heading);
            font-size: 3rem;
            font-weight: 700;
            color: var(--color-dark);
            margin-bottom: 0.5rem;
        }

        .rating-stars {
            margin-bottom: 0.8rem;
        }

        .rating-stars i {
            color: var(--color-gold);
            font-size: 1.3rem;
            margin: 0 2px;
        }

        .rating-count {
            font-family: var(--font-primary);
            font-size: 0.95rem;
            color: var(--color-text-light);
        }

        /* Liste des avis */
        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .review-item {
            background-color: white;
            border-radius: 12px;
            padding: 1.8rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 3px solid transparent;
        }

        .review-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border-left-color: var(--color-gold);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
        }

        .review-author {
            font-weight: 600;
            color: var(--color-dark);
            font-size: 1.05rem;
        }

        .review-date {
            color: var(--color-text-light);
            font-size: 0.9rem;
        }

        .review-rating {
            margin-bottom: 1rem;
        }

        .review-rating i {
            color: var(--color-gold);
            font-size: 1rem;
            margin-right: 2px;
        }

        .review-content {
            font-family: var(--font-primary);
            color: var(--color-text);
            line-height: 1.7;
            font-size: 1rem;
        }

        /* Message quand il n'y a pas d'avis */
        .no-reviews {
            text-align: center;
            background-color: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.04);
            margin-bottom: 3rem;
        }

        .no-reviews p {
            color: var(--color-text-light);
            font-size: 1.1rem;
            font-style: italic;
        }

        /* Formulaire d'avis */
        .review-form-container {
            background-color: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            margin-top: 2rem;
        }

        .review-form-container h3 {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--color-dark);
            text-align: center;
            position: relative;
            padding-bottom: 0.8rem;
        }

        .review-form-container h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: var(--color-gold);
        }

        #review-form .form-group {
            margin-bottom: 1.5rem;
        }

        #review-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--color-dark);
            font-size: 0.95rem;
        }

        .rating-select {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .rating-star {
            font-size: 1.5rem;
            cursor: pointer;
            color: #ddd;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .rating-star:hover, .rating-star.active {
            color: var(--color-gold);
            transform: scale(1.15);
        }

        #review-form textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--color-gray-300);
            border-radius: 8px;
            resize: vertical;
            font-family: var(--font-primary);
            transition: border-color 0.3s ease;
        }

        #review-form textarea:focus {
            outline: none;
            border-color: var(--color-gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }

        .form-actions {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .submit-review-btn {
            background: linear-gradient(135deg, #d4af37, #a17b10);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 0.8rem 2rem;
            font-family: var(--font-primary);
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .submit-review-btn:hover {
            background: linear-gradient(135deg, #e5c458, #d4af37);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
        }

        /* Message pour se connecter */
        .login-to-review {
            text-align: center;
            background-color: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.04);
            margin-top: 2rem;
        }

        .login-to-review p {
            font-size: 1.05rem;
            color: var(--color-text);
        }

        .login-to-review a {
            color: var(--color-gold);
            font-weight: 600;
            text-decoration: underline;
            transition: color 0.2s ease;
        }

        .login-to-review a:hover {
            color: var(--color-gold-dark);
        }

        /* Adaptations mobiles */
        @media (max-width: 768px) {
            .rating-number {
                font-size: 2.5rem;
            }
            
            .review-form-container {
                padding: 1.5rem;
            }
            
            .reviews-container {
                padding: 0 1rem;
            }
            
            .review-item {
                padding: 1.2rem;
            }
            
            .review-header {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .average-rating {
                padding: 1.5rem;
                min-width: auto;
                width: 100%;
            }
            
            .rating-stars i {
                font-size: 1.1rem;
            }
            
            .rating-select {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <!-- Insérer ici le header de votre site -->
    </header>

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
                <?php if (!empty($product['image'])): ?>
                    <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($product['image'])); ?>" 
                         alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                         class="main-image" 
                         id="main-product-image">
                <?php else: ?>
                    <div class="no-image"><i class="fas fa-image"></i></div>
                <?php endif; ?>
                
                <?php if (!empty($additionalImages)): ?>
                <div class="gallery-thumbnails">
                    <?php if (!empty($product['image'])): ?>
                    <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($product['image'])); ?>" 
                         alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                         class="gallery-thumbnail active" 
                         onclick="changeImage(this.src)">
                    <?php endif; ?>
                    
                    <?php foreach ($additionalImages as $img): ?>
                        <?php if (!empty($img)): ?>
                        <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename(trim($img))); ?>" 
                             alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                             class="gallery-thumbnail" 
                             onclick="changeImage(this.src)">
                        <?php endif; ?>
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
                    <button class="add-to-wishlist-btn toggle-wishlist" data-product-id="<?php echo $product['id']; ?>" aria-label="Ajouter aux favoris">
                        <i class="fas fa-heart"></i> 
                        <span class="wishlist-text">Ajouter aux favoris</span>
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
                    <?php if (!empty($related['image'])): ?>
                        <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($related['image'])); ?>" 
                             alt="<?php echo htmlspecialchars($related['nom']); ?>" 
                             class="related-image">
                    <?php else: ?>
                        <div class="no-image"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
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
    
    <!-- Section des avis -->
    <section class="product-reviews">
        <div class="reviews-container">
            <h2 class="reviews-title">Avis clients</h2>
            
            <div class="reviews-summary">
                <div class="average-rating">
                    <div class="rating-number"><?php echo $noteMoyenne; ?>/5</div>
                    <div class="rating-stars">
                        <?php
                        // Afficher les étoiles en fonction de la note moyenne
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $noteMoyenne) {
                                echo '<i class="fas fa-star"></i>';
                            } elseif ($i - 0.5 <= $noteMoyenne) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    <div class="rating-count"><?php echo $nbAvis; ?> avis</div>
                </div>
            </div>
            
            <?php if ($nbAvis > 0): ?>
            <div class="reviews-list">
                <?php foreach ($avis as $unAvis): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="review-author">
                            <?php echo htmlspecialchars($unAvis['utilisateur_prenom'] . ' ' . substr($unAvis['utilisateur_nom'], 0, 1) . '.'); ?>
                        </div>
                        <div class="review-date">
                            <?php echo date('d/m/Y', strtotime($unAvis['date_creation'])); ?>
                        </div>
                    </div>
                    <div class="review-rating">
                        <?php
                        // Afficher les étoiles en fonction de la note
                        for ($i = 1; $i <= 5; $i++) {
                            echo ($i <= $unAvis['note']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                        }
                        ?>
                    </div>
                    <div class="review-content">
                        <?php echo nl2br(htmlspecialchars($unAvis['commentaire'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="no-reviews">
                <p>Aucun avis pour ce produit pour le moment.</p>
            </div>
            <?php endif; ?>
            
            <?php if ($isLoggedIn): ?>
            <div class="review-form-container">
                <h3>Donnez votre avis</h3>
                <form id="review-form" action="../../../php/api/reviews/add-review.php" method="post">
                    <input type="hidden" name="produit_id" value="<?php echo $productId; ?>">
                    
                    <div class="form-group">
                        <label for="note">Note</label>
                        <div class="rating-select">
                            <i class="far fa-star rating-star" data-value="1"></i>
                            <i class="far fa-star rating-star" data-value="2"></i>
                            <i class="far fa-star rating-star" data-value="3"></i>
                            <i class="far fa-star rating-star" data-value="4"></i>
                            <i class="far fa-star rating-star" data-value="5"></i>
                            <input type="hidden" name="note" id="note" value="">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="commentaire">Commentaire</label>
                        <textarea name="commentaire" id="commentaire" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="submit-review-btn">Envoyer mon avis</button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="login-to-review">
                <p>Vous devez être <a href="<?php echo $accountLink; ?>">connecté</a> pour laisser un avis.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <!-- Insérer ici le footer de votre site -->
    </footer>
    
    <script src="../../../assets/js/cart.js"></script>
    <script>
        // Changer l'image principale quand on clique sur une vignette
        function changeImage(src) {
            const mainImage = document.getElementById('main-product-image');
            if (mainImage) {
                mainImage.src = src;
            }
            
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
        });
    </script>
    <script>
// Remplacer le code existant de gestion des favoris par celui-ci

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des favoris avec API backend
    const toggleWishlistBtn = document.querySelector('.toggle-wishlist');
    if (toggleWishlistBtn) {
        const productId = toggleWishlistBtn.getAttribute('data-product-id');
        const productName = document.querySelector('.product-title').textContent;
        
        // Vérifier si le produit est déjà en favoris
        fetch('/Site-Vitrine/php/api/wishlist/manage.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=check&product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.is_favorite) {
                toggleWishlistBtn.classList.add('active');
                toggleWishlistBtn.querySelector('span').textContent = 'Retirer des favoris';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la vérification des favoris:', error);
        });
        
        // Gérer le clic sur le bouton
        toggleWishlistBtn.addEventListener('click', function() {
            if (!<?php echo $isLoggedIn ? 'true' : 'false'; ?>) {
                // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
                window.location.href = '../auth/login.html?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            const isActive = this.classList.contains('active');
            const action = isActive ? 'remove' : 'add';
            
            fetch('/Site-Vitrine/php/api/wishlist/manage.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${action}&product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (action === 'add') {
                        this.classList.add('active');
                        this.querySelector('span').textContent = 'Retirer des favoris';
                        if (typeof showNotification === 'function') {
                            showNotification(`"${productName}" ajouté aux favoris`, 'success');
                        }
                    } else {
                        this.classList.remove('active');
                        this.querySelector('span').textContent = 'Ajouter aux favoris';
                        if (typeof showNotification === 'function') {
                            showNotification(`"${productName}" retiré des favoris`, 'success');
                        }
                    }
                } else {
                    if (typeof showNotification === 'function') {
                        showNotification(data.message || 'Une erreur est survenue', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                if (typeof showNotification === 'function') {
                    showNotification('Une erreur est survenue lors de la communication avec le serveur', 'error');
                }
            });
        });
    }
});
</script>
    <script>
// Correction spécifique pour le dropdown du panier
document.addEventListener('DOMContentLoaded', function() {
    // Configuration du dropdown du panier
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        // Supprimer tous les gestionnaires d'événements existants
        const newCartIcon = cartIcon.cloneNode(true);
        cartIcon.parentNode.replaceChild(newCartIcon, cartIcon);
        
        // Ajouter un nouveau gestionnaire
        newCartIcon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdown = this.querySelector('.cart-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        });
        
        // Permettre aux liens dans le dropdown de fonctionner
        const dropdownButtons = newCartIcon.querySelectorAll('.cart-dropdown-button');
        dropdownButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Ne pas arrêter la propagation pour permettre la navigation
                e.stopPropagation();
            });
        });
    }
    
    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cart-dropdown') && !e.target.closest('.cart-icon')) {
            const dropdown = document.querySelector('.cart-dropdown.show');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        }
    });
});
</script>
    <script>
// Gestion du formulaire d'avis
document.addEventListener('DOMContentLoaded', function() {
    // Sélection des étoiles pour la note
    const ratingStars = document.querySelectorAll('.rating-star');
    if (ratingStars.length) {
        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                document.getElementById('note').value = value;
                
                // Mettre à jour l'apparence des étoiles
                ratingStars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                        s.classList.add('active');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                        s.classList.remove('active');
                    }
                });
            });
            
            // Effet de survol
            star.addEventListener('mouseenter', function() {
                const value = this.getAttribute('data-value');
                
                ratingStars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
        });
        
        // Rétablir l'état des étoiles lorsque la souris quitte la zone
        const ratingSelect = document.querySelector('.rating-select');
        if (ratingSelect) {
            ratingSelect.addEventListener('mouseleave', function() {
                const selectedValue = document.getElementById('note').value;
                
                ratingStars.forEach(s => {
                    if (selectedValue && s.getAttribute('data-value') <= selectedValue) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                        s.classList.add('active');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                        s.classList.remove('active');
                    }
                });
            });
        }
    }
    
    // Soumission du formulaire
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const note = document.getElementById('note').value;
            if (!note) {
                showNotification('Veuillez sélectionner une note', 'error');
                return;
            }
            
            const formData = new FormData(this);
            
            // Ajouter un header pour assurer le maintien de la session
            fetch(this.getAttribute('action'), {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Réponse serveur:', data);
                if (data.success) {
                    showNotification('Votre avis a été soumis avec succès et sera publié après validation', 'success');
                    this.reset();
                    
                    // Réinitialiser les étoiles
                    document.querySelectorAll('.rating-star').forEach(s => {
                        s.classList.remove('fas');
                        s.classList.add('far');
                        s.classList.remove('active');
                    });
                    document.getElementById('note').value = '';
                } else {
                    showNotification(data.error || 'Une erreur est survenue lors de l\'envoi de votre avis', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Une erreur de communication est survenue. Veuillez réessayer.', 'error');
            });
        });
    }
    
    // Fonction pour afficher les notifications si elle n'existe pas déjà
    if (typeof showNotification !== 'function') {
        window.showNotification = function(message, type) {
            const container = document.querySelector('.notifications-container');
            if (!container) return;
            
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            container.appendChild(notification);
            
            // Afficher la notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Supprimer la notification après 5 secondes
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }
    }
});
</script>
    <div class="notifications-container"></div>
    <script src="../../assets/js/cart.js" defer></script>
</body>
</html>