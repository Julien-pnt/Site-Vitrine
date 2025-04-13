/**
 * Gestion des aperçus rapides et détails des produits
 * Fonctionne en complément de cart.js pour les fonctionnalités liées aux produits
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser toutes les fonctionnalités relatives aux produits
    initProductFunctionality();
});

/**
 * Initialise toutes les fonctionnalités liées aux produits
 */
function initProductFunctionality() {
    initQuickViewButtons();
    initWishlistButtons();
    initFilterButtons();
    initSearchAndSort();
}

/**
 * Initialise les boutons d'aperçu rapide
 */
function initQuickViewButtons() {
    const quickViewButtons = document.querySelectorAll('.quick-view-btn');
    const modal = document.getElementById('quick-view-modal');
    const closeModalButton = document.querySelector('.close-modal');
    
    if (quickViewButtons.length) {
        quickViewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const productId = this.getAttribute('data-product-id');
                
                // Par défaut, rediriger vers la page détail
                window.location.href = '../products/product-detail.php?id=' + productId;
                
                /* Alternative : afficher un modal (décommenter pour activer)
                if (modal) {
                    const productCard = this.closest('.product-card');
                    const title = productCard.querySelector('.product-title').textContent;
                    const price = productCard.querySelector('.product-price').textContent;
                    const imageSrc = productCard.querySelector('.product-image').getAttribute('src');
                    
                    document.getElementById('modal-product-title').textContent = title;
                    document.getElementById('modal-product-price').textContent = price;
                    document.getElementById('modal-product-image').setAttribute('src', imageSrc);
                    document.getElementById('modal-product-image').setAttribute('alt', title);
                    
                    // Configurer le bouton d'ajout au panier dans le modal
                    const addToCartBtn = document.getElementById('modal-add-to-cart');
                    if (addToCartBtn) {
                        addToCartBtn.setAttribute('data-product-id', productId);
                        // La fonction handleAddToCartClick de cart.js est utilisée ici
                    }
                    
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
                */
            });
        });
    }
    
    // Fermeture du modal
    if (closeModalButton && modal) {
        closeModalButton.addEventListener('click', function() {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        });
        
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    }
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
window.initQuickViewButtons = initQuickViewButtons;
window.initWishlistButtons = initWishlistButtons;
window.initFilterButtons = initFilterButtons;
window.initSearchAndSort = initSearchAndSort;