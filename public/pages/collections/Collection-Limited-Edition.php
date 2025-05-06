<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Collection Limitée - Elixir du Temps";
$pageDescription = "Des créations exceptionnelles et rares, chacune numérotée et produite en série limitée. Découvrez l'exclusivité à son apogée avec notre collection la plus prestigieuse.";

// Inclure les helpers pour les produits
require_once($relativePath . '/includes/product-helpers.php');

// Récupérer tous les produits associés à la collection Limited Edition
$products = getProductsByPage('collection_limited');

// Séparer les produits homme et femme
$hommeProducts = [];
$femmeProducts = [];

foreach ($products as $product) {
    if ($product['categorie_id'] == 5) { // ID 5 = catégorie femme
        $femmeProducts[] = $product;
    } else {
        $hommeProducts[] = $product;
    }
}

// Récupérer les IDs de tous les produits pour précharger les stocks
$productIds = array_map(function($product) { 
    return $product['id']; 
}, $products);

// Pré-charger tous les stocks pour optimiser les performances
$productsStock = loadProductsStockBatch($productIds);

// Pré-charger les informations des produits
$productsInfo = [];
foreach ($products as $product) {
    $productsInfo[$product['id']] = $product;
}

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

    /* Style pour l\'affichage des images manquantes */
    .no-image {
        width: 100%;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .no-image i {
        font-size: 3rem;
        color: #dee2e6;
    }

    .recommendation-item .no-image {
        height: 150px;
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
            <?php foreach($hommeProducts as $product): ?>
            <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                <div class="product-image-container">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($product['image'])); ?>" 
                             alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                             class="product-image" 
                             loading="lazy">
                    <?php else: ?>
                        <div class="no-image"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="<?php echo $product['id']; ?>">Aperçu rapide</button>
                    </div>
                    <?php if ($product['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (!empty($product['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $product['prix_promo'] / $product['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($product['nom']); ?></h3>
                    
                    <?php if (!empty($product['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($product['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($product['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo number_format($product['prix'], 0, ',', ' '); ?> €</p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator($product['id']); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable($product['id'])): ?>
                            <button class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="<?php echo $product['id']; ?>" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="<?php echo $product['id']; ?>" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Section pour Femme products -->
        <div id="femme-products" class="product-grid">
            <?php foreach($femmeProducts as $product): ?>
            <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                <div class="product-image-container">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($product['image'])); ?>" 
                             alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                             class="product-image" 
                             loading="lazy">
                    <?php else: ?>
                        <div class="no-image"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="<?php echo $product['id']; ?>">Aperçu rapide</button>
                    </div>
                    <?php if ($product['nouveaute']): ?>
                        <div class="product-badge new">Nouveau</div>
                    <?php endif; ?>
                    <?php if (!empty($product['prix_promo'])): ?>
                        <div class="product-badge sale">-<?php echo round((1 - $product['prix_promo'] / $product['prix']) * 100); ?>%</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($product['nom']); ?></h3>
                    
                    <?php if (!empty($product['prix_promo'])): ?>
                        <p class="product-price">
                            <span class="price-old"><?php echo number_format($product['prix'], 0, ',', ' '); ?> €</span> 
                            <?php echo number_format($product['prix_promo'], 0, ',', ' '); ?> €
                        </p>
                    <?php else: ?>
                        <p class="product-price"><?php echo number_format($product['prix'], 0, ',', ' '); ?> €</p>
                    <?php endif; ?>
                    
                    <?php echo generateStockIndicator($product['id']); ?>
                    
                    <div class="product-actions">
                        <?php if (isProductAvailable($product['id'])): ?>
                            <button class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">Ajouter au panier</button>
                        <?php else: ?>
                            <button class="add-to-cart-btn disabled" data-product-id="<?php echo $product['id']; ?>" disabled>Indisponible</button>
                        <?php endif; ?>
                        
                        <button class="add-to-wishlist-btn" data-product-id="<?php echo $product['id']; ?>" aria-label="Ajouter aux favoris">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
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
                <?php if (!empty($recommendedInfo[$recId]['image'])): ?>
                    <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($recommendedInfo[$recId]['image'])); ?>" 
                         alt="<?php echo htmlspecialchars($recommendedInfo[$recId]['nom']); ?>" 
                         loading="lazy">
                <?php else: ?>
                    <div class="no-image"><i class="fas fa-image"></i></div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($recommendedInfo[$recId]['nom'] ?? ''); ?></h3>
                
                <?php if (!empty($recommendedInfo[$recId]['prix_promo'])): ?>
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