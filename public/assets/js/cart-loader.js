/**
 * Charge le contenu du panier au démarrage
 */
document.addEventListener('DOMContentLoaded', function() {
    // Détecter l'API path
    const apiPath = window.apiBasePath || '/Site-Vitrine/php/api';
    
    // Charger le contenu du panier
    fetch(`${apiPath}/cart/get-cart.php`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour le compteur
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cartCount;
                }
                
                // Mettre à jour le contenu du panier si disponible
                if (data.cartContent && typeof updateCartDropdown === 'function') {
                    updateCartDropdown(data.cartContent);
                }
            }
        })
        .catch(error => console.error('Erreur lors du chargement du panier:', error));
});