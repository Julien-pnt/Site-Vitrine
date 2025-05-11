/**
 * Module de gestion du panier pour Elixir du Temps
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation du système d\'ajout au panier');
    
    // Variables globales
    const apiPath = '/Site-Vitrine/php/api/cart/';
    let isProcessing = false;
    
    // Initialiser les boutons d'ajout au panier
    initAddToCartButtons();
    
    // Ajouter un produit au panier
    function addToCart(productId, quantity = 1) {
        // Ajouter cette vérification pour éviter les appels multiples
        if (isProcessing) {
            console.log('Opération déjà en cours, abandon');
            return;
        }
        
        console.log(`Ajout du produit ${productId} au panier, quantité: ${quantity}`);
        isProcessing = true;
        
        // Animer le badge du panier
        const cartBadge = document.querySelector('.cart-badge');
        if (cartBadge) {
            cartBadge.classList.add('pulse');
            setTimeout(() => {
                cartBadge.classList.remove('pulse');
            }, 500);
        }
        
        // IMPORTANT: Utilisez add_to_cart.php avec underscore
        fetch(apiPath + 'add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            isProcessing = false;
            console.log('Réponse API:', data);
            
            if (data.success) {
                showNotification(data.message || 'Produit ajouté au panier', 'success');
                
                // Mise à jour du badge
                updateCartBadge(data.cartCount);
                
                // Mise à jour du dropdown si ouvert
                updateCartDropdown(data.cartContent, data.cartTotal);
            } else {
                showNotification(data.message || 'Erreur lors de l\'ajout au panier', 'error');
            }
        })
        .catch(error => {
            isProcessing = false;
            console.error('Erreur:', error);
            showNotification('Erreur de connexion au serveur', 'error');
        });
    }
    
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        // Ajouter un bouton de fermeture
        const closeButton = document.createElement('button');
        closeButton.className = 'close-notification';
        closeButton.innerHTML = '×';
        closeButton.addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        });
        
        // Ajouter le message
        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;
        
        notification.appendChild(closeButton);
        notification.appendChild(messageSpan);
        
        document.body.appendChild(notification);
        
        // Animation d'entrée
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Animation de sortie après 2 secondes
        setTimeout(() => {
            if (document.body.contains(notification)) {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 2000);
    }
    
    function updateCartBadge(count) {
        const cartBadges = document.querySelectorAll('.cart-badge');
        cartBadges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
    }
    
    function updateCartDropdown(items, total) {
        // Code pour mettre à jour le contenu du dropdown
        const totalElement = document.getElementById('cart-dropdown-total');
        if (totalElement) {
            totalElement.textContent = total || '0 €';
        }
        
        // Afficher/masquer les éléments selon l'état du panier
        const cartFooter = document.querySelector('.cart-dropdown-footer');
        if (cartFooter) {
            cartFooter.style.display = items && items.length > 0 ? 'block' : 'none';
        }
    }
    
    function initAddToCartButtons() {
        // Modifier cette ligne pour exclure le bouton du modal
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn:not(#modal-add-to-cart)');
        
        console.log(`Boutons d'ajout trouvés: ${addToCartButtons.length}`);
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                if (productId) {
                    addToCart(productId);
                }
            });
        });
    }

    function resetAllEventListeners() {
        // Sauvegarder une référence aux boutons
        const addButtons = document.querySelectorAll('.add-to-cart-btn, #modal-add-to-cart');
        
        // Remplacer chaque bouton par un clone pour supprimer tous les gestionnaires
        addButtons.forEach(btn => {
            const clone = btn.cloneNode(true);
            btn.parentNode.replaceChild(clone, btn);
        });
        
        // Réinitialiser les gestionnaires pour les boutons de produits
        initAddToCartButtons();
        
        // Réinitialiser le gestionnaire pour le bouton du modal
        const modalBtn = document.getElementById('modal-add-to-cart');
        if (modalBtn) {
            modalBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const productId = this.getAttribute('data-product-id');
                if (productId) {
                    console.log('Unique modal click handler:', productId);
                    addToCart(productId);
                }
            });
        }
    }

    // Appeler cette fonction au chargement
    resetAllEventListeners();
    
    // Exposer les fonctions publiques
    window.CartManager = {
        addToCart: addToCart,
        showNotification: showNotification,
        updateCartBadge: updateCartBadge
    };
});