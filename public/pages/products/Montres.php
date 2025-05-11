<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Nos Montres - Elixir du Temps";
$pageDescription = "Découvrez notre sélection de montres de luxe - Des garde-temps d'exception indépendants et exclusifs par Elixir du Temps.";

// Inclure les helpers pour les produits
require_once($relativePath . '/includes/product-helpers.php');

// Récupérer tous les produits depuis la base de données
$products = getProductsByPage('all'); // 'all' pourrait être un paramètre spécial pour récupérer tous les produits

// Obtenir juste les IDs pour la compatibilité avec les fonctions existantes
$productIds = array_map(function($product) { 
    return $product['id']; 
}, $products);

// Pré-charger tous les stocks pour optimiser les performances
$productsStock = loadProductsStockBatch($productIds);

// Organiser les produits par genre
$hommeProducts = [];
$femmeProducts = [];

foreach ($products as $product) {
    // Assurez-vous que ces catégories correspondent à vos IDs de catégorie réels
    // Supposons que categorie_id 1 = homme, 5 = femme
    if (isset($product['categorie_id']) && $product['categorie_id'] == 5) {
        $femmeProducts[] = $product;
    } else {
        $hommeProducts[] = $product;
    }
}

// CSS spécifique à cette page
$additionalCss = '
<link rel="stylesheet" href="../../assets/css/collections.css">
<link rel="stylesheet" href="../../assets/css/wishlist-buttons.css">
<!-- FontAwesome pour les icônes des images manquantes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    
    .no-image i {
        font-size: 3rem;
        color: #dee2e6;
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
            <?php foreach($products as $product): 
                // Déterminer le genre pour l'attribut data-gender
                $gender = (isset($product['categorie_id']) && $product['categorie_id'] == 5) ? 'femme' : 'homme';
            ?>
            <div class="product-card" data-gender="<?php echo $gender; ?>" data-product-id="<?php echo $product['id']; ?>">
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

        <!-- Les sections homme et femme resteront masquées/affichées via JavaScript -->
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
            // IDs pour les produits recommandés - Vous pouvez les définir de manière dynamique
            $recommendedIds = [901, 903, 905, 907]; // Gardez ces IDs ou utilisez des produits populaires de votre DB
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
                
                <?php if (isset($recommendedInfo[$recId]) && !empty($recommendedInfo[$recId]['prix_promo'])): ?>
                    <p class="product-price">
                        <span class="price-old"><?php echo number_format($recommendedInfo[$recId]['prix'], 0, ',', ' '); ?> €</span> 
                        <?php echo number_format($recommendedInfo[$recId]['prix_promo'], 0, ',', ' '); ?> €
                    </p>
                <?php else: ?>
                    <p><?php echo isset($recommendedInfo[$recId]['prix']) ? number_format($recommendedInfo[$recId]['prix'], 0, ',', ' ').' €' : ''; ?></p>
                <?php endif; ?>
                
                <a href="product-detail.php?id=<?php echo $recId; ?>" class="view-product">Découvrir</a>
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
                    // Rediriger vers product-detail.php au lieu de produit.php
                    window.location.href = 'product-detail.php?id=' + productId;
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

        // Ajouter cette partie pour gérer l'ajout au panier depuis le modal
        const modalAddToCartBtn = document.getElementById('modal-add-to-cart');
        if (modalAddToCartBtn) {
            // Supprimer les gestionnaires existants (au cas où)
            modalAddToCartBtn.replaceWith(modalAddToCartBtn.cloneNode(true));
            
            // Récupérer la référence au nouvel élément
            const newModalBtn = document.getElementById('modal-add-to-cart');
            
            newModalBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Important
                e.stopPropagation(); // Empêche la propagation
                
                const productId = this.getAttribute('data-product-id');
                if (productId && window.CartManager) {
                    console.log('Ajout depuis le modal, ID:', productId);
                    window.CartManager.addToCart(productId);
                }
                
                // Fermer le modal après l'ajout
                const modal = document.getElementById('quick-view-modal');
                if (modal) modal.style.display = 'none';
            });
        }

        // Dans Montres.php
        const modalCartBtn = document.getElementById('modal-cart-action');
    });
</script>