<?php
$currentPage = 'Accueil.php';
$relativePath = '/Site-Vitrine/public';

// Inclure les helpers pour les produits
require_once($_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/includes/product-helpers.php');

// Récupérer les produits associés à la page d'accueil
$featuredProducts = getProductsByPage('accueil');

// Pré-charger les stocks pour optimiser les performances
$productIds = array_map(function($product) { 
    return $product['id']; 
}, $featuredProducts);

$productsStock = loadProductsStockBatch($productIds);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Accueil - Elixir du Temps</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Découvrez l'univers Elixir du Temps, montres de luxe et haute horlogerie suisse.">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="shortcut icon" href="../assets/img/layout/icon2.png" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="<?= $relativePath ?>/assets/css/header.css">

  <style>
    /* Base styles */
    body {
      margin: 0;
      padding: 0;
      font-family: 'Raleway', sans-serif;
      background-color: #fff;
      color: #333;
      overflow-x: hidden;
    }

    /* Video Background */
    .video-background {
      position: relative;
      height: 100vh;
      overflow: hidden;
    }

    .video-bg {
      position: absolute;
      top: 50%;
      left: 50%;
      min-width: 100%;
      min-height: 100%;
      width: auto;
      height: auto;
      transform: translateX(-50%) translateY(-50%);
      z-index: -1;
      object-fit: cover;
    }

    .video-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.6) 75%, #111 100%);
      z-index: 0;
    }

    /* Hero Section */
    .hero {
      position: relative;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      z-index: 1;
    }

    .hero-content {
      max-width: 900px;
      padding: 0 20px;
      z-index: 2;
    }

    .hero-title {
      font-family: 'Playfair Display', serif;
      font-size: 3.8rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
      line-height: 1.2;
    }

    .hero-subtitle {
      font-size: 1.4rem;
      font-weight: 300;
      line-height: 1.6;
      margin-bottom: 40px;
      max-width: 80%;
      margin-left: auto;
      margin-right: auto;
    }

    .hero-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .btn-primary {
      background-color: #d4af37;
      color: white;
      padding: 14px 32px;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border: 2px solid #d4af37;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.95rem;
    }

    .btn-primary:hover {
      background-color: #c49b27;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      border-color: #c49b27;
    }

    .btn-secondary {
      background-color: transparent;
      color: white;
      padding: 14px 32px;
      border: 2px solid white;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.95rem;
    }

    .btn-secondary:hover {
      background-color: rgba(255, 255, 255, 0.1);
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    /* Sections générales */
    .section {
      padding: 100px 20px;
    }

    .container {
      max-width: 1300px;
      margin: 0 auto;
    }

    .section-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.6rem;
      color: #333;
      text-align: center;
      margin-bottom: 20px;
      position: relative;
      padding-bottom: 15px;
    }

    .section-title::after {
      content: '';
      position: absolute;
      width: 80px;
      height: 3px;
      background-color: #d4af37;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
    }

    .section-subtitle {
      text-align: center;
      max-width: 700px;
      margin: 0 auto 60px;
      color: #666;
      font-size: 1.2rem;
      line-height: 1.7;
    }

    /* Welcome Section */
    .welcome-section {
      background-color: #f9f9f9;
    }

    .welcome-container {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 50px;
    }

    .welcome-image {
      flex: 1 1 500px;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      position: relative;
      transform: perspective(1000px) rotateY(-5deg);
      transition: all 0.5s ease;
    }

    .welcome-image:hover {
      transform: perspective(1000px) rotateY(0);
    }

    .welcome-image img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform 0.7s ease;
    }

    .welcome-image:hover img {
      transform: scale(1.05);
    }

    .welcome-text {
      flex: 1 1 500px;
    }

    .welcome-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.5rem;
      color: #333;
      margin-bottom: 25px;
      position: relative;
      padding-bottom: 15px;
    }

    .welcome-title::after {
      content: '';
      position: absolute;
      width: 60px;
      height: 3px;
      background-color: #d4af37;
      bottom: 0;
      left: 0;
    }

    .welcome-description {
      color: #555;
      font-size: 1.1rem;
      line-height: 1.8;
      margin-bottom: 25px;
    }

    /* Featured Collections */
    .collections-section {
      background-color: #fff;
    }

    .collections-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
    }

    .collection-card {
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.4s ease;
    }

    .collection-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .collection-image {
      height: 300px;
      overflow: hidden;
      position: relative;
    }

    .collection-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s ease;
    }

    .collection-card:hover .collection-image img {
      transform: scale(1.1);
    }

    .collection-info {
      padding: 25px 20px;
      text-align: center;
    }

    .collection-info h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      color: #333;
      margin-bottom: 15px;
    }

    .collection-info p {
      color: #666;
      margin-bottom: 15px;
      line-height: 1.6;
    }

    .price-range {
      display: block;
      color: #d4af37;
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .explore-button {
      display: inline-block;
      background-color: transparent;
      color: #333;
      padding: 12px 25px;
      border: 2px solid #d4af37;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .explore-button:hover {
      background-color: #d4af37;
      color: white;
      transform: translateY(-3px);
    }

    /* Features Section */
    .features-section {
      background-color: #f9f9f9;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
    }

    .feature-card {
      background-color: white;
      padding: 40px 25px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }

    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .feature-icon {
      color: #d4af37;
      font-size: 3rem;
      margin-bottom: 20px;
      transition: transform 0.3s ease;
    }

    .feature-card:hover .feature-icon {
      transform: scale(1.1);
    }

    .feature-card h3 {
      font-size: 1.4rem;
      color: #333;
      margin-bottom: 15px;
    }

    .feature-card p {
      color: #666;
      font-size: 0.95rem;
      line-height: 1.6;
    }

    /* Newsletter Section */
    .newsletter-section {
      background-image: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('<?= $relativePath ?>/assets/img/backgrounds/newsletter-bg.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: white;
      text-align: center;
    }

    .newsletter-title {
      color: #d4af37;
      margin-bottom: 20px;
    }

    .newsletter-subtitle {
      color: rgba(255, 255, 255, 0.9);
      margin-bottom: 40px;
    }

    .newsletter-form {
      display: flex;
      max-width: 550px;
      margin: 0 auto;
      border-radius: 50px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .newsletter-input {
      flex: 1;
      padding: 18px 25px;
      border: none;
      font-size: 1rem;
      font-family: 'Raleway', sans-serif;
    }

    .newsletter-button {
      background-color: #d4af37;
      color: white;
      border: none;
      padding: 18px 30px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-family: 'Raleway', sans-serif;
    }

    .newsletter-button:hover {
      background-color: #c49b27;
    }

    /* Animation classes */
    .animated-element {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }

    .animated-element.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Styles pour la section Produits populaires */
    .featured-products-section {
      background-color: #fff;
      padding: 100px 0;
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 50px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    
    @media (max-width: 992px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(1, 1fr);
            max-width: 400px;
        }
    }
    
    .product-card {
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.4s ease;
      display: flex;
      flex-direction: column;
    }
    
    .product-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .product-image-container {
      height: 280px;
      position: relative;
      overflow: hidden;
    }
    
    .product-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s ease;
    }
    
    .product-card:hover .product-image {
      transform: scale(1.1);
    }
    
    .product-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.4);
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 0;
      transition: opacity 0.4s ease;
    }
    
    .product-card:hover .product-overlay {
      opacity: 1;
    }
    
    .quick-view-btn {
      background-color: #fff;
      color: #333;
      padding: 12px 24px;
      border: none;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .quick-view-btn:hover {
      background-color: #d4af37;
      color: #fff;
    }
    
    .product-info {
      padding: 25px 20px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }
    
    .product-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.4rem;
      margin-bottom: 10px;
      color: #333;
    }
    
    .product-price {
      color: #d4af37;
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 15px;
    }
    
    .price-old {
      text-decoration: line-through;
      color: #999;
      font-size: 1rem;
      margin-right: 10px;
    }
    
    .product-actions {
      margin-top: auto;
      display: flex;
      justify-content: space-between;
    }
    
    .add-to-cart-btn {
      background-color: #d4af37;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      flex-grow: 1;
      margin-right: 10px;
    }
    
    .add-to-cart-btn:hover {
      background-color: #c49b27;
    }
    
    .add-to-wishlist-btn {
      background-color: transparent;
      border: 1px solid #d4af37;
      color: #d4af37;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .add-to-wishlist-btn:hover {
      background-color: #d4af37;
      color: white;
    }
    
    .product-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      padding: 8px 12px;
      border-radius: 4px;
      color: white;
      font-weight: 600;
      font-size: 0.9rem;
      z-index: 2;
    }
    
    .new {
      background-color: #28a745;
    }
    
    .sale {
      background-color: #dc3545;
    }
    
    /* Style pour l'affichage des images manquantes */
    .no-image {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f8f9fa;
      position: relative;
    }
    
    .no-image i {
      font-size: 3rem;
      color: #dee2e6;
    }
    
    /* Styles stock indicator */
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
      opacity: 0.6;
      cursor: not-allowed;
      background-color: #6c757d;
    }
    
    /* Responsive ajustements */
    @media (max-width: 768px) {
      .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      }
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
      .hero-title {
        font-size: 3.2rem;
      }
      
      .welcome-container {
        flex-direction: column-reverse;
      }
      
      .welcome-title::after {
        left: 50%;
        transform: translateX(-50%);
      }
      
      .welcome-text {
        text-align: center;
      }
    }

    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.5rem;
      }
      
      .hero-subtitle {
        font-size: 1.1rem;
        max-width: 100%;
      }
      
      .hero-buttons {
        flex-direction: column;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
      }
      
      .section {
        padding: 70px 20px;
      }
      
      .section-title {
        font-size: 2.2rem;
      }
      
      .newsletter-form {
        flex-direction: column;
        border-radius: 10px;
      }
      
      .newsletter-input,
      .newsletter-button {
        width: 100%;
        text-align: center;
        border-radius: 0;
      }
    }

    /* Style pour l'étiquette "Rupture de stock" */
    .product-badge.out-of-stock {
      background-color: #dc3545;
      color: white;
      font-weight: bold;
    }

    .out-of-stock-overlay {
      position: absolute;
      top: 50%;
      left: 0;
      width: 100%;
      text-align: center;
      background-color: rgba(220, 53, 69, 0.8);
      color: white;
      padding: 8px 0;
      font-weight: bold;
      transform: translateY(-50%);
    }

    .no-image {
      position: relative;
    }
  </style>
