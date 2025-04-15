/**
 * Gestion du modal d'aperçu rapide des produits
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Quick view module loaded');
    
    const quickViewButtons = document.querySelectorAll('.quick-view-btn');
    const modal = document.getElementById('quick-view-modal');
    const closeModalButton = document.querySelector('.close-modal');
    
    if (quickViewButtons.length) {
        quickViewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Option 1: Redirection vers la page de détail du produit
                const productId = this.getAttribute('data-product-id');
                window.location.href = '../products/product-detail.php?id=' + productId;
                
                /* Option 2: Afficher le modal (décommentez pour utiliser)
                if (modal) {
                    const productId = this.getAttribute('data-product-id');
                    const productCard = this.closest('.product-card');
                    const title = productCard.querySelector('.product-title').textContent;
                    const price = productCard.querySelector('.product-price').textContent;
                    const imageSrc = productCard.querySelector('.product-image').getAttribute('src');
                    
                    document.getElementById('modal-product-title').textContent = title;
                    document.getElementById('modal-product-price').textContent = price;
                    document.getElementById('modal-product-image').setAttribute('src', imageSrc);
                    document.getElementById('modal-product-image').setAttribute('alt', title);
                    
                    // Ajouter l'ID du produit au bouton d'ajout au panier
                    const addToCartBtn = document.getElementById('modal-add-to-cart');
                    if (addToCartBtn) {
                        addToCartBtn.setAttribute('data-product-id', productId);
                        addToCartBtn.addEventListener('click', function() {
                            const id = this.getAttribute('data-product-id');
                            const modalTitle = document.getElementById('modal-product-title').textContent;
                            const modalPriceText = document.getElementById('modal-product-price').textContent;
                            const modalPrice = parseFloat(modalPriceText.replace(/[^0-9,.-]+/g, '').replace(',', '.'));
                            const modalImage = document.getElementById('modal-product-image').getAttribute('src');
                            
                            // Vérifier si la fonction cartFunctions est disponible
                            if (window.cartFunctions) {
                                window.cartFunctions.checkProductStock(id, modalTitle, modalPrice, modalImage);
                            }
                            modal.style.display = 'none';
                        });
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
});

/**
 * Ouvre le modal avec les détails du produit
 */
function openQuickViewModal(productId) {
    const productCard = document.querySelector(`.product-card [data-product-id="${productId}"]`).closest('.product-card');
    const modal = document.getElementById('quick-view-modal');
    
    if (productCard && modal) {
        const title = productCard.querySelector('.product-title').textContent;
        const price = productCard.querySelector('.product-price').textContent;
        const imageSrc = productCard.querySelector('.product-image').getAttribute('src');
        
        document.getElementById('modal-product-title').textContent = title;
        document.getElementById('modal-product-price').textContent = price;
        document.getElementById('modal-product-image').setAttribute('src', imageSrc);
        document.getElementById('modal-product-image').setAttribute('alt', title);
        
        // Ajouter l'ID du produit au bouton d'ajout au panier
        const addToCartBtn = document.getElementById('modal-add-to-cart');
        if (addToCartBtn) {
            addToCartBtn.setAttribute('data-product-id', productId);
        }
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Exporter les fonctions pour les rendre accessibles globalement
window.quickViewFunctions = {
    openQuickViewModal
};