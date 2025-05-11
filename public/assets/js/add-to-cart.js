/**
 * Fichier: add-to-cart.js
 * Gère l'ajout de produits au panier
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation du système d\'ajout au panier');
    
    // Détecter l'API path
    const apiPath = window.apiBasePath || '/Site-Vitrine/php/api';
    console.log('Utilisation du chemin API:', apiPath);
    
    // Sélectionner tous les boutons d'ajout au panier
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    console.log('Boutons d\'ajout trouvés:', addToCartButtons.length);
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Vérifier que l'ID du produit existe
            const productId = this.dataset.productId;
            if (!productId) {
                console.error('Erreur: data-product-id manquant sur le bouton');
                window.showMinimalNotification('Erreur: ID produit manquant', 'error');
                return;
            }
            
            console.log('Ajout au panier du produit ID:', productId);
            
            // Animation du bouton
            this.classList.add('adding');
            
            // Appel AJAX pour ajouter au panier
            fetch(`${apiPath}/cart/add_to_cart.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                // Retirer l'animation du bouton
                this.classList.remove('adding');
                
                if (data.success) {
                    // Mettre à jour le localStorage également pour la compatibilité
                    const cartItem = {
                        id: productId,
                        name: data.productName,
                        price: data.productPrice,
                        promo_price: data.productPromoPrice || null,
                        image: data.productImage,
                        quantity: 1,
                        reference: data.productReference || `ELX-${productId}`
                    };
                    
                    // Mettre à jour le localStorage
                    const localCart = JSON.parse(localStorage.getItem('cart') || '[]');
                    const existingItemIndex = localCart.findIndex(item => item.id === productId);
                    
                    if (existingItemIndex > -1) {
                        localCart[existingItemIndex].quantity += 1;
                    } else {
                        localCart.push(cartItem);
                    }
                    
                    localStorage.setItem('cart', JSON.stringify(localCart));
                    
                    // Animation de succès
                    this.classList.add('success');
                    setTimeout(() => this.classList.remove('success'), 1000);
                    
                    // Mettre à jour le compteur
                    if (data.success) {
                        updateCartCounter(data.cartCount);
                    }
                    
                    // Mettre à jour le contenu du panier
                    if (data.cartContent) {
                        updateCartDropdown(data.cartContent);
                    }
                    
                    // Notifier l'utilisateur
                    window.showMinimalNotification('Produit ajouté au panier');
                    
                    // Ouvrir le panier
                    if (window.ElixirApp && window.ElixirApp.cart && window.ElixirApp.cart.open) {
                        window.ElixirApp.cart.open();
                    }
                } else {
                    // Gérer l'erreur
                    this.classList.add('error');
                    setTimeout(() => this.classList.remove('error'), 1000);
                    window.showMinimalNotification(data.message || 'Erreur lors de l\'ajout', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur de connexion:', error);
                this.classList.remove('adding');
                this.classList.add('error');
                setTimeout(() => this.classList.remove('error'), 1000);
                window.showMinimalNotification('Erreur de connexion à l\'API', 'error');
            });
        });
    });
    
    // Rendre la fonction de notification disponible globalement
    window.showMinimalNotification = function(message, type = 'success') {
        document.querySelectorAll('.cart-notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `cart-notification mini ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Assurer que les styles sont appliqués
        notification.style.position = 'fixed';
        notification.style.bottom = '20px';
        notification.style.right = '20px';
        notification.style.padding = '12px 20px';
        notification.style.borderRadius = '4px';
        notification.style.background = type === 'success' ? 'rgba(40, 167, 69, 0.9)' : 'rgba(220, 53, 69, 0.9)';
        notification.style.color = 'white';
        notification.style.zIndex = '9999';
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(10px)';
        notification.style.transition = 'all 0.3s ease';
        
        requestAnimationFrame(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(10px)';
                setTimeout(() => notification.remove(), 300);
            }, 1500);
        });
    };
    
    // Fonction pour mettre à jour le contenu du panier
    function updateCartDropdown(cartContent) {
        const container = document.querySelector('.cart-dropdown-items');
        if (!container) return;
        
        // Vider le contenu actuel
        container.innerHTML = '';
        
        // Si le panier est vide
        if (!cartContent || cartContent.length === 0) {
            const emptyMessage = document.querySelector('.cart-dropdown-empty');
            if (emptyMessage) {
                emptyMessage.style.display = 'flex';
            }
            container.style.display = 'none';
            return;
        }
        
        // Masquer le message de panier vide
        const emptyMessage = document.querySelector('.cart-dropdown-empty');
        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }
        container.style.display = 'block';
        
        // Ajouter chaque article
        cartContent.forEach(item => {
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.setAttribute('data-product-id', item.id);
            
            // Créer le HTML pour l'article
            cartItem.innerHTML = `
                <div class="cart-item-image">
                    ${item.image 
                        ? `<img src="/Site-Vitrine/uploads/products/${item.image}" alt="${item.name}">`
                        : `<div class="no-image"><i class="fas fa-image"></i></div>`
                    }
                </div>
                <div class="cart-item-details">
                    <h4 class="cart-item-title">${item.name}</h4>
                    <div class="cart-item-price">
                        ${item.promo_price 
                            ? `<span class="price-old">${item.price} €</span> <span class="price-current">${item.promo_price} €</span>`
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
    }
    
    // Fonction pour mettre à jour le compteur du panier
    function updateCartCounter(count) {
        const cartBadge = document.getElementById('cart-count');
        if (cartBadge) {
            cartBadge.textContent = count;
            
            // Animation pour mettre en évidence le changement
            cartBadge.classList.add('pulse');
            setTimeout(() => cartBadge.classList.remove('pulse'), 500);
        }
    }
});