</head>
<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/Includes/header.php'; ?>

<!-- Video Background avec Hero Section -->
<div class="video-background">
  <video class="video-bg" autoplay muted loop playsinline preload="auto">
    <source src="<?= $relativePath ?>/assets/video/background.mp4" type="video/mp4">
    Votre navigateur ne supporte pas la vidéo.
  </video>
  <div class="video-overlay"></div>
  
  <section class="hero">
    <div class="hero-content animate__animated animate__fadeIn">
      <h1 class="hero-title animate__animated animate__fadeInUp animate__delay-1s">L'excellence horlogère à votre poignet</h1>
      <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">Découvrez nos créations uniques et intemporelles, alliant tradition séculaire et innovation contemporaine</p>
      <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-2s">
        <a href="<?= $relativePath ?>/pages/Collections.php" class="btn-primary">Nos collections</a>
        <a href="<?= $relativePath ?>/pages/Montres.php" class="btn-secondary">Explorer</a>
      </div>
    </div>
  </section>
</div>

<!-- Welcome Section -->
<section class="section welcome-section">
  <div class="container welcome-container">
    <div class="welcome-image animated-element">
      <img src="<?= $relativePath ?>/assets/img/layout/atelier.png" alt="Atelier Elixir du Temps" loading="lazy">
    </div>
    <div class="welcome-text animated-element">
      <h2 class="welcome-title">Bienvenue chez Elixir du Temps</h2>
      <p class="welcome-description">
        Depuis 1985, Elixir du Temps incarne l'excellence et le prestige de la haute horlogerie suisse. 
        Nos montres ne sont pas de simples instruments de mesure du temps, mais de véritables œuvres d'art 
        portables qui reflètent votre personnalité et votre statut.
      </p>
      <p class="welcome-description">
        À travers nos collections, nous marions la tradition séculaire des maîtres horlogers à l'innovation 
        technologique la plus pointue. Chaque garde-temps est assemblé à la main dans nos ateliers de Genève, 
        perpétuant un savoir-faire unique et un souci du détail incomparable.
      </p>
      <a href="<?= $relativePath ?>/pages/APropos.php" class="btn-primary">Découvrir notre histoire</a>
    </div>
  </div>
