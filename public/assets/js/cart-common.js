/**
 * Fonctions communes pour la gestion du panier sur tout le site
 */
// Pour éviter les appels multiples
let isProcessing = false;

window.CartManager = {
    // Récupérer le panier depuis localStorage
    getCart: function() {
        return JSON.parse(localStorage.getItem('cart')) || [];
    },
    
    // Sauvegarder le panier dans localStorage
    saveCart: function(cart, refresh = false) {
        localStorage.setItem('cart', JSON.stringify(cart));
        this.updateCartUI(cart);
        
        // Synchroniser avec le serveur si l'utilisateur est connecté
        if (window.isLoggedIn) {
            this.syncCartWithServer(cart);
        }
        
        // Rafraîchir la page si demandé
        if (refresh) {
            setTimeout(function() {
                window.location.reload();
            }, 50);
        }
    },
    
    // Synchroniser le panier avec le serveur
    syncCartWithServer: function(cart) {
        fetch('/Site-Vitrine/php/api/cart/sync_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cart=${encodeURIComponent(JSON.stringify(cart))}`
        })
        .then(response => response.json())
        .catch(error => {
            console.error('Erreur de synchronisation:', error);
        });
    },

    // Synchroniser localStorage avec la session PHP
    syncFromServer: function() {
        // Si une requête est déjà en cours, ne pas en lancer une autre
        if (isProcessing) return;
        
        isProcessing = true;
        
        fetch('/Site-Vitrine/php/api/cart/get-header-cart.php')
            .then(response => response.json())
            .then(data => {
                isProcessing = false;
                if (data.success) {
                    // Mettre à jour le badge du panier
                    const cartBadges = document.querySelectorAll('.cart-badge');
                    cartBadges.forEach(badge => {
                        badge.textContent = data.cartCount;
                        badge.style.display = data.cartCount > 0 ? 'flex' : 'none';
                    });
                    
                    // Mettre à jour le dropdown du panier s'il est ouvert
                    const cartDropdown = document.querySelector('.cart-dropdown');
                    if (cartDropdown) {
                        const cartItemsContainer = cartDropdown.querySelector('.cart-dropdown-items');
                        const cartEmptyMessage = cartDropdown.querySelector('.cart-dropdown-empty');
                        const cartFooter = cartDropdown.querySelector('.cart-dropdown-footer');
                        
                        if (data.cartItems.length > 0) {
                            // Afficher les articles
                            cartItemsContainer.innerHTML = '';
                            cartItemsContainer.style.display = 'block';
                            cartEmptyMessage.style.display = 'none';
                            cartFooter.style.display = 'block';
                            
                            // Générer le HTML pour chaque article
                            data.cartItems.forEach(item => {
                                const cartItemHTML = `
                                    <div class="cart-item" data-product-id="${item.id}">
                                        <div class="cart-item-image">
                                            ${item.image 
                                                ? `<img src="/Site-Vitrine/uploads/products/${item.image.split('/').pop()}" alt="${item.name}">`
                                                : `<div class="no-image"><i class="fas fa-image"></i></div>`
                                            }
                                        </div>
                                        <div class="cart-item-details">
                                            <h4 class="cart-item-title">${item.name}</h4>
                                            <div class="cart-item-price">
                                                ${item.hasPromo 
                                                    ? `<span class="price-current">${this.formatPrice(item.price)} €</span>
                                                       <span class="price-old">${this.formatPrice(item.regularPrice)} €</span>`
                                                    : `<span class="price-current">${this.formatPrice(item.price)} €</span>`
                                                }
                                            </div>
                                            <div class="cart-item-quantity">
                                                <button class="quantity-btn decrease" data-product-id="${item.id}">-</button>
                                                <span class="quantity-value">${item.quantity}</span>
                                                <button class="quantity-btn increase" data-product-id="${item.id}">+</button>
                                            </div>
                                        </div>
                                        <button class="remove-cart-item" data-product-id="${item.id}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                `;
                                cartItemsContainer.innerHTML += cartItemHTML;
                            });
                            
                            // Mettre à jour le total
                            const totalElement = cartDropdown.querySelector('#cart-dropdown-total');
                            if (totalElement) {
                                totalElement.textContent = data.cartTotal;
                            }
                            
                            // Ajouter les écouteurs d'événements aux boutons
                            this.addCartItemListeners();
                        } else {
                            // Afficher le message panier vide
                            cartItemsContainer.style.display = 'none';
                            cartEmptyMessage.style.display = 'flex';
                            cartFooter.style.display = 'none';
                        }
                    }
                }
            })
            .catch(error => {
                isProcessing = false;
                console.error('Erreur lors de la récupération du panier:', error);
            });
    },
    
    // Mettre à jour l'interface utilisateur du panier
    updateCartUI: function(cart) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const cartBadge = document.querySelector('.cart-badge');
        
        if (cartBadge) {
            cartBadge.textContent = totalItems;
            cartBadge.style.display = totalItems > 0 ? 'flex' : 'none';
            animateCartBadge();
        }
        
        // Mettre à jour le contenu du dropdown du panier
        this.updateCartDropdown(cart);
    },
    
    // Mettre à jour le contenu du dropdown du panier
    updateCartDropdown: function(cart) {
        const container = document.querySelector('.cart-dropdown-items');
        const emptyMessage = document.querySelector('.cart-dropdown-empty');
        const totalElement = document.getElementById('cart-dropdown-total');
        
        if (!container) return;
        
        // Vider le conteneur actuel
        container.innerHTML = '';
        
        // Si le panier est vide
        if (!cart || cart.length === 0) {
            if (emptyMessage) emptyMessage.style.display = 'flex';
            if (container) container.style.display = 'none';
            if (totalElement) totalElement.textContent = '0,00 €';
            return;
        }
        
        // Masquer le message de panier vide
        if (emptyMessage) emptyMessage.style.display = 'none';
        if (container) container.style.display = 'block';
        
        // Calculer le total
        let cartTotal = 0;
        
        // Ajouter chaque article
        cart.forEach(item => {
            // Correction: utiliser prix promo si disponible
            const price = parseFloat(item.price);
            const regularPrice = item.hasPromo ? parseFloat(item.regularPrice) : price;
            const total = price * item.quantity;
            cartTotal += total;
            
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.setAttribute('data-product-id', item.id);
            
            // Créer le HTML pour l'article
            cartItem.innerHTML = `
                <div class="cart-item-image">
                    ${item.image 
                        ? `<img src="${item.image}" alt="${item.name}">`
                        : `<div class="no-image"><i class="fas fa-image"></i></div>`
                    }
                </div>
                <div class="cart-item-details">
                    <h4 class="cart-item-title">${item.name}</h4>
                    <div class="cart-item-price">
                        ${item.hasPromo 
                            ? `<span class="price-old">${item.regularPrice} €</span> <span class="price-current">${item.price} €</span>`
                            : `<span class="price-current">${item.price} €</span>`
                        }
                    </div>
                    <div class="cart-item-quantity">
                        <button class="quantity-btn decrease" data-product-id="${item.id}">-</button>
                        <span class="quantity-value">${item.quantity}</span>
                        <button class="quantity-btn increase" data-product-id="${item.id}">+</button>
                    </div>
                </div>
                <button class="remove-cart-item" data-product-id="${item.id}">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(cartItem);
        });
        
        // Mettre à jour le total
        if (totalElement) {
            totalElement.textContent = `${this.formatPrice(cartTotal)} €`;
        }
        
        // Ajouter les écouteurs d'événements pour les boutons de quantité
        this.addCartItemListeners();
    },
    
    // Ajouter les écouteurs d'événements aux boutons du panier
    addCartItemListeners: function() {
        const self = this;
        
        // Boutons d'augmentation
        document.querySelectorAll('.quantity-btn.increase').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                self.increaseQuantity(productId);
            });
        });
        
        // Boutons de diminution
        document.querySelectorAll('.quantity-btn.decrease').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                self.decreaseQuantity(productId);
            });
        });
        
        // Boutons de suppression
        document.querySelectorAll('.remove-cart-item').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                self.removeItem(productId);
            });
        });
    },
    
    // Augmenter la quantité d'un article
    increaseQuantity: function(productId) {
        const cart = this.getCart();
        const index = cart.findIndex(item => item.id === productId);
        
        if (index !== -1) {
            cart[index].quantity++;
            this.saveCart(cart);
        }
    },
    
    // Diminuer la quantité d'un article
    decreaseQuantity: function(productId) {
        const cart = this.getCart();
        const index = cart.findIndex(item => item.id === productId);
        
        if (index !== -1) {
            if (cart[index].quantity > 1) {
                cart[index].quantity--;
                this.saveCart(cart);
            } else {
                this.removeItem(productId);
            }
        }
    },
    
    // Supprimer un article du panier
    removeItem: function(productId) {
        // Si une requête est déjà en cours, ne pas en lancer une autre
        if (isProcessing) return;
        
        isProcessing = true;
        
        // Convertir en nombre si c'est une chaîne
        productId = parseInt(productId);
        
        const cart = this.getCart();
        const updatedCart = cart.filter(item => parseInt(item.id) !== productId);
        
        // Sauvegarder d'abord dans localStorage pour une réponse rapide
        localStorage.setItem('cart', JSON.stringify(updatedCart));
        this.updateCartUI(updatedCart);
        
        // Synchroniser avec le serveur
        fetch('/Site-Vitrine/php/api/cart/remove-item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            isProcessing = false;
            if (data.success) {
                // Si nous sommes sur la page panier.php, recharger la page
                if (window.location.pathname.includes('/pages/products/panier.php')) {
                    window.location.reload();
                } else {
                    // Sinon juste recharger la page actuelle pour mettre à jour le header
                    window.location.reload();
                }
            }
        })
        .catch(error => {
            isProcessing = false;
            console.error('Erreur lors de la suppression:', error);
            // Si l'erreur est grave, recharger quand même la page sur panier.php
            if (window.location.pathname.includes('/pages/products/panier.php')) {
                window.location.reload();
            }
        });
    },
    
    // Formater un prix
    formatPrice: function(price) {
        return Number(price).toFixed(2).replace('.', ',');
    },
    
    // Initialiser le panier
    init: function() {
        // D'abord, essayer de récupérer les données du serveur
        this.syncFromServer();
        
        // Ensuite, mettre à jour l'UI avec ce qui est dans localStorage
        // (comme fallback ou en attendant la réponse du serveur)
        this.updateCartUI(this.getCart());
        
        // Le reste du code reste inchangé...
        const cartIcon = document.querySelector('.cart-icon');
        const cartDropdown = document.querySelector('.cart-dropdown');
        
        if (cartIcon && cartDropdown) {
            cartIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                cartDropdown.classList.toggle('show');
            });
            
            // Fermer le panier en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!cartIcon.contains(e.target) && !cartDropdown.contains(e.target)) {
                    cartDropdown.classList.remove('show');
                }
            });
            
            // Bouton pour fermer le panier
            const closeButton = document.querySelector('.close-cart-dropdown');
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    cartDropdown.classList.remove('show');
                });
            }
        }
    }
};

// Animation du compteur quand on ajoute un produit
function animateCartBadge() {
    const badge = document.querySelector('.cart-badge');
    if (badge) {
        badge.classList.add('pulse');
        setTimeout(() => {
            badge.classList.remove('pulse');
        }, 500);
    }
}

// Ouverture/fermeture douce du dropdown
function toggleCartDropdown(show) {
    const dropdown = document.querySelector('.cart-dropdown');
    if (!dropdown) return;
    
    if (show) {
        dropdown.classList.add('active');
    } else {
        dropdown.classList.remove('active');
    }
}

// Ajouter ça à votre code existant
document.addEventListener('DOMContentLoaded', function() {
    const cartIcon = document.querySelector('.toggle-cart');
    const closeCart = document.querySelector('.close-cart-dropdown');
    
    if (cartIcon) {
        cartIcon.addEventListener('click', function(e) {
            e.preventDefault();
            
            const dropdown = document.querySelector('.cart-dropdown');
            const isActive = dropdown.classList.contains('active');
            
            toggleCartDropdown(!isActive);
        });
    }
    
    if (closeCart) {
        closeCart.addEventListener('click', function() {
            toggleCartDropdown(false);
        });
    }
    
    // Fermer le panier en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        const cartDropdown = document.querySelector('.cart-dropdown');
        const cartIcon = document.querySelector('.cart-icon');
        
        if (cartDropdown && cartDropdown.classList.contains('active') && 
            !cartDropdown.contains(e.target) && !cartIcon.contains(e.target)) {
            toggleCartDropdown(false);
        }
    });
});

// Initialiser le panier au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    window.CartManager.init();
});