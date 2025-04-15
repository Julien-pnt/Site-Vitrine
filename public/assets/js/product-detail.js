/**
 * Module de gestion des détails produit et aperçus rapides
 * Peut être utilisé de deux façons:
 * 1. Redirection vers une page dédiée (product-detail.php)
 * 2. Affichage dans un modal sur la même page
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Module product-detail chargé');
    
    // Configuration - peut être modifiée selon les besoins
    const config = {
        useModal: false, // true = afficher modal, false = rediriger vers page dédiée
        detailPageUrl: '../products/product-detail.php', // URL de redirection
        modalId: 'quick-view-modal' // ID du modal (s'il est utilisé)
    };
    
    // Initialiser les éléments
    initQuickViewButtons();
    
    /**
     * Initialise les boutons d'aperçu rapide
     */
    function initQuickViewButtons() {
        const quickViewButtons = document.querySelectorAll('.quick-view-btn');
        
        if (quickViewButtons.length === 0) {
            console.log('Aucun bouton d\'aperçu rapide trouvé');
            return;
        }
        
        quickViewButtons.forEach(button => {
            button.addEventListener('click', handleQuickView);
        });
        
        console.log(`${quickViewButtons.length} boutons d'aperçu rapide initialisés`);
        
        // Initialiser également la fermeture des modals si on utilise cette option
        if (config.useModal) {
            initModalClosers();
        }
    }
    
    /**
     * Gère le clic sur un bouton d'aperçu rapide
     */
    function handleQuickView(e) {
        e.preventDefault();
        const productId = this.getAttribute('data-product-id');
        
        if (!productId) {
            console.error('ID de produit manquant');
            return;
        }
        
        if (config.useModal) {
            // Option 1: Afficher les détails dans un modal
            openProductModal(productId, this);
        } else {
            // Option 2: Rediriger vers la page de détail
            redirectToProductPage(productId);
        }
    }
    
    /**
     * Redirige vers la page de détail du produit
     */
    function redirectToProductPage(productId) {
        const url = `${config.detailPageUrl}?id=${productId}`;
        console.log(`Redirection vers ${url}`);
        window.location.href = url;
    }
    
    /**
     * Ouvre le modal avec les détails du produit
     */
    function openProductModal(productId, clickedButton) {
        const modal = document.getElementById(config.modalId);
        if (!modal) {
            console.error(`Modal avec ID ${config.modalId} non trouvé`);
            return;
        }
        
        // Récupérer les informations du produit depuis le DOM
        const productCard = clickedButton.closest('.product-card');
        if (!productCard) {
            console.error('Carte produit non trouvée');
            return;
        }
        
        const title = productCard.querySelector('.product-title').textContent;
        const price = productCard.querySelector('.product-price').textContent;
        const imageSrc = productCard.querySelector('.product-image').getAttribute('src');
        
        // Mettre à jour le contenu du modal
        updateModalContent(modal, {
            id: productId,
            title: title,
            price: price,
            imageSrc: imageSrc
        });
        
        // Afficher le modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Désactiver le défilement de la page
    }
    
    /**
     * Met à jour le contenu du modal avec les détails du produit
     */
    function updateModalContent(modal, product) {
        const titleElement = modal.querySelector('#modal-product-title');
        const priceElement = modal.querySelector('#modal-product-price');
        const imageElement = modal.querySelector('#modal-product-image');
        const addToCartButton = modal.querySelector('#modal-add-to-cart');
        
        if (titleElement) titleElement.textContent = product.title;
        if (priceElement) priceElement.textContent = product.price;
        if (imageElement) {
            imageElement.setAttribute('src', product.imageSrc);
            imageElement.setAttribute('alt', product.title);
        }
        
        // Configuration du bouton d'ajout au panier
        if (addToCartButton) {
            // Supprimer les gestionnaires d'événements précédents
            const newButton = addToCartButton.cloneNode(true);
            addToCartButton.parentNode.replaceChild(newButton, addToCartButton);
            
            // Ajouter l'ID du produit
            newButton.setAttribute('data-product-id', product.id);
            
            // Ajouter le gestionnaire d'événement
            newButton.addEventListener('click', function() {
                // Si le module de panier est disponible, utiliser ses fonctions
                if (window.cartFunctions) {
                    const priceValue = parseFloat(product.price.replace(/[^0-9,.-]+/g, '').replace(',', '.'));
                    window.cartFunctions.addToCart(product.id, product.title, 1, priceValue, product.imageSrc);
                    window.cartFunctions.showNotification('Produit ajouté au panier');
                } else {
                    console.error('Module de panier non disponible');
                }
                
                // Fermer le modal
                modal.style.display = 'none';
                document.body.style.overflow = '';
            });
        }
    }
    
    /**
     * Initialise les gestionnaires pour fermer le modal
     */
    function initModalClosers() {
        const modal = document.getElementById(config.modalId);
        if (!modal) return;
        
        // Fermer avec le bouton X
        const closeButton = modal.querySelector('.close-modal');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            });
        }
        
        // Fermer en cliquant en dehors du modal
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    }
    
    // Exporter les fonctions pour les rendre accessibles depuis d'autres scripts
    window.productDetailFunctions = {
        openProductModal,
        redirectToProductPage,
        initQuickViewButtons
    };
});