</section>

<!-- Featured Collections Section -->
<section class="section collections-section">
  <div class="container">
    <h2 class="section-title animated-element">Nos collections emblématiques</h2>
    <p class="section-subtitle animated-element">Un héritage d'excellence et de raffinement, fruit de notre expertise horlogère</p>
    
    <div class="collections-grid">
      <div class="collection-card animated-element">
        <div class="collection-image">
          <img src="<?= $relativePath ?>/assets/img/collections/classique.jpg" alt="Collection Classique" loading="lazy">
        </div>
        <div class="collection-info">
          <h3>Classique</h3>
          <p>L'élégance intemporelle qui traverse les époques</p>
          <span class="price-range">À partir de 8 500 €</span>
          <a href="<?= $relativePath ?>/pages/collections/Collection-Classic.php" class="explore-button">Découvrir</a>
        </div>
      </div>
      
      <div class="collection-card animated-element">
        <div class="collection-image">
          <img src="<?= $relativePath ?>/assets/img/collections/prestige.jpg" alt="Collection Prestige" loading="lazy">
        </div>
        <div class="collection-info">
          <h3>Prestige</h3>
          <p>Le summum du luxe et de la haute horlogerie</p>
          <span class="price-range">À partir de 25 000 €</span>
          <a href="<?= $relativePath ?>/pages/collections/Collection-Prestige.php" class="explore-button">Découvrir</a>
        </div>
      </div>
      
      <div class="collection-card animated-element">
        <div class="collection-image">
          <img src="<?= $relativePath ?>/assets/img/collections/sport.jpg" alt="Collection Sport" loading="lazy">
        </div>
        <div class="collection-info">
          <h3>Sport</h3>
          <p>Robustes et performantes pour tous vos défis</p>
          <span class="price-range">À partir de 12 000 €</span>
          <a href="<?= $relativePath ?>/pages/collections/Collection-Sport.php" class="explore-button">Découvrir</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Nouvelle section Produits populaires - limité à 3 montres -->
