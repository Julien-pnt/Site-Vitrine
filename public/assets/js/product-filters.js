/**
 * Gestion des filtres, recherche et tri des produits
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Product filters loaded');
    
    // Filtrage par genre (homme/femme)
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
    
    // Recherche de produits
    const searchBar = document.querySelector('.search-bar');
    const sortSelect = document.getElementById('sort-by');
    
    if (searchBar) searchBar.addEventListener('input', filterProducts);
    if (sortSelect) sortSelect.addEventListener('change', sortProducts);
    
    // Gestion des boutons d'ajout aux favoris
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist-btn');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');
            const productId = this.getAttribute('data-product-id');
            // Vous pourriez implémenter ici l'ajout réel aux favoris
        });
    });
});

/**
 * Filtre les produits en fonction du terme de recherche
 */
function filterProducts() {
    const searchBar = document.querySelector('.search-bar');
    const searchTerm = searchBar.value.toLowerCase();
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
}

/**
 * Trie les produits en fonction de l'option sélectionnée
 */
function sortProducts() {
    const sortSelect = document.getElementById('sort-by');
    const option = sortSelect.value;
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
}

// Exporter les fonctions pour les rendre accessibles globalement
window.filterFunctions = {
    filterProducts,
    sortProducts
};