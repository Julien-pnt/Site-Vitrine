<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Collection Sport - Elixir du Temps";
$pageDescription = "Découvrez notre Collection Sport - Des montres d'exception alliant performance et élégance par Elixir du Temps.";

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

// Inclure les helpers pour les produits
require_once($relativePath . '/includes/product-helpers.php');

// Pré-charger tous les stocks pour optimiser les performances
$productIds = [301, 302, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312];
$productsStock = loadProductsStockBatch($productIds);

// Pré-charger les informations des produits
$productsInfo = getProductsInfoBatch($productIds);

// Inclure le header
include($relativePath . '/includes/header.php');
?>

<!-- Video background et introduction -->
<div class="video-background">
    <video class="video-bg" autoplay muted loop playsinline preload="auto">
        <source src="../../assets/video/background-sport.mp4" type="video/mp4">
        <!-- Fallback image si la vidéo ne se charge pas -->
        <img src="../../assets/img/collections/sport-hero.jpg" alt="Collection Sport" class="fallback-img">
    </video>
    <div class="video-overlay"></div>
    
    <!-- Hero content sur la vidéo -->
    <div class="collection-hero">
        <div class="collection-hero-content">
            <h1 class="collection-title">Collection Sport</h1>
            <p class="collection-description">Performance et élégance en parfaite harmonie. Des montres conçues pour les amateurs de sensations fortes, alliant robustesse, précision et design audacieux.</p>
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
            <!-- Produit 1 -->
            <div class="product-card" data-product-id="301">
                <div class="product-image-container">
                    <img src="../../assets/img/products/chrono-sport-pro.jpg" alt="<?php echo htmlspecialchars($productsInfo[301]['nom'] ?? 'Chrono Sport Pro'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="301">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[301]) && $productsInfo[301]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[301]['nom'] ?? 'Chrono Sport Pro'); ?></h3>
                    
                    <?php if (isset($productsInfo[301]) && !empty($productsInfo[301]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[301]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[301]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[301]) ? number_format($productsInfo[301]['prix'], 0, ',', ' ').' €' : '9 800 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(301); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(301)): ?>
                            <button class="add-to-cart-btn" data-product-id="301">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="301" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="301" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 2 -->
            <div class="product-card" data-product-id="302">
                <div class="product-image-container">
                    <img src="../../assets/img/products/aqua-dive-master.jpg" alt="<?php echo htmlspecialchars($productsInfo[302]['nom'] ?? 'Aqua Dive Master'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="302">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[302]) && $productsInfo[302]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[302]['nom'] ?? 'Aqua Dive Master'); ?></h3>
                    
                    <?php if (isset($productsInfo[302]) && !empty($productsInfo[302]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[302]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[302]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[302]) ? number_format($productsInfo[302]['prix'], 0, ',', ' ').' €' : '11 200 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(302); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(302)): ?>
                            <button class="add-to-cart-btn" data-product-id="302">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="302" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="302" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 3 -->
            <div class="product-card" data-product-id="303">
                <div class="product-image-container">
                    <img src="../../assets/img/products/pilot-chronograph.jpg" alt="<?php echo htmlspecialchars($productsInfo[303]['nom'] ?? 'Pilot Chronograph'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="303">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[303]) && $productsInfo[303]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[303]['nom'] ?? 'Pilot Chronograph'); ?></h3>
                    
                    <?php if (isset($productsInfo[303]) && !empty($productsInfo[303]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[303]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[303]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[303]) ? number_format($productsInfo[303]['prix'], 0, ',', ' ').' €' : '8 900 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(303); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(303)): ?>
                            <button class="add-to-cart-btn" data-product-id="303">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="303" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="303" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 4 -->
            <div class="product-card" data-product-id="304">
                <div class="product-image-container">
                    <img src="../../assets/img/products/racing-timer.jpg" alt="<?php echo htmlspecialchars($productsInfo[304]['nom'] ?? 'Racing Timer'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="304">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[304]) && !empty($productsInfo[304]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[304]['prix_promo'] / $productsInfo[304]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[304]['nom'] ?? 'Racing Timer'); ?></h3>
                    
                    <?php if (isset($productsInfo[304]) && !empty($productsInfo[304]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[304]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[304]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[304]) ? number_format($productsInfo[304]['prix'], 0, ',', ' ').' €' : '12 300 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(304); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(304)): ?>
                            <button class="add-to-cart-btn" data-product-id="304">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="304" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="304" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 5 -->
            <div class="product-card" data-product-id="305">
                <div class="product-image-container">
                    <img src="../../assets/img/products/explorer-gmt.jpg" alt="<?php echo htmlspecialchars($productsInfo[305]['nom'] ?? 'Explorer GMT'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="305">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[305]) && $productsInfo[305]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[305]['nom'] ?? 'Explorer GMT'); ?></h3>
                    
                    <?php if (isset($productsInfo[305]) && !empty($productsInfo[305]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[305]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[305]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[305]) ? number_format($productsInfo[305]['prix'], 0, ',', ' ').' €' : '13 800 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(305); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(305)): ?>
                            <button class="add-to-cart-btn" data-product-id="305">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="305" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="305" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 6 -->
            <div class="product-card" data-product-id="306">
                <div class="product-image-container">
                    <img src="../../assets/img/products/conquest-chrono.jpg" alt="<?php echo htmlspecialchars($productsInfo[306]['nom'] ?? 'Conquest Chrono'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="306">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[306]) && $productsInfo[306]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[306]['nom'] ?? 'Conquest Chrono'); ?></h3>
                    
                    <?php if (isset($productsInfo[306]) && !empty($productsInfo[306]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[306]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[306]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[306]) ? number_format($productsInfo[306]['prix'], 0, ',', ' ').' €' : '14 200 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(306); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(306)): ?>
                            <button class="add-to-cart-btn" data-product-id="306">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="306" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="306" aria-label="Ajouter aux favoris">
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
            <!-- Produit 1 Femme -->
            <div class="product-card" data-product-id="307">
                <div class="product-image-container">
                    <img src="../../assets/img/products/lady-diver.jpg" alt="<?php echo htmlspecialchars($productsInfo[307]['nom'] ?? 'Lady Diver'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="307">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[307]) && $productsInfo[307]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[307]['nom'] ?? 'Lady Diver'); ?></h3>
                    
                    <?php if (isset($productsInfo[307]) && !empty($productsInfo[307]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[307]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[307]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[307]) ? number_format($productsInfo[307]['prix'], 0, ',', ' ').' €' : '7 900 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(307); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(307)): ?>
                            <button class="add-to-cart-btn" data-product-id="307">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="307" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="307" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 2 Femme -->
            <div class="product-card" data-product-id="308">
                <div class="product-image-container">
                    <img src="../../assets/img/products/sport-elegance.jpg" alt="<?php echo htmlspecialchars($productsInfo[308]['nom'] ?? 'Sport Élégance'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="308">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[308]) && $productsInfo[308]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[308]['nom'] ?? 'Sport Élégance'); ?></h3>
                    
                    <?php if (isset($productsInfo[308]) && !empty($productsInfo[308]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[308]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[308]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[308]) ? number_format($productsInfo[308]['prix'], 0, ',', ' ').' €' : '8 400 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(308); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(308)): ?>
                            <button class="add-to-cart-btn" data-product-id="308">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="308" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="308" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 3 Femme -->
            <div class="product-card" data-product-id="309">
                <div class="product-image-container">
                    <img src="../../assets/img/products/aquatic-rose.jpg" alt="<?php echo htmlspecialchars($productsInfo[309]['nom'] ?? 'Aquatic Rose'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="309">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[309]) && !empty($productsInfo[309]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[309]['prix_promo'] / $productsInfo[309]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[309]['nom'] ?? 'Aquatic Rose'); ?></h3>
                    
                    <?php if (isset($productsInfo[309]) && !empty($productsInfo[309]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[309]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[309]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[309]) ? number_format($productsInfo[309]['prix'], 0, ',', ' ').' €' : '9 200 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(309); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(309)): ?>
                            <button class="add-to-cart-btn" data-product-id="309">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="309" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="309" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 4 Femme -->
            <div class="product-card" data-product-id="310">
                <div class="product-image-container">
                    <img src="../../assets/img/products/diamond-sport.jpg" alt="<?php echo htmlspecialchars($productsInfo[310]['nom'] ?? 'Diamond Sport'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="310">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[310]) && $productsInfo[310]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[310]['nom'] ?? 'Diamond Sport'); ?></h3>
                    
                    <?php if (isset($productsInfo[310]) && !empty($productsInfo[310]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[310]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[310]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[310]) ? number_format($productsInfo[310]['prix'], 0, ',', ' ').' €' : '16 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(310); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(310)): ?>
                            <button class="add-to-cart-btn" data-product-id="310">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="310" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="310" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 5 Femme -->
            <div class="product-card" data-product-id="311">
                <div class="product-image-container">
                    <img src="../../assets/img/products/chrono-lady.jpg" alt="<?php echo htmlspecialchars($productsInfo[311]['nom'] ?? 'Chrono Lady'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="311">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[311]) && $productsInfo[311]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[311]['nom'] ?? 'Chrono Lady'); ?></h3>
                    
                    <?php if (isset($productsInfo[311]) && !empty($productsInfo[311]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[311]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[311]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[311]) ? number_format($productsInfo[311]['prix'], 0, ',', ' ').' €' : '9 600 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(311); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(311)): ?>
                            <button class="add-to-cart-btn" data-product-id="311">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="311" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="311" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 6 Femme -->
            <div class="product-card" data-product-id="312">
                <div class="product-image-container">
                    <img src="../../assets/img/products/athletic-grace.jpg" alt="<?php echo htmlspecialchars($productsInfo[312]['nom'] ?? 'Athletic Grace'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="312">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[312]) && $productsInfo[312]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[312]['nom'] ?? 'Athletic Grace'); ?></h3>
                    
                    <?php if (isset($productsInfo[312]) && !empty($productsInfo[312]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[312]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[312]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[312]) ? number_format($productsInfo[312]['prix'], 0, ',', ' ').' €' : '7 900 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(312); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(312)): ?>
                            <button class="add-to-cart-btn" data-product-id="312">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="312" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="312" aria-label="Ajouter aux favoris">
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

<!-- Section caractéristiques techniques -->
<section class="technical-features">
    <div class="container">
        <h2 class="section-title">Caractéristiques de notre Collection Sport</h2>
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                        <path d="M12 12h.01"></path>
                    </svg>
                </div>
                <h3>Étanchéité</h3>
                <p>Étanche jusqu'à 300 mètres, idéale pour la plongée et les sports nautiques.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3>Chronographe</h3>
                <p>Précision au 1/10e de seconde avec fonctions chronométriques avancées.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 18L12 12 6 6"></path>
                        <path d="M18 6L12 12 18 18"></path>
                    </svg>
                </div>
                <h3>Résistance aux chocs</h3>
                <p>Construction robuste avec système d'absorption des impacts pour une durabilité exceptionnelle.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                </div>
                <h3>Luminosité optimale</h3>
                <p>Cadran et aiguilles luminescents pour une parfaite lisibilité dans toutes les conditions.</p>
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
            $recommendedIds = [101, 104, 201, 306];
            $recommendedInfo = getProductsInfoBatch($recommendedIds);
            
            foreach($recommendedIds as $recId):
                if(isset($recommendedInfo[$recId])):
            ?>
            <div class="recommendation-item">
                <img src="../../assets/img/products/<?php echo $recId; ?>.jpg" alt="<?php echo htmlspecialchars($recommendedInfo[$recId]['nom']); ?>" loading="lazy">
                <h3><?php echo htmlspecialchars($recommendedInfo[$recId]['nom']); ?></h3>
                
                <?php if (isset($recommendedInfo[$recId]) && !empty($recommendedInfo[$recId]['prix_promo'])): ?>
                    <p class="product-price">
                        <span class="price-old"><?php echo number_format($recommendedInfo[$recId]['prix'], 0, ',', ' '); ?> €</span> 
                        <?php echo number_format($recommendedInfo[$recId]['prix_promo'], 0, ',', ' '); ?> €
                    </p>
                <?php else: ?>
                    <p><?php echo number_format($recommendedInfo[$recId]['prix'], 0, ',', ' '); ?> €</p>
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

<!-- Modal d'aperçu rapide -->
<div id="quick-view-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-product-title"></h2>
            <button class="close-modal" aria-label="Fermer">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-product-image">
                <img id="modal-product-image" src="" alt="">
            </div>
            <div class="modal-product-details">
                <p id="modal-product-price" class="modal-price"></p>
                <div class="modal-product-description">
                    <p>Les montres de notre Collection Sport sont conçues pour les amateurs de performances et d'aventures. Étanches, robustes et précises, elles allient technologie de pointe et design dynamique. Leur mouvement à haute fréquence et leur boîtier en matériaux composites de dernière génération garantissent une fiabilité exceptionnelle dans toutes les situations.</p>
                </div>
                <div class="modal-product-actions">
                    <button id="modal-add-to-cart" class="btn-primary">Ajouter au panier</button>
                    <a id="modal-view-details" class="btn-outline">Voir les détails</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include($relativePath . '/includes/footer.php');
?>

<!-- Scripts chargés à la fin pour optimiser le chargement -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/collection-sorting.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-detail.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-filters.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/quick-view.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/wishlist-manager.js"></script>

</body>
</html>