/**
 * Script de gestion du tri et filtrage des produits dans les collections
 */
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const searchBar = document.querySelector('.search-bar');
    const sortSelect = document.getElementById('sort-by');
    const hommeButton = document.getElementById('homme-button');
    const femmeButton = document.getElementById('femme-button');
    const hommeProducts = document.getElementById('homme-products');
    const femmeProducts = document.getElementById('femme-products');
    
    // Initialiser les données de tri pour les produits existants
    initializeProductData();
    
    // Appliquer le tri initial par défaut
    sortProducts();
    
    // Événements de filtrage et tri
    if (searchBar) searchBar.addEventListener('input', filterProducts);
    if (sortSelect) sortSelect.addEventListener('change', sortProducts);
    
    // Gestion du filtre homme/femme
    if (hommeButton && femmeButton) {
        hommeButton.addEventListener('click', function() {
            switchCategory('homme');
            sortProducts(); // Réapplique le tri sur la nouvelle catégorie active
        });
        
        femmeButton.addEventListener('click', function() {
            switchCategory('femme');
            sortProducts(); // Réapplique le tri sur la nouvelle catégorie active
        });
    }
    
    /**
     * Initialise les attributs de données pour le tri sur tous les produits
     */
    function initializeProductData() {
        // Simuler des données de produits pour le tri
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach((card, index) => {
            // Récupérer le prix numérique pour les calculs
            const priceText = card.querySelector('.product-price').textContent;
            const price = parseFloat(priceText.replace(/[^0-9,.-]+/g, "").replace(",", "."));
            
            // Si les data-attributes n'existent pas déjà, créer des valeurs
            if (!card.hasAttribute('data-release-date')) {
                // Simuler des dates de sortie différentes (plus récents pour les premiers produits)
                const monthsAgo = index % 4;
                const releaseDate = new Date();
                releaseDate.setMonth(releaseDate.getMonth() - monthsAgo);
                card.setAttribute('data-release-date', releaseDate.toISOString());
            }
            
            if (!card.hasAttribute('data-popularity')) {
                // Simuler une popularité (inversement proportionnelle au prix)
                const popularity = Math.round(100 - (index * 5) % 50);
                card.setAttribute('data-popularity', popularity.toString());
            }
            
            // Stocker le prix pour faciliter le tri
            card.setAttribute('data-price', price.toString());
        });
    }
    
    /**
     * Change la catégorie active (homme/femme)
     */
    function switchCategory(category) {
        if (category === 'homme') {
            hommeButton.classList.add('active');
            femmeButton.classList.remove('active');
            hommeProducts.classList.add('active');
            femmeProducts.classList.remove('active');
            hommeButton.setAttribute('aria-pressed', 'true');
            femmeButton.setAttribute('aria-pressed', 'false');
        } else {
            femmeButton.classList.add('active');
            hommeButton.classList.remove('active');
            femmeProducts.classList.add('active');
            hommeProducts.classList.remove('active');
            femmeButton.setAttribute('aria-pressed', 'true');
            hommeButton.setAttribute('aria-pressed', 'false');
        }
    }
    
    /**
     * Filtre les produits en fonction de la recherche
     */
    function filterProducts() {
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
     * Trie les produits selon le critère sélectionné
     */
    function sortProducts() {
        const option = sortSelect.value;
        const activeGrid = document.querySelector('.product-grid.active');
        if (!activeGrid) return;
        
        const products = Array.from(activeGrid.querySelectorAll('.product-card'));
        
        products.sort((a, b) => {
            // Tri par nouveauté (date de sortie)
            if (option === 'nouveaute') {
                const dateA = new Date(a.getAttribute('data-release-date') || "2000-01-01");
                const dateB = new Date(b.getAttribute('data-release-date') || "2000-01-01");
                return dateB - dateA; // Plus récent en premier
            }
            // Tri par prix croissant
            else if (option === 'price-asc') {
                const priceA = parseFloat(a.getAttribute('data-price'));
                const priceB = parseFloat(b.getAttribute('data-price'));
                return priceA - priceB;
            }
            // Tri par prix décroissant
            else if (option === 'price-desc') {
                const priceA = parseFloat(a.getAttribute('data-price'));
                const priceB = parseFloat(b.getAttribute('data-price'));
                return priceB - priceA;
            }
            // Tri par popularité
            else if (option === 'popularity') {
                const popA = parseInt(a.getAttribute('data-popularity') || "0");
                const popB = parseInt(b.getAttribute('data-popularity') || "0");
                return popB - popA; // Plus populaire en premier
            }
            return 0;
        });
        
        // Réorganiser les produits dans le DOM
        products.forEach(product => {
            activeGrid.appendChild(product);
        });
    }
});