/**
 * Initialise toutes les fonctionnalités liées aux produits
 */
function initProductFunctionality() {
    initWishlistButtons();
    initFilterButtons();
    initSearchAndSort();
}

/**
 * Initialise les boutons d'ajout aux favoris
 */
function initWishlistButtons() {
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist-btn');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');
            const productId = this.getAttribute('data-product-id');
            
            // Appeler l'API pour ajouter/retirer des favoris si disponible
            try {
                fetch(`/Site-Vitrine/public/php/api/wishlist/toggle.php?id=${productId}`, {
                    method: 'POST',
                    credentials: 'include' 
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Utiliser la fonction de notification de cart.js
                        window.showNotification('Produit ' + 
                            (this.classList.contains('active') ? 'ajouté aux' : 'retiré des') + 
                            ' favoris', 'info');
                    }
                });
            } catch (e) {
                // Mode développement - simuler l'ajout aux favoris
                window.showNotification('Produit ' + 
                    (this.classList.contains('active') ? 'ajouté aux' : 'retiré des') + 
                    ' favoris', 'info');
            }
        });
    });
}

/**
 * Initialise les boutons de filtrage homme/femme
 */
function initFilterButtons() {
    const hommeButton = document.getElementById('homme-button');
    const femmeButton = document.getElementById('femme-button');
    const hommeProducts = document.getElementById('homme-products');
    const femmeProducts = document.getElementById('femme-products');
    
    if (hommeButton && femmeButton) {
        hommeButton.addEventListener('click', function() {
            hommeButton.classList.add('active');
            femmeButton.classList.remove('active');
            hommeProducts.classList.add('active');
            femmeProducts.classList.remove('active');
            hommeButton.setAttribute('aria-pressed', 'true');
            femmeButton.setAttribute('aria-pressed', 'false');
        });
        
        femmeButton.addEventListener('click', function() {
            femmeButton.classList.add('active');
            hommeButton.classList.remove('active');
            femmeProducts.classList.add('active');
            hommeProducts.classList.remove('active');
            femmeButton.setAttribute('aria-pressed', 'true');
            hommeButton.setAttribute('aria-pressed', 'false');
        });
    }
}

/**
 * Initialise la recherche et le tri des produits
 */
function initSearchAndSort() {
    const searchBar = document.querySelector('.search-bar');
    const sortSelect = document.getElementById('sort-by');
    
    if (searchBar) {
        searchBar.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const activeGrid = document.querySelector('.product-grid.active');
            if (!activeGrid) return;
            
            const products = activeGrid.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const title = product.querySelector('.product-title').textContent.toLowerCase();
                if (title.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const option = this.value;
            const activeGrid = document.querySelector('.product-grid.active');
            if (!activeGrid) return;
            
            const products = Array.from(activeGrid.querySelectorAll('.product-card'));
            
            products.sort((a, b) => {
                const priceA = parseFloat(a.querySelector('.product-price').textContent.replace(/[^0-9.-]+/g,""));
                const priceB = parseFloat(b.querySelector('.product-price').textContent.replace(/[^0-9.-]+/g,""));
                
                if (option === 'price-asc') {
                    return priceA - priceB;
                } else if (option === 'price-desc') {
                    return priceB - priceA;
                }
                return 0;
            });
            
            products.forEach(product => {
                activeGrid.appendChild(product);
            });
        });
    }
}

// Exporter les fonctions
window.initProductFunctionality = initProductFunctionality;
window.initWishlistButtons = initWishlistButtons;
window.initFilterButtons = initFilterButtons;
window.initSearchAndSort = initSearchAndSort;