<section class="section featured-products-section">
  <div class="container">
    <h2 class="section-title animated-element">Nos montres populaires</h2>
    <p class="section-subtitle animated-element">Découvrez nos modèles les plus prisés, reflets de notre savoir-faire horloger d'exception</p>
    
    <div class="products-grid">
      <?php 
      // Limiter à seulement 3 produits
      $limitedProducts = array_slice($featuredProducts, 0, 3);
      
      // Vérifier si nous avons des produits
      if(empty($limitedProducts)): 
      ?>
        <div class="alert-message" style="text-align: center; width: 100%; padding: 30px;">
          <p>Nos montres populaires sont temporairement en rupture de stock. Veuillez consulter notre catalogue complet.</p>
        </div>
      <?php else: ?>
      
      <?php foreach($limitedProducts as $product): 
        // Vérifier si le produit existe réellement et a toutes les propriétés nécessaires
        $productExists = isset($product['id']) && isset($product['nom']) && isset($product['prix']);
        $isAvailable = $productExists && isProductAvailable($product['id']);
      ?>
      <div class="product-card animated-element" data-product-id="<?php echo $productExists ? $product['id'] : 0; ?>">
        <div class="product-image-container">
          <?php if ($productExists && !empty($product['image'])): ?>
            <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($product['image'])); ?>" 
                 alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                 class="product-image" 
                 loading="lazy">
          <?php else: ?>
            <div class="no-image">
              <i class="fas fa-image"></i>
              <div class="out-of-stock-overlay">Rupture de stock</div>
            </div>
          <?php endif; ?>
          <div class="product-overlay">
            <button class="quick-view-btn" data-product-id="<?php echo $productExists ? $product['id'] : 0; ?>">
              Aperçu rapide
            </button>
          </div>
          <?php if ($productExists && isset($product['nouveaute']) && $product['nouveaute']): ?>
            <div class="product-badge new">Nouveau</div>
          <?php endif; ?>
          <?php if ($productExists && !empty($product['prix_promo'])): ?>
            <div class="product-badge sale">-<?php echo round((1 - $product['prix_promo'] / $product['prix']) * 100); ?>%</div>
          <?php endif; ?>
          <!-- Modification du badge pour les produits non trouvés -->
          <?php if (!$productExists): ?>
            <div class="product-badge out-of-stock">Indisponible</div>
          <?php elseif (!$isAvailable): ?>
            <div class="product-badge out-of-stock">Rupture de stock</div>
          <?php endif; ?>
        </div>
        <div class="product-info">
          <h3 class="product-title"><?php echo $productExists ? htmlspecialchars($product['nom']) : 'Produit en rupture de stock'; ?></h3>
          
          <?php if ($productExists && !empty($product['prix_promo'])): ?>
            <p class="product-price">
              <span class="price-old"><?php echo number_format($product['prix'], 0, ',', ' '); ?> €</span> 
              <?php echo number_format($product['prix_promo'], 0, ',', ' '); ?> €
            </p>
          <?php elseif ($productExists): ?>
            <p class="product-price"><?php echo number_format($product['prix'], 0, ',', ' '); ?> €</p>
          <?php else: ?>
            <p class="product-price">Prix non disponible</p>
          <?php endif; ?>
          
          <?php echo $productExists ? generateStockIndicator($product['id']) : '<div class="stock-indicator out-of-stock"><i class="fas fa-times-circle"></i> Rupture de stock</div>'; ?>
          
          <div class="product-actions">
            <?php if ($productExists && $isAvailable): ?>
              <button class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                Ajouter au panier
              </button>
            <?php elseif (!$productExists): ?>
              <button class="add-to-cart-btn disabled" data-product-id="0" disabled>
                Indisponible
              </button>
            <?php else: ?>
              <button class="add-to-cart-btn disabled" data-product-id="<?php echo $product['id']; ?>" disabled>
                Rupture de stock
              </button>
            <?php endif; ?>
            
            <button class="add-to-wishlist-btn" data-product-id="<?php echo $productExists ? $product['id'] : 0; ?>" aria-label="Ajouter aux favoris">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
    
    <div class="text-center" style="margin-top: 50px;">
      <a href="<?= $relativePath ?>/pages/Montres.php" class="btn-primary">Voir toute la collection</a>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="section features-section">
  <div class="container">
    <h2 class="section-title animated-element">Nos garanties</h2>
    <p class="section-subtitle animated-element">Nous vous offrons une expérience d'achat sans compromis</p>
    
    <div class="features-grid">
      <div class="feature-card animated-element">
        <div class="feature-icon">
          <i class="fas fa-shield-alt"></i>
        </div>
        <h3>Garantie 5 ans</h3>
        <p>Toutes nos montres bénéficient d'une garantie internationale et d'un service après-vente d'exception</p>
      </div>
      
      <div class="feature-card animated-element">
        <div class="feature-icon">
          <i class="fas fa-shipping-fast"></i>
        </div>
        <h3>Livraison offerte</h3>
        <p>Livraison express et assurée dans le monde entier, avec un emballage sécurisé et élégant</p>
      </div>
      
      <div class="feature-card animated-element">
        <div class="feature-icon">
          <i class="fas fa-undo-alt"></i>
        </div>
        <h3>Retours gratuits</h3>
        <p>30 jours pour changer d'avis, avec retours gratuits et remboursement rapide et sécurisé</p>
      </div>
      
      <div class="feature-card animated-element">
        <div class="feature-icon">
          <i class="fas fa-headset"></i>
        </div>
        <h3>Service client dédié</h3>
        <p>Une équipe d'experts à votre service pour vous conseiller dans votre choix et répondre à vos questions</p>
      </div>
    </div>
  </div>
