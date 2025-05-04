<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Nos Montres - Elixir du Temps";
$pageDescription = "Découvrez notre sélection de montres de luxe - Des garde-temps d'exception indépendants et exclusifs par Elixir du Temps.";

// Inclure les helpers pour les produits
require_once($relativePath . '/includes/product-helpers.php');

// Pré-charger tous les stocks pour optimiser les performances
$productIds = [901, 902, 903, 904, 905, 906, 907, 908, 909, 910]; // IDs des montres indépendantes
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

<!-- Video Background Hero Section -->
<div class="video-background">
    <video class="video-bg" autoplay muted loop playsinline id="background-video">
        <source src="../../assets/video/background.mp4" type="video/mp4">
        <!-- Fallback image si la vidéo ne se charge pas -->
    </video>
    <img src="../../assets/img/collections/montres-hero.jpg" alt="Nos Montres" class="fallback-img">
    <div class="video-overlay"></div>
    
    <!-- Hero content sur la vidéo -->
    <div class="collection-hero">
        <div class="collection-hero-content">
            <h1 class="collection-title">Nos Garde-Temps d'Exception</h1>
            <p class="collection-description">Explorez notre sélection de montres de haute horlogerie indépendantes. Des pièces uniques qui incarnent l'expertise et l'innovation d'Elixir du Temps.</p>
            <a href="#products" class="btn-primary">Découvrir nos montres</a>
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
                        <button id="all-button" class="gender-button active" aria-pressed="true">Tous</button>
                        <button id="homme-button" class="gender-button" aria-pressed="false">Homme</button>
                        <button id="femme-button" class="gender-button" aria-pressed="false">Femme</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section produits -->
