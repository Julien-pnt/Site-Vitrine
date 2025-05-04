<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Collection Prestige - Elixir du Temps";
$pageDescription = "Découvrez notre Collection Prestige - Des montres d'exception au luxe incomparable par Elixir du Temps.";

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

// Inclure les helpers pour les produits
require_once($relativePath . '/includes/product-helpers.php');

// Pré-charger tous les stocks pour optimiser les performances
$productIds = [501, 502, 503, 504, 505, 601, 602, 603, 604, 605]; // IDs de tous vos produits
$productsStock = loadProductsStockBatch($productIds);

// Pré-charger les informations des produits
$productsInfo = getProductsInfoBatch($productIds);
?>

<!-- Video background et introduction -->
<div class="video-background">
    <video class="video-bg" autoplay muted loop playsinline preload="auto">
        <source src="../../assets/video/background.mp4" type="video/mp4">
        <!-- Fallback image si la vidéo ne se charge pas -->
        <img src="../../assets/img/collections/prestige-hero.jpg" alt="Collection Prestige" class="fallback-img">
    </video>
    <div class="video-overlay"></div>
    
    <!-- Hero content sur la vidéo -->
    <div class="collection-hero">
        <div class="collection-hero-content">
            <h1 class="collection-title">Collection Prestige</h1>
            <p class="collection-description">L'excellence à son apogée. Notre collection Prestige incarne le sommet du savoir-faire horloger, où chaque détail reflète notre quête incessante de perfection et d'excellence.</p>
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

            <!-- Produit 2 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="../../assets/img/products/majeste-eternelle.jpg" alt="Majesté Éternelle" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="502">Aperçu rapide</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Majesté Éternelle</h3>
                    <p class="product-price">45 000 €</p>
                    
                    <?php echo generateStockIndicator(502); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(502)): ?>
                            <button class="add-to-cart-btn" data-product-id="502">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="502" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="502" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 3 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="../../assets/img/products/grandeur-supreme.jpg" alt="Grandeur Suprême" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="503">Aperçu rapide</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Grandeur Suprême</h3>
                    <p class="product-price">38 500 €</p>
                    
                    <?php echo generateStockIndicator(503); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(503)): ?>
                            <button class="add-to-cart-btn" data-product-id="503">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="503" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="503" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 4 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="../../assets/img/products/accomplissement-royal.jpg" alt="Accomplissement Royal" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="504">Aperçu rapide</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Accomplissement Royal</h3>
                    <p class="product-price">52 000 €</p>
                    
                    <?php echo generateStockIndicator(504); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(504)): ?>
                            <button class="add-to-cart-btn" data-product-id="504">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="504" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="504" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 5 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="../../assets/img/products/perfection-ultime.jpg" alt="Perfection Ultime" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="505">Aperçu rapide</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Perfection Ultime</h3>
                    <p class="product-price">41 500 €</p>
                    
                    <?php echo generateStockIndicator(505); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(505)): ?>
                            <button class="add-to-cart-btn" data-product-id="505">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="505" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="505" aria-label="Ajouter aux favoris">
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
            <div class="product-card" data-product-id="601">
                <div class="product-image-container">
                    <img src="../../assets/img/products/splendeur-celeste.jpg" alt="<?php echo htmlspecialchars($productsInfo[601]['nom'] ?? 'Splendeur Céleste'); ?>" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="601">Aperçu rapide</button>
                    </div>
                    <?php if (isset($productsInfo[601]) && $productsInfo[601]['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (isset($productsInfo[601]) && !empty($productsInfo[601]['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $productsInfo[601]['prix_promo'] / $productsInfo[601]['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($productsInfo[601]['nom'] ?? 'Splendeur Céleste'); ?></h3>
                    
                    <?php if (isset($productsInfo[601]) && !empty($productsInfo[601]['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($productsInfo[601]['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($productsInfo[601]['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo isset($productsInfo[601]) ? number_format($productsInfo[601]['prix'], 0, ',', ' ').' €' : '36 500 €'; ?></p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator(601); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(601)): ?>
                            <button class="add-to-cart-btn" data-product-id="601">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="601" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="601" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 2 Femme -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="../../assets/img/products/delicatesse-majestueuse.jpg" alt="Délicatesse Majestueuse" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="602">Aperçu rapide</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Délicatesse Majestueuse</h3>
                    <p class="product-price">34 000 €</p>
                    
                    <?php echo generateStockIndicator(602); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(602)): ?>
                            <button class="add-to-cart-btn" data-product-id="602">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="602" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="602" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 3 Femme -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="../../assets/img/products/elegance-souveraine.jpg" alt="Élégance Souveraine" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="603">Aperçu rapide</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Élégance Souveraine</h3>
                    <p class="product-price">31 500 €</p>
                    
                    <?php echo generateStockIndicator(603); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(603)): ?>
                            <button class="add-to-cart-btn" data-product-id="603">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="603" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="603" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 4 Femme -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="../../assets/img/products/eclat-imperial.jpg" alt="Éclat Impérial" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="604">Aperçu rapide</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Éclat Impérial</h3>
                    <p class="product-price">45 000 €</p>
                    
                    <?php echo generateStockIndicator(604); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(604)): ?>
                            <button class="add-to-cart-btn" data-product-id="604">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="604" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="604" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Produit 5 Femme -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="../../assets/img/products/grace-absolue.jpg" alt="Grâce Absolue" class="product-image" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="605">Aperçu rapide</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Grâce Absolue</h3>
                    <p class="product-price">29 800 €</p>
                    
                    <?php echo generateStockIndicator(605); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable(605)): ?>
                            <button class="add-to-cart-btn" data-product-id="605">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="605" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="605" aria-label="Ajouter aux favoris">
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
            $recommendedIds = [101, 201, 301, 401];
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
                <div id="modal-stock-indicator"></div>
                <div class="modal-product-description">
                    <p id="modal-product-description"></p>
                </div>
                <div class="modal-product-actions">
                    <button id="modal-add-to-cart" class="btn-primary">Ajouter au panier</button>
                    <a id="modal-view-details" class="btn-outline" href="#">Voir les détails</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts chargés à la fin pour optimiser le chargement -->
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


<?php
// Inclure le footer
include($relativePath . '/includes/footer.php');
?>
</body>
</html>