</section>

<!-- Newsletter Section -->
<section class="section newsletter-section">
  <div class="container">
    <h2 class="section-title newsletter-title animated-element">Restez informé</h2>
    <p class="section-subtitle newsletter-subtitle animated-element">Inscrivez-vous à notre newsletter pour recevoir nos actualités, nos offres exclusives et nos conseils d'experts</p>
    
    <form class="newsletter-form animated-element" action="<?= $relativePath ?>/php/api/newsletter/subscribe.php" method="post">
      <input type="email" class="newsletter-input" name="email" placeholder="Votre adresse email" required>
      <button type="submit" class="newsletter-button">S'inscrire</button>
    </form>
  </div>
</section>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/Includes/footer.php'; ?>

<!-- Script pour les animations au scroll -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Supprimer définitivement le loader
    const loader = document.querySelector('.loader-container');
    if (loader) {
      loader.style.display = 'none';
      loader.remove();
    }
    
    // Animation au scroll pour les éléments avec classe .animated-element
    const animatedElements = document.querySelectorAll('.animated-element');
    
    const checkIfInView = function() {
      animatedElements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementTop < windowHeight - 100) {
          element.classList.add('visible');
        }
      });
    };
    
    // Vérifier lors du scroll
    window.addEventListener('scroll', checkIfInView);
    
    // Vérifier aussi au chargement
    checkIfInView();
  });
