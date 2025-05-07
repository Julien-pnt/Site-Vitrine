/**
 * Fonctions minimales pour le panier - sans conflit avec header-functions.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ne pas initialiser si header-functions.js est déjà chargé
    if (window.headerFunctionsLoaded) {
        console.log("header-functions.js détecté, désactivation de cart.js");
        return;
    }
    
    console.log("Initialisation du panier via cart.js");
    
    // Configurez l'ouverture et la fermeture du panier
    initializeCartTemplate();
    
    // Marquer que cart.js est chargé
    window.cartJsLoaded = true;
});

/**
 * Initialise les écouteurs d'événements pour le template du panier
 */
function initializeCartTemplate() {
    // Configurez les boutons de quantité
    setupQuantityButtons();
    
    // Configurez les boutons de suppression
    setupRemoveButtons();
}

/**
 * Configure les gestionnaires pour les boutons de quantité
 */
function setupQuantityButtons() {
    const quantityButtons = document.querySelectorAll('.quantity-btn');
    if (!quantityButtons || quantityButtons.length === 0) return;
    
    quantityButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            const isIncrease = this.classList.contains('increase');
            
            // Appel de l'API pour mettre à jour la quantité
            fetch('../../php/api/cart/update_quantity.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    product_id: productId,
                    increase: isIncrease
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Rechargement simple de la page après modification
                    window.location.reload();
                } else {
                    alert(data.message || 'Erreur lors de la mise à jour de la quantité');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        });
    });
}

/**
 * Configure les gestionnaires pour les boutons de suppression
 */
function setupRemoveButtons() {
    const removeButtons = document.querySelectorAll('.remove-cart-item');
    if (!removeButtons || removeButtons.length === 0) return;
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            
            // Appel de l'API pour supprimer l'article
            fetch('../../php/api/cart/remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Rechargement simple de la page après suppression
                    window.location.reload();
                } else {
                    alert(data.message || 'Erreur lors de la suppression du produit');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        });
    });
}

/**
 * Configuration simplifiée pour la visibilité du panier
 */
document.addEventListener('DOMContentLoaded', function() {
    const cartIcon = document.querySelector('.cart-icon');
    const cartDropdown = document.querySelector('.cart-dropdown');
    
    if (cartIcon && cartDropdown) {
        cartIcon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle class directement
            cartDropdown.classList.toggle('show');
            console.log("Dropdown visible:", cartDropdown.classList.contains('show'));
            
            // Debug - afficher les styles calculés
            const computedStyle = window.getComputedStyle(cartDropdown);
            console.log("Style calculé - display:", computedStyle.display);
            console.log("Style calculé - opacity:", computedStyle.opacity);
            console.log("Style calculé - visibility:", computedStyle.visibility);
        });
        
        // Fermeture au clic extérieur
        document.addEventListener('click', function(e) {
            if (!cartIcon.contains(e.target) && !cartDropdown.contains(e.target)) {
                cartDropdown.classList.remove('show');
            }
        });
    }
});