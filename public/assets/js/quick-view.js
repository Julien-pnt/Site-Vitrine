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
    
    if (closeModalButton && modal) {
        closeModalButton.addEventListener('click', function() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
        
        // Fermer le modal en cliquant à l'extérieur
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
});

/**
 * Ouvre le modal avec les détails du produit
 */
function openQuickViewModal(productId) {
    const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
    const modal = document.getElementById('quick-view-modal');
    
    if (productCard && modal) {
        const title = productCard.querySelector('.product-title').textContent;
        const price = productCard.querySelector('.product-price').textContent;
        const imageSrc = productCard.querySelector('.product-image')?.getAttribute('src') || 
                          productCard.querySelector('.no-image i').getAttribute('class');
        
        // Assurez-vous d'utiliser les IDs avec tirets
        document.getElementById('modal-product-title').textContent = title;
        document.getElementById('modal-product-price').textContent = price;
        
        const imageElement = document.getElementById('modal-product-image');
        if (imageElement) {
            if (imageSrc.includes('.jpg') || imageSrc.includes('.png') || imageSrc.includes('.jpeg')) {
                imageElement.setAttribute('src', imageSrc);
                imageElement.setAttribute('alt', title);
                imageElement.style.display = 'block';
            } else {
                // Gérer le cas des images manquantes
                imageElement.style.display = 'none';
                const noImageContainer = document.createElement('div');
                noImageContainer.className = 'no-image';
                noImageContainer.innerHTML = '<i class="fas fa-image"></i>';
                imageElement.parentNode.appendChild(noImageContainer);
            }
        }
        
        // Ajouter l'ID du produit au bouton d'ajout au panier
        const addToCartBtn = document.getElementById('modal-add-to-cart');
        if (addToCartBtn) {
            addToCartBtn.setAttribute('data-product-id', productId);
            console.log(`ID produit défini sur le bouton modal: ${productId}`);
        }
        
        // Mise à jour du lien "Voir les détails"
        const viewDetailsLink = document.getElementById('modal-view-details');
        if (viewDetailsLink) {
            viewDetailsLink.href = `product-detail.php?id=${productId}`;
        }
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    } else {
        console.error(`Produit avec ID ${productId} non trouvé ou modal non disponible`);
    }
}

// Exporter la fonction pour la rendre accessible globalement
window.quickViewFunctions = {
    openQuickViewModal
};