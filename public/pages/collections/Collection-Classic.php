<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Collection Classic - Elixir du Temps";
$pageDescription = "Découvrez notre Collection Classic - Des montres élégantes qui incarnent l'excellence horlogère d'Elixir du Temps.";

// Inclure les helpers pour les produits
require_once($relativePath . '/includes/product-helpers.php');

// Corriger le chemin d'inclusion de la base de données
include_once '../../../php/config/database.php';

// Récupérer tous les produits associés à la collection Classic
$products = getProductsByPage('collection_classic');

// Extraire les IDs pour la compatibilité avec le code existant
$productIds = array_column($products, 'id');

// Pré-charger les stocks pour la compatibilité avec le code existant
$productsStock = loadProductsStockBatch($productIds);

// Pré-charger les informations des produits pour la compatibilité
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
    
    /* FontAwesome si pas déjà inclus */
    .no-image {
        width: 100%;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    .no-image i {
        font-size: 2.5rem;
        color: #dee2e6;
    }
    
    .recommendation-item .no-image {
        height: 120px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <?php
            // Filtrer les produits pour hommes (si vous avez un champ pour distinguer homme/femme)
            $hommeProducts = array_filter($products, function($product) {
                return isset($product['categorie_id']) && $product['categorie_id'] == 1; // Supposons que 1 = Homme
            });
            
            if (empty($hommeProducts)): 
            ?>
                <div class="no-products">
                    <p>Aucun produit disponible dans cette catégorie pour le moment.</p>
                </div>
            <?php 
            else:
                foreach($hommeProducts as $product):
            ?>
                <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                    <div class="product-image-container">
                        <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($product['image'])); ?>" 
                             alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                             class="product-image" loading="lazy">
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
            <?php 
                endforeach;
            endif; 
            ?>
        </div>

        <!-- Section pour Femme products -->
        <div id="femme-products" class="product-grid">
            <?php
            // Filtrer les produits pour femmes
            $femmeProducts = array_filter($products, function($product) {
                return isset($product['categorie_id']) && $product['categorie_id'] == 5; // Supposons que 5 = Femme
            });
            
            if (empty($femmeProducts)): 
            ?>
                <div class="no-products">
                    <p>Aucun produit disponible dans cette catégorie pour le moment.</p>
                </div>
            <?php 
            else:
                foreach($femmeProducts as $product):
            ?>
                <!-- Structure identique à celle des produits pour homme -->
                <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                    <div class="product-image-container">
                        <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($product['image'])); ?>" 
                             alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                             class="product-image" loading="lazy">
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
            <?php 
                endforeach;
            endif; 
            ?>
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
                <?php if (!empty($recommendedInfo[$recId]['image'])): ?>
                    <img src="<?php echo $relativePath; ?>/uploads/products/<?php echo htmlspecialchars(basename($recommendedInfo[$recId]['image'])); ?>" 
                         alt="<?php echo htmlspecialchars($recommendedInfo[$recId]['nom'] ?? 'Montre Elixir du Temps'); ?>" 
                         loading="lazy">
                <?php else: ?>
                    <div class="no-image"><i class="fas fa-image"></i></div>
                <?php endif; ?>
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