</script>

<!-- Ajouter ce script pour la redirection vers product-detail.php -->
<script>
    // Redirection depuis les boutons d'aperçu rapide vers les pages de détail
    document.addEventListener('DOMContentLoaded', function() {
        const quickViewButtons = document.querySelectorAll('.quick-view-btn');
        
        if (quickViewButtons.length) {
            quickViewButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.getAttribute('data-product-id');
                    
                    // Vérifier si le produit existe avant de rediriger
                    fetch('/Site-Vitrine/public/php/api/products/check-product.php?id=' + productId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                // Le produit existe, rediriger vers sa page détaillée
                                window.location.href = '/Site-Vitrine/public/pages/products/product-detail.php?id=' + productId;
                            } else {
                                // Le produit n'existe pas, rediriger vers la page des montres
                                window.location.href = '/Site-Vitrine/public/pages/products/Montres.php';
                                // Optionnel : afficher un message d'erreur
                                // alert('Produit introuvable. Redirection vers la liste des montres.');
                            }
                        })
                        .catch(error => {
                            // En cas d'erreur, rediriger vers la page des montres
                            console.error('Erreur lors de la vérification du produit:', error);
                            window.location.href = '/Site-Vitrine/public/pages/products/Montres.php';
                        });
                });
            });
        }
    });
</script>

<!-- Importation des fichiers JS modulaires (une seule fois chacun) -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-filters.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/quick-view.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/wishlist-manager.js"></script>

</body>
</html>
