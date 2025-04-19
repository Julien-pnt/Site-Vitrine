/**
 * Gestion de la liste de favoris
 */
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    const clearWishlistBtn = document.getElementById('clear-wishlist');
    const confirmModal = document.getElementById('confirm-modal');
    const confirmClearBtn = document.getElementById('confirm-clear');
    const cancelClearBtn = document.getElementById('cancel-clear');
    const closeModalBtn = document.querySelector('.close-modal');
    
    // Fonction pour afficher/masquer le modal
    function toggleModal(show = true) {
        confirmModal.style.display = show ? 'flex' : 'none';
    }
    
    // Gestionnaires pour le modal
    if (clearWishlistBtn) {
        clearWishlistBtn.addEventListener('click', function() {
            toggleModal(true);
        });
    }
    
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            toggleModal(false);
        });
    }
    
    if (cancelClearBtn) {
        cancelClearBtn.addEventListener('click', function() {
            toggleModal(false);
        });
    }
    
    // Fermer le modal si on clique en dehors
    window.addEventListener('click', function(event) {
        if (event.target === confirmModal) {
            toggleModal(false);
        }
    });
    
    // Vider la liste de favoris
    if (confirmClearBtn) {
        confirmClearBtn.addEventListener('click', function() {
            clearWishlist();
        });
    }
    
    // Supprimer un produit des favoris
    document.querySelectorAll('.remove-from-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            removeFromWishlist(productId, this.closest('.wishlist-item'));
        });
    });
    
    // Ajouter au panier
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToCart(productId);
        });
    });
    
    // Demander une alerte de stock
    document.querySelectorAll('.notify-stock').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            notifyStock(productId);
        });
    });
    
    /**
     * Fonctions pour gérer les favoris
     */
    
    // Supprimer un produit des favoris
    function removeFromWishlist(productId, itemElement) {
        fetch('../../php/api/wishlist/manage.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Animation de suppression
                itemElement.style.opacity = '0';
                setTimeout(() => {
                    itemElement.style.height = '0';
                    itemElement.style.margin = '0';
                    itemElement.style.padding = '0';
                    itemElement.style.overflow = 'hidden';
                    
                    setTimeout(() => {
                        itemElement.remove();
                        
                        // Vérifier s'il reste des produits
                        if (document.querySelectorAll('.wishlist-item').length === 0) {
                            // Recharger la page pour afficher l'état vide
                            window.location.reload();
                        }
                    }, 300);
                }, 300);
                
                // Mettre à jour le compteur dans la sidebar si présent
                updateWishlistCounter();
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la communication avec le serveur');
        });
    }
    
    // Vider la liste de favoris
    function clearWishlist() {
        fetch('../../php/api/wishlist/manage.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=clear'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fermer le modal
                toggleModal(false);
                
                // Recharger la page
                window.location.reload();
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la communication avec le serveur');
        });
    }
    
    // Ajouter au panier
    function addToCart(productId) {
        // Utiliser la fonction existante si elle existe
        if (typeof window.addToCart === 'function') {
            window.addToCart(productId, 1);
            return;
        }
        
        // Sinon, implémentation basique
        fetch('../php/api/cart/add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=1`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Feedback visuel
                const button = document.querySelector(`.add-to-cart[data-id="${productId}"]`);
                if (button) {
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check"></i> Ajouté au panier';
                    button.classList.add('added');
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.classList.remove('added');
                    }, 2000);
                }
                
                // Mettre à jour le compteur du panier si présent
                if (document.getElementById('cart-count')) {
                    const currentCount = parseInt(document.getElementById('cart-count').textContent);
                    document.getElementById('cart-count').textContent = currentCount + 1;
                }
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la communication avec le serveur');
        });
    }
    
    // S'inscrire pour une alerte de stock
    function notifyStock(productId) {
        alert('Fonctionnalité à venir : vous serez alerté quand ce produit sera de nouveau en stock.');
        // Ici, vous pourriez implémenter une vraie inscription à une alerte
    }
    
    // Mettre à jour le compteur de favoris dans la sidebar
    function updateWishlistCounter() {
        fetch('../../php/api/wishlist/count.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && document.querySelector('.wishlist-count')) {
                    document.querySelector('.wishlist-count').textContent = data.count;
                }
            })
            .catch(error => console.error('Erreur:', error));
    }
});