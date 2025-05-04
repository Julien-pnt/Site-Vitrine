<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Collection Classic - Elixir du Temps";
$pageDescription = "Découvrez notre Collection Classic - Des montres élégantes qui incarnent l'excellence horlogère d'Elixir du Temps.";

// Inclure les helpers pour les produits
require_once($relativePath . '/includes/product-helpers.php');

// Pré-charger tous les stocks pour optimiser les performances
$productIds = [101, 102, 103, 104, 201, 202, 203, 204, 501]; // IDs de tous vos produits
$productsStock = loadProductsStockBatch($productIds);

// Pré-charger les informations des produits
$productsInfo = getProductsInfoBatch($productIds);

// CSS spécifique à cette page
$additionalCss = '
<link rel="stylesheet" href="../../assets/css/collections.css">
<link rel="stylesheet" href="../../assets/css/wishlist-buttons.css">
<style>
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
    
    .add-to-cart-btn.disabled,
    #modal-add-to-cart.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: #6c757d;
    }

    .stock-warning {
        color: #ffc107;
        font-style: italic;
        font-size: 0.8rem;
    }
</style>
';

// Inclure le header (chemin corrigé)
include($relativePath . '/includes/header.php');
?>

<!-- Ajouter la vidéo d'arrière-plan -->
<div class="video-background">
    <video class="video-bg" autoplay muted loop playsinline preload="auto">
        <source src="../../assets/video/background.mp4" type="video/mp4">
        <!-- Fallback image si la vidéo ne se charge pas -->
        <img src="../../assets/img/collections/classic-hero.jpg" alt="Collection Classic" class="fallback-img">
    </video>
    <div class="video-overlay"></div>
    
    <!-- Hero content sur la vidéo -->
    <div class="collection-hero">
        <div class="collection-hero-content">
            <h1 class="collection-title">Collection Classic</h1>
            <p class="collection-description">L'élégance intemporelle dans sa forme la plus pure. Notre collection Classic rend hommage aux traditions horlogères avec une touche contemporaine.</p>
        </div>
    </div>
</div>

<!-- Filtres et Recherche -->
<section class="collection-filters">
    <div class="container">
        <div class="filters-row">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Rechercher une montre..." aria-label="Rechercher une montre">
                <button class="search-button" aria-label="Rechercher">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </div>
            
            <div class="filter-options">
                <div class="sort-section">
                    <label for="sort-by">Trier par :</label>
                    <select id="sort-by" aria-label="Trier les montres par">
                        <option value="nouveaute">Nouveautés</option>
                        <option value="price-asc">Prix croissant</option>
                        <option value="price-desc">Prix décroissant</option>
                        <option value="popularity">Popularité</option>
                    </select>
                </div>
                
                <div class="gender-filter">
                    <span>Catégorie :</span>
                    <div class="gender-buttons">
                        <button id="homme-button" class="gender-button active" aria-pressed="true">Homme</button>
                        <button id="femme-button" class="gender-button" aria-pressed="false">Femme</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section produits -->