<section class="products-section" id="products">
    <div class="container">
        <!-- Grille de produits mixte (visible par défaut) -->
        <div id="all-products" class="product-grid active">
            <!-- Produit 1 - Chronographe Indépendant -->
            <div class="product-card" data-gender="homme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/chronographe-independant.jpg" alt="<?php echo htmlspecialchars($productsInfo[901]['nom'] ?? 'Chronographe Indépendant'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="901">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[901]) && $productsInfo[901]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[901]['nom'] ?? 'Chronographe Indépendant'); ?></h3>
                    
                    <?php if (isset($productsInfo[901]) && !empty($productsInfo[901]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[901]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[901]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[901]) ? number_format($productsInfo[901]['prix'], 0, ',', ' ').' €' : '14 950 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(901); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(901)): ?>
                            <button class="add-to-cart-btn" data-product-id="901">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="901" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="901" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 2 - Tourbillon Élégant -->
            <div class="product-card" data-gender="homme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/tourbillon-elegant.jpg" alt="<?php echo htmlspecialchars($productsInfo[902]['nom'] ?? 'Tourbillon Élégant'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="902">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[902]) && $productsInfo[902]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[902]['nom'] ?? 'Tourbillon Élégant'); ?></h3>
                    
                    <?php if (isset($productsInfo[902]) && !empty($productsInfo[902]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[902]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[902]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[902]) ? number_format($productsInfo[902]['prix'], 0, ',', ' ').' €' : '32 750 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(902); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(902)): ?>
                            <button class="add-to-cart-btn" data-product-id="902">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="902" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="902" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
                
            <!-- Produit 3 - Grande Complication -->
            <div class="product-card" data-gender="homme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/grande-complication.jpg" alt="<?php echo htmlspecialchars($productsInfo[903]['nom'] ?? 'Grande Complication'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="903">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[903]) && $productsInfo[903]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[903]['nom'] ?? 'Grande Complication'); ?></h3>
                    
                    <?php if (isset($productsInfo[903]) && !empty($productsInfo[903]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[903]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[903]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[903]) ? number_format($productsInfo[903]['prix'], 0, ',', ' ').' €' : '54 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(903); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(903)): ?>
                            <button class="add-to-cart-btn" data-product-id="903">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="903" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="903" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
                
            <!-- Produit 4 - Phase de Lune -->
            <div class="product-card" data-gender="femme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/phase-lune.jpg" alt="<?php echo htmlspecialchars($productsInfo[904]['nom'] ?? 'Phase de Lune'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="904">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[904]) && $productsInfo[904]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[904]['nom'] ?? 'Phase de Lune'); ?></h3>
                    
                    <?php if (isset($productsInfo[904]) && !empty($productsInfo[904]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[904]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[904]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[904]) ? number_format($productsInfo[904]['prix'], 0, ',', ' ').' €' : '18 900 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(904); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(904)): ?>
                            <button class="add-to-cart-btn" data-product-id="904">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="904" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="904" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
                
            <!-- Produit 5 - Squelette Artistique -->
            <div class="product-card" data-gender="femme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/squelette-artistique.jpg" alt="<?php echo htmlspecialchars($productsInfo[905]['nom'] ?? 'Squelette Artistique'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="905">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[905]) && $productsInfo[905]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[905]) && !empty($productsInfo[905]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[905]['prix_promo'] / $productsInfo[905]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[905]['nom'] ?? 'Squelette Artistique'); ?></h3>
                    
                    <?php if (isset($productsInfo[905]) && !empty($productsInfo[905]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[905]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[905]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[905]) ? number_format($productsInfo[905]['prix'], 0, ',', ' ').' €' : '25 800 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(905); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(905)): ?>
                            <button class="add-to-cart-btn" data-product-id="905">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="905" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="905" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
                
            <!-- Produit 6 - Quantième Perpétuel -->
            <div class="product-card" data-gender="femme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/quantieme-perpetuel.jpg" alt="<?php echo htmlspecialchars($productsInfo[906]['nom'] ?? 'Quantième Perpétuel'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="906">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[906]) && $productsInfo[906]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[906]['nom'] ?? 'Quantième Perpétuel'); ?></h3>
                    
                    <?php if (isset($productsInfo[906]) && !empty($productsInfo[906]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[906]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[906]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[906]) ? number_format($productsInfo[906]['prix'], 0, ',', ' ').' €' : '35 600 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(906); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(906)): ?>
                            <button class="add-to-cart-btn" data-product-id="906">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="906" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="906" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
                
            <!-- Produit 7 - Réserve de Marche -->
            <div class="product-card" data-gender="homme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/reserve-marche.jpg" alt="<?php echo htmlspecialchars($productsInfo[907]['nom'] ?? 'Réserve de Marche'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="907">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[907]) && $productsInfo[907]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[907]['nom'] ?? 'Réserve de Marche'); ?></h3>
                    
                    <?php if (isset($productsInfo[907]) && !empty($productsInfo[907]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[907]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[907]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[907]) ? number_format($productsInfo[907]['prix'], 0, ',', ' ').' €' : '12 750 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(907); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(907)): ?>
                            <button class="add-to-cart-btn" data-product-id="907">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="907" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="907" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 8 - Chronomètre Certifié -->
            <div class="product-card" data-gender="homme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/chronometre-certifie.jpg" alt="<?php echo htmlspecialchars($productsInfo[908]['nom'] ?? 'Chronomètre Certifié'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="908">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[908]) && $productsInfo[908]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[908]['nom'] ?? 'Chronomètre Certifié'); ?></h3>
                    
                    <?php if (isset($productsInfo[908]) && !empty($productsInfo[908]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[908]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[908]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[908]) ? number_format($productsInfo[908]['prix'], 0, ',', ' ').' €' : '9 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(908); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(908)): ?>
                            <button class="add-to-cart-btn" data-product-id="908">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="908" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="908" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 9 - Montre de Poche Moderne -->
            <div class="product-card" data-gender="homme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/montre-poche.jpg" alt="<?php echo htmlspecialchars($productsInfo[909]['nom'] ?? 'Montre de Poche Moderne'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="909">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[909]) && $productsInfo[909]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[909]['nom'] ?? 'Montre de Poche Moderne'); ?></h3>
                    
                    <?php if (isset($productsInfo[909]) && !empty($productsInfo[909]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[909]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[909]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[909]) ? number_format($productsInfo[909]['prix'], 0, ',', ' ').' €' : '17 800 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(909); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(909)): ?>
                            <button class="add-to-cart-btn" data-product-id="909">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="909" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="909" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Produit 10 - Haute Précision -->
            <div class="product-card" data-gender="femme">
                <div class="product-image-container">
                    <img src="../../assets/img/products/haute-precision.jpg" alt="<?php echo htmlspecialchars($productsInfo[910]['nom'] ?? 'Haute Précision'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="910">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[910]) && $productsInfo[910]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[910]['nom'] ?? 'Haute Précision'); ?></h3>
                    
                    <?php if (isset($productsInfo[910]) && !empty($productsInfo[910]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[910]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[910]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[910]) ? number_format($productsInfo[910]['prix'], 0, ',', ' ').' €' : '16 200 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(910); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(910)): ?>
                            <button class="add-to-cart-btn" data-product-id="910">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="910" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="910" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section pour Homme products (initialement masquée) -->
        <div id="homme-products" class="product-grid">
            <!-- Les produits hommes seront filtrés par JavaScript -->
        </div>

        <!-- Section pour Femme products (initialement masquée) -->
        <div id="femme-products" class="product-grid">
            <!-- Les produits femmes seront filtrés par JavaScript -->
        </div>
    </div>
</section>

<!-- Section Découvrir nos collections -->
<section class="featured-collections">
    <div class="container">
        <h2 class="section-title">Découvrir nos collections</h2>
        <div class="collections-grid">
            <!-- Collection Classic -->
            <div class="collection-card fade-in">
                <div class="collection-image">
                    <img src="../../assets/img/collections/classic-collection.jpg" alt="Collection Classic" loading="lazy">
                </div>
                <div class="collection-info">
                    <h2>Collection Classic</h2>
                    <p>L'élégance intemporelle dans sa forme la plus pure. Notre collection Classic rend hommage aux traditions horlogères.</p>
                    <span class="price-range">À partir de 4 750 €</span>
                    <a href="<?php echo $relativePath; ?>/pages/collections/Collection-Classic.php" class="explore-button">Explorer</a>
                </div>
            </div>
            
            <!-- Collection Sport -->
            <div class="collection-card fade-in">
                <div class="collection-image">
                    <img src="../../assets/img/collections/sport-collection.jpg" alt="Collection Sport" loading="lazy">
                </div>
                <div class="collection-info">
                    <h2>Collection Sport</h2>
                    <p>Performance et style se rencontrent dans notre collection Sport, conçue pour les aventuriers modernes.</p>
                    <span class="price-range">À partir de 5 900 €</span>
                    <a href="<?php echo $relativePath; ?>/pages/collections/Collection-Sport.php" class="explore-button">Explorer</a>
                </div>
            </div>
            
            <!-- Collection Limitée -->
            <div class="collection-card fade-in">
                <div class="collection-image">
                    <img src="../../assets/img/collections/limited-collection.jpg" alt="Éditions Limitées" loading="lazy">
                </div>
                <div class="collection-info">
                    <h2>Éditions Limitées</h2>
                    <p>Des pièces exclusives en séries limitées, alliant innovation technique et design distinctif.</p>
                    <span class="price-range">À partir de 18 500 €</span>
                    <a href="<?php echo $relativePath; ?>/pages/collections/Collection-Limited-Edition.php" class="explore-button">Explorer</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Recommandations -->
<section class="recommendations">
    <div class="container">
        <h2 class="section-title">Nos Bestsellers</h2>
        <div class="recommendations-carousel">
            <?php
            // IDs pour les produits recommandés
            $recommendedIds = [901, 903, 905, 907];
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
<div id="quick-view-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-product-title">Titre de la montre</h2>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-product-image">
                <img id="modal-product-image" src="" alt="Image du produit">
            </div>
            <div class="modal-product-details">
                <h3 id="modal-product-collection">Collection</h3>
                <p id="modal-product-price" class="modal-price">Prix</p>
                <p id="modal-product-description" class="modal-product-description">
                    Description du produit.
                </p>
                <div class="modal-product-actions">
                    <button id="modal-add-to-cart" class="btn-primary">Ajouter au panier</button>
                    <a id="modal-view-details" href="#" class="btn-outline">Voir les détails</a>
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
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-filters.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/quick-view.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/wishlist-manager.js"></script>

<!-- Script spécifique à la page (qui ne fait pas doublon avec les modules) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des boutons d'aperçu rapide
        const quickViewButtons = document.querySelectorAll('.quick-view-btn');
        if (quickViewButtons.length) {
            quickViewButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.getAttribute('data-product-id');
                    window.location.href = '<?php echo $relativePath; ?>/produit.php?id=' + productId;
                });
            });
        }
        
        // Gestion des filtres par genre
        function setActiveGenderFilter(button) {
            // Retirer la classe active de tous les boutons
            document.querySelectorAll('.gender-button').forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('aria-pressed', 'false');
            });
            
            // Ajouter la classe active au bouton cliqué
            button.classList.add('active');
            button.setAttribute('aria-pressed', 'true');
        }
        
        const allProducts = document.querySelectorAll('.product-card');
        const allButton = document.getElementById('all-button');
        const hommeButton = document.getElementById('homme-button');
        const femmeButton = document.getElementById('femme-button');
        
        if (allButton) {
            allButton.addEventListener('click', function() {
                setActiveGenderFilter(this);
                
                // Afficher tous les produits
                allProducts.forEach(product => {
                    product.style.display = 'block';
                });
            });
        }
        
        if (hommeButton) {
            hommeButton.addEventListener('click', function() {
                setActiveGenderFilter(this);
                
                // Afficher seulement les produits hommes
                allProducts.forEach(product => {
                    const productGender = product.getAttribute('data-gender');
                    
                    if (productGender === 'homme') {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        }
        
        if (femmeButton) {
            femmeButton.addEventListener('click', function() {
                setActiveGenderFilter(this);
                
                // Afficher seulement les produits femmes
                allProducts.forEach(product => {
                    const productGender = product.getAttribute('data-gender');
                    
                    if (productGender === 'femme') {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        }
        
        // Animation pour les cards de collection
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        const fadeElements = document.querySelectorAll('.fade-in');
        fadeElements.forEach(element => {
            observer.observe(element);
        });
    });
</script>