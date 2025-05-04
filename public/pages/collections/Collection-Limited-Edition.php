<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Collection Limitée - Elixir du Temps";
$pageDescription = "Des créations exceptionnelles et rares, chacune numérotée et produite en série limitée. Découvrez l'exclusivité à son apogée avec notre collection la plus prestigieuse.";

// Inclure les helpers pour les produits
require_once($relativePath . '/includes/product-helpers.php');

// Pré-charger tous les stocks pour optimiser les performances
$productIds = [401, 402, 403, 404, 405, 406, 407, 408]; // IDs des produits édition limitée
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

// Inclure le header
include($relativePath . '/includes/header.php');
?>

<!-- Video background et introduction -->
<div class="video-background">
    <video class="video-bg" autoplay muted loop playsinline preload="auto">
        <source src="../../assets/video/background.mp4" type="video/mp4">
        <!-- Fallback image si la vidéo ne se charge pas -->
        <img src="../../assets/img/collections/limited-hero.jpg" alt="Collection Limitée" class="fallback-img">
    </video>
    <div class="video-overlay"></div>
    
    <!-- Hero content sur la vidéo -->
    <div class="collection-hero">
        <div class="collection-hero-content">
            <h1 class="collection-title">Collection Limitée</h1>
            <p class="collection-description">Des créations exceptionnelles et rares, chacune numérotée et produite en série limitée. Découvrez l'exclusivité à son apogée avec notre collection la plus prestigieuse.</p>
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
            <!-- Premier produit - Chronos Édition Limitée -->
            <div class="product-card" data-product-id="401">
                <div class="product-image-container">
                    <img src="../../assets/img/products/Chronos-edition-limited.png" alt="<?php echo htmlspecialchars($productsInfo[401]['nom'] ?? 'Chronos Édition Limitée'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="401">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[401]) && $productsInfo[401]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[401]) && !empty($productsInfo[401]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[401]['prix_promo'] / $productsInfo[401]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[401]['nom'] ?? 'Chronos Édition Limitée'); ?></h3>
                    
                    <?php if (isset($productsInfo[401]) && !empty($productsInfo[401]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[401]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[401]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[401]) ? number_format($productsInfo[401]['prix'], 0, ',', ' ').' €' : '8 950 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(401); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(401)): ?>
                            <button class="add-to-cart-btn" data-product-id="401">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="401" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="401" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Deuxième produit - Prestige Unique -->
            <div class="product-card" data-product-id="402">
                <div class="product-image-container">
                    <img src="../../assets/img/products/Prestige-unique-edition-limited.png" alt="<?php echo htmlspecialchars($productsInfo[402]['nom'] ?? 'Prestige Unique'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="402">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[402]) && $productsInfo[402]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[402]) && !empty($productsInfo[402]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[402]['prix_promo'] / $productsInfo[402]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[402]['nom'] ?? 'Prestige Unique'); ?></h3>
                    
                    <?php if (isset($productsInfo[402]) && !empty($productsInfo[402]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[402]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[402]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[402]) ? number_format($productsInfo[402]['prix'], 0, ',', ' ').' €' : '4 750 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(402); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(402)): ?>
                            <button class="add-to-cart-btn" data-product-id="402">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="402" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="402" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Troisième produit - Héritage Exclusif -->
            <div class="product-card" data-product-id="403">
                <div class="product-image-container">
                    <img src="../../assets/img/products/HeritageUnique-edition-limited.png" alt="<?php echo htmlspecialchars($productsInfo[403]['nom'] ?? 'Héritage Exclusif'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="403">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[403]) && $productsInfo[403]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[403]) && !empty($productsInfo[403]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[403]['prix_promo'] / $productsInfo[403]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[403]['nom'] ?? 'Héritage Exclusif'); ?></h3>
                    
                    <?php if (isset($productsInfo[403]) && !empty($productsInfo[403]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[403]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[403]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[403]) ? number_format($productsInfo[403]['prix'], 0, ',', ' ').' €' : '12 800 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(403); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(403)): ?>
                            <button class="add-to-cart-btn" data-product-id="403">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="403" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="403" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Quatrième produit - Souveraineté Singulière -->
            <div class="product-card" data-product-id="404">
                <div class="product-image-container">
                    <img src="../../assets/img/products/SouvraineteSinguliere-edition-limited.png" alt="<?php echo htmlspecialchars($productsInfo[404]['nom'] ?? 'Souveraineté Singulière'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="404">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[404]) && $productsInfo[404]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[404]) && !empty($productsInfo[404]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[404]['prix_promo'] / $productsInfo[404]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[404]['nom'] ?? 'Souveraineté Singulière'); ?></h3>
                    
                    <?php if (isset($productsInfo[404]) && !empty($productsInfo[404]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[404]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[404]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[404]) ? number_format($productsInfo[404]['prix'], 0, ',', ' ').' €' : '7 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(404); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(404)): ?>
                            <button class="add-to-cart-btn" data-product-id="404">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="404" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="404" aria-label="Ajouter aux favoris">
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
            <div class="product-card" data-product-id="405">
                <div class="product-image-container">
                    <img src="../../assets/img/products/EleganceRose-edition-limited.png" alt="<?php echo htmlspecialchars($productsInfo[405]['nom'] ?? 'Élégance Rose'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="405">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[405]) && $productsInfo[405]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[405]) && !empty($productsInfo[405]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[405]['prix_promo'] / $productsInfo[405]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[405]['nom'] ?? 'Élégance Rose'); ?></h3>
                    
                    <?php if (isset($productsInfo[405]) && !empty($productsInfo[405]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[405]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[405]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[405]) ? number_format($productsInfo[405]['prix'], 0, ',', ' ').' €' : '13 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(405); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(405)): ?>
                            <button class="add-to-cart-btn" data-product-id="405">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="405" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="405" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Deuxième produit femme - Grâce Limitée -->
            <div class="product-card" data-product-id="406">
                <div class="product-image-container">
                    <img src="../../assets/img/products/GraceLimitee-edition-limited.png" alt="<?php echo htmlspecialchars($productsInfo[406]['nom'] ?? 'Grâce Limitée'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="406">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[406]) && $productsInfo[406]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[406]) && !empty($productsInfo[406]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[406]['prix_promo'] / $productsInfo[406]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[406]['nom'] ?? 'Grâce Limitée'); ?></h3>
                    
                    <?php if (isset($productsInfo[406]) && !empty($productsInfo[406]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[406]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[406]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[406]) ? number_format($productsInfo[406]['prix'], 0, ',', ' ').' €' : '13 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(406); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(406)): ?>
                            <button class="add-to-cart-btn" data-product-id="406">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="406" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="406" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Troisième produit femme - Éclat Unique -->
            <div class="product-card" data-product-id="407">
                <div class="product-image-container">
                    <img src="../../assets/img/products/EclatUnique-edition-limited.png" alt="<?php echo htmlspecialchars($productsInfo[407]['nom'] ?? 'Éclat Unique'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="407">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[407]) && $productsInfo[407]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[407]) && !empty($productsInfo[407]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[407]['prix_promo'] / $productsInfo[407]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[407]['nom'] ?? 'Éclat Unique'); ?></h3>
                    
                    <?php if (isset($productsInfo[407]) && !empty($productsInfo[407]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[407]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[407]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[407]) ? number_format($productsInfo[407]['prix'], 0, ',', ' ').' €' : '13 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(407); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(407)): ?>
                            <button class="add-to-cart-btn" data-product-id="407">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="407" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="407" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Quatrième produit femme - Essence Précieuse -->
            <div class="product-card" data-product-id="408">
                <div class="product-image-container">
                    <img src="../../assets/img/products/EssencePrecieuse-edition-limited.png" alt="<?php echo htmlspecialchars($productsInfo[408]['nom'] ?? 'Essence Précieuse'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="408">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[408]) && $productsInfo[408]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[408]) && !empty($productsInfo[408]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[408]['prix_promo'] / $productsInfo[408]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[408]['nom'] ?? 'Essence Précieuse'); ?></h3>
                    
                    <?php if (isset($productsInfo[408]) && !empty($productsInfo[408]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[408]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[408]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[408]) ? number_format($productsInfo[408]['prix'], 0, ',', ' ').' €' : '12 900 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(408); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(408)): ?>
                            <button class="add-to-cart-btn" data-product-id="408">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="408" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="408" aria-label="Ajouter aux favoris">
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
            $recommendedIds = [101, 201, 301, 501]; 
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

<!-- Modal pour aperçu rapide -->
<div id="quick-view-modal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="modal-product-title"></h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-content">
            <div class="modal-product-image">
                <img id="modal-product-image" src="" alt="">
            </div>
            <div class="modal-product-details">
                <p id="modal-product-price" class="product-price"></p>
                <div id="modal-stock-indicator"></div>
                <p id="modal-product-description" class="product-description">Montre exclusive et élégante de notre collection limitée.</p>
                <div class="modal-actions">
                    <button id="modal-add-to-cart" class="add-to-cart-btn">Ajouter au panier</button>
                    <button class="add-to-wishlist-btn" aria-label="Ajouter aux favoris">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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