<section class="products-section">
    <div class="container">
        <!-- Section pour Homme products -->
        <div id="homme-products" class="product-grid active">
            <!-- Premier produit - Élégance Éternelle -->
            <div class="product-card" data-product-id="101">
                <div class="product-image-container">
                    <img src="../../assets/img/products/elegance-eternal.jpg" alt="<?php echo htmlspecialchars($productsInfo[101]['nom'] ?? 'Élégance Éternelle'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="101">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[101]) && $productsInfo[101]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[101]['nom'] ?? 'Élégance Éternelle'); ?></h3>
                    
                    <?php if (isset($productsInfo[101]) && !empty($productsInfo[101]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[101]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[101]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[101]) ? number_format($productsInfo[101]['prix'], 0, ',', ' ').' €' : '8 950 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(101); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(101)): ?>
                            <button class="add-to-cart-btn" data-product-id="101">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="101" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="101" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Deuxième produit - Tradition Classique -->
            <div class="product-card" data-product-id="102">
                <div class="product-image-container">
                    <img src="../../assets/img/products/tradition-classique.jpg" alt="<?php echo htmlspecialchars($productsInfo[102]['nom'] ?? 'Tradition Classique'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="102">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[102]) && $productsInfo[102]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[102]['nom'] ?? 'Tradition Classique'); ?></h3>
                    
                    <?php if (isset($productsInfo[102]) && !empty($productsInfo[102]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[102]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[102]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[102]) ? number_format($productsInfo[102]['prix'], 0, ',', ' ').' €' : '4 750 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(102); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(102)): ?>
                            <button class="add-to-cart-btn" data-product-id="102">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="102" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="102" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Troisième produit - Raffinement Or -->
            <div class="product-card" data-product-id="103">
                <div class="product-image-container">
                    <img src="../../assets/img/products/raffinement-or.jpg" alt="<?php echo htmlspecialchars($productsInfo[103]['nom'] ?? 'Raffinement Or'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="103">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[103]) && !empty($productsInfo[103]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[103]['prix_promo'] / $productsInfo[103]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[103]['nom'] ?? 'Raffinement Or'); ?></h3>
                    
                    <?php if (isset($productsInfo[103]) && !empty($productsInfo[103]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[103]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[103]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[103]) ? number_format($productsInfo[103]['prix'], 0, ',', ' ').' €' : '14 200 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(103); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(103)): ?>
                            <button class="add-to-cart-btn" data-product-id="103">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="103" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="103" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Quatrième produit - Heritage Prestige -->
            <div class="product-card" data-product-id="104">
                <div class="product-image-container">
                    <img src="../../assets/img/products/heritage-prestige.jpg" alt="<?php echo htmlspecialchars($productsInfo[104]['nom'] ?? 'Heritage Prestige'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="104">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[104]) && $productsInfo[104]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[104]['nom'] ?? 'Heritage Prestige'); ?></h3>
                    
                    <?php if (isset($productsInfo[104]) && !empty($productsInfo[104]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[104]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[104]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[104]) ? number_format($productsInfo[104]['prix'], 0, ',', ' ').' €' : '7 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(104); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(104)): ?>
                            <button class="add-to-cart-btn" data-product-id="104">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="104" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="104" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 5 - Excellence Royale -->
            <div class="product-card" data-product-id="501">
                <div class="product-image-container">
                    <img src="../../assets/img/products/excellence-royale.jpg" alt="<?php echo htmlspecialchars($productsInfo[501]['nom'] ?? 'Excellence Royale'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="501">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[501]) && $productsInfo[501]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[501]) && !empty($productsInfo[501]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[501]['prix_promo'] / $productsInfo[501]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[501]['nom'] ?? 'Excellence Royale'); ?></h3>
                    
                    <?php if (isset($productsInfo[501]) && !empty($productsInfo[501]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[501]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[501]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[501]) ? number_format($productsInfo[501]['prix'], 0, ',', ' ').' €' : '32 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(501); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(501)): ?>
                            <button class="add-to-cart-btn" data-product-id="501">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="501" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="501" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section pour Femme products -->
        <div id="femme-products" class="product-grid">
            <!-- Premier produit femme - Élégance Rose -->
            <div class="product-card" data-product-id="201">
                <div class="product-image-container">
                    <img src="../../assets/img/products/elegance-rose.jpg" alt="<?php echo htmlspecialchars($productsInfo[201]['nom'] ?? 'Élégance Rose'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="201">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[201]) && $productsInfo[201]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[201]['nom'] ?? 'Élégance Rose'); ?></h3>
                    
                    <?php if (isset($productsInfo[201]) && !empty($productsInfo[201]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[201]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[201]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[201]) ? number_format($productsInfo[201]['prix'], 0, ',', ' ').' €' : '13 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(201); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(201)): ?>
                            <button class="add-to-cart-btn" data-product-id="201">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="201" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="201" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Deuxième produit femme - Classic Diamond -->
            <div class="product-card" data-product-id="202">
                <div class="product-image-container">
                    <img src="../../assets/img/products/classic-diamond.jpg" alt="<?php echo htmlspecialchars($productsInfo[202]['nom'] ?? 'Classic Diamond'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="202">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[202]) && $productsInfo[202]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[202]['nom'] ?? 'Classic Diamond'); ?></h3>
                    
                    <?php if (isset($productsInfo[202]) && !empty($productsInfo[202]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[202]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[202]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[202]) ? number_format($productsInfo[202]['prix'], 0, ',', ' ').' €' : '9 800 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(202); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(202)): ?>
                            <button class="add-to-cart-btn" data-product-id="202">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="202" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="202" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Troisième produit femme - Perle Classique -->
            <div class="product-card" data-product-id="203">
                <div class="product-image-container">
                    <img src="../../assets/img/products/perle-classique.jpg" alt="<?php echo htmlspecialchars($productsInfo[203]['nom'] ?? 'Perle Classique'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="203">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[203]) && !empty($productsInfo[203]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[203]['prix_promo'] / $productsInfo[203]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[203]['nom'] ?? 'Perle Classique'); ?></h3>
                    
                    <?php if (isset($productsInfo[203]) && !empty($productsInfo[203]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[203]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[203]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[203]) ? number_format($productsInfo[203]['prix'], 0, ',', ' ').' €' : '11 200 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(203); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(203)): ?>
                            <button class="add-to-cart-btn" data-product-id="203">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="203" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="203" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Quatrième produit femme - Essence Intemporelle -->
            <div class="product-card" data-product-id="204">
                <div class="product-image-container">
                    <img src="../../assets/img/products/essence-intemporelle.jpg" alt="<?php echo htmlspecialchars($productsInfo[204]['nom'] ?? 'Essence Intemporelle'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="204">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[204]) && $productsInfo[204]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[204]['nom'] ?? 'Essence Intemporelle'); ?></h3>
                    
                    <?php if (isset($productsInfo[204]) && !empty($productsInfo[204]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[204]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[204]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[204]) ? number_format($productsInfo[204]['prix'], 0, ',', ' ').' €' : '6 750 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(204); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(204)): ?>
                            <button class="add-to-cart-btn" data-product-id="204">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="204" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="204" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Recommandations -->
<section class="recommendations">
    <div class="container">
        <h2 class="section-title">Vous pourriez aussi aimer</h2>
        <div class="recommendations-carousel">
            <?php
            // IDs pour les produits recommandés
            $recommendedIds = [301, 401, 501, 104];
            $recommendedInfo = getProductsInfoBatch($recommendedIds);
            
            foreach($recommendedIds as $recId):
                if(isset($recommendedInfo[$recId])):
            ?>
            <div class="recommendation-item">
                <img src="../../assets/img/products/<?php echo $recId; ?>.jpg" alt="<?php echo htmlspecialchars($recommendedInfo[$recId]['nom'] ?? ''); ?>" loading="lazy">
                <h3><?php echo htmlspecialchars($recommendedInfo[$recId]['nom'] ?? ''); ?></h3>
                
                <?php if (isset($recommendedInfo[$recId]) && !empty($recommendedInfo[$recId]['prix_promo'])): ?>
                    <p class="product-price">
                        <span class="price-old"><?php echo number_format($recommendedInfo[$recId]['prix'], 0, ',', ' '); ?> €</span> 
                        <?php echo number_format($recommendedInfo[$recId]['prix_promo'], 0, ',', ' '); ?> €
                    </p>
                <?php else: ?>
                    <p><?php echo isset($recommendedInfo[$recId]['prix']) ? number_format($recommendedInfo[$recId]['prix'], 0, ',', ' ').' €' : ''; ?></p>
                <?php endif; ?>
                
                <a href="<?php echo $relativePath; ?>/produit.php?id=<?php echo $recId; ?>" class="view-product">Découvrir</a>
            </div>
            <?php
                endif;
            endforeach;
            ?>
        </div>
    </div>
</section>

<?php
// Inclure le footer
include($relativePath . '/includes/footer.php');
?>

<!-- Importation des fichiers JS modulaires (une seule fois chacun) -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-filters.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/quick-view.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/wishlist-manager.js"></script>

<!-- Script spécifique à la page (qui ne fait pas doublon avec les modules) -->
<script>
    // Redirection depuis les boutons d'aperçu rapide vers les pages de détail
    document.addEventListener('DOMContentLoaded', function() {
        const quickViewButtons = document.querySelectorAll('.quick-view-btn');
        
        if (quickViewButtons.length) {
            quickViewButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.getAttribute('data-product-id');
                    window.location.href = '../products/product-detail.php?id=' + productId;
                });
            });
        }
    });
</script>

</body>
</html>