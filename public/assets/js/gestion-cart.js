/**
 * Système de gestion du panier pour Elixir du Temps
 * Ce fichier contient toutes les fonctionnalités nécessaires pour gérer le panier d'achat.
 */

// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart.js chargé');
    
    // Initialiser l'affichage du panier
    updateCartDisplay();
    
    // Configuration du dropdown du panier
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        // Supprimer le lien direct transparent qui bloque les clics
        const directLink = cartIcon.querySelector('.cart-direct-link');
        if (directLink) {
            directLink.remove();
        }
        
        // Gestionnaire d'événement simple sur l'icône
        cartIcon.addEventListener('click', function(e) {
            // Toujours arrêter la propagation pour éviter la fermeture immédiate
            e.stopPropagation();
            
            // Vérifier si le clic est sur un bouton ou lien du dropdown
            if (e.target.closest('.cart-dropdown-button')) {
                // Ne rien faire, laisser le lien fonctionner naturellement
                return;
            }
            
            // Sinon, toggle le dropdown
            const dropdown = this.querySelector('.cart-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
                console.log('Dropdown toggled:', dropdown.classList.contains('show'));
            }
        });
    }
    
    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cart-dropdown') && !e.target.closest('.cart-icon')) {
            const dropdown = document.querySelector('.cart-dropdown.show');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        }
    });
    
    // Gestion des boutons d'ajout au panier
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        if (!button.closest('#quick-view-modal')) { // Évite les boutons dans le modal
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productCard = this.closest('.product-card');
                const productName = productCard.querySelector('.product-title').textContent;
                const productPriceText = productCard.querySelector('.product-price').textContent;
                const productPrice = parseFloat(productPriceText.replace(/[^0-9,.-]+/g, '').replace(',', '.'));
                const productImage = productCard.querySelector('.product-image').getAttribute('src');
                
                checkProductStock(productId, productName, productPrice, productImage);
            });
        }
    });
    
    // Gestion des boutons d'ajout dans le modal d'aperçu rapide
    const modalAddToCartBtn = document.getElementById('modal-add-to-cart');
    if (modalAddToCartBtn) {
        modalAddToCartBtn.addEventListener('click', function() {
            const id = this.getAttribute('data-product-id');
            const modalTitle = document.getElementById('modal-product-title').textContent;
            const modalPriceText = document.getElementById('modal-product-price').textContent;
            const modalPrice = parseFloat(modalPriceText.replace(/[^0-9,.-]+/g, '').replace(',', '.'));
            const modalImage = document.getElementById('modal-product-image').getAttribute('src');
            
            checkProductStock(id, modalTitle, modalPrice, modalImage);
            
            // Fermer le modal après ajout
            const modal = document.getElementById('quick-view-modal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    }
    
    // Gestion des boutons de suppression d'articles existants
    document.querySelectorAll('.cart-item-remove').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            removeFromCart(productId);
        });
    });
});

/**
 * Vérifie la disponibilité en stock et ajoute au panier
 */
function checkProductStock(productId, productName, productPrice, productImage) {
    console.log('Vérification stock pour:', productName); 
    
    // Pour éviter l'ajout en double, on vérifie si on a déjà effectué cette action récemment
    const lastAddTime = parseInt(localStorage.getItem(`last_add_${productId}`)) || 0;
    const now = Date.now();
    if (now - lastAddTime < 2000) { // Ignorer les clics multiples dans un intervalle de 2 secondes
        console.log('Action ignorée - trop rapide');
        return;
    }
    localStorage.setItem(`last_add_${productId}`, now);
    
    // Ajouter directement au panier (sans vérification de stock pour simplifier)
    addToCart(productId, productName, 1, productPrice, productImage);
    showNotification('Produit ajouté au panier');
}

/**
 * Ajoute un produit au panier
 */
function addToCart(productId, productName, quantity, price, imageSrc) {
    // Récupérer le panier existant ou créer un nouveau
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Vérifier si l'article existe déjà dans le panier
    const existingItemIndex = cart.findIndex(item => item.id === productId);
    
    if (existingItemIndex > -1) {
        // Incrémenter la quantité si le produit existe déjà
        cart[existingItemIndex].quantity += quantity;
    } else {
        // Ajouter le nouvel article
        cart.push({
            id: productId,
            name: productName,
            price: price,
            quantity: quantity,
            image: imageSrc
        });
    }
    
    // Sauvegarder le panier mis à jour
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Mettre à jour l'affichage
    updateCartDisplay();
}

/**
 * Met à jour l'affichage du panier dans l'interface
 */
function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartBadge = document.querySelector('.cart-badge');
    const cartDropdownItems = document.querySelector('.cart-dropdown-items');
    const cartDropdownEmpty = document.querySelector('.cart-dropdown-empty');
    const cartDropdownTotal = document.querySelector('.cart-dropdown-total-value');
    
    if (!cartBadge || !cartDropdownItems || !cartDropdownEmpty || !cartDropdownTotal) {
        console.error("Éléments du panier manquants dans le DOM");
        return;
    }
    
    // Mettre à jour le compteur d'articles
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartBadge.textContent = totalItems;
    
    // Vider le contenu précédent
    cartDropdownItems.innerHTML = '';
    
    // Calculer le total
    let total = 0;
    
    if (cart.length === 0) {
        // Afficher le message "panier vide"
        cartDropdownEmpty.style.display = 'block';
    } else {
        // Masquer le message "panier vide"
        cartDropdownEmpty.style.display = 'none';
        
        // Afficher chaque article
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <div class="cart-item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="cart-item-info">
                    <h4>${item.name}</h4>
                    <div class="cart-item-price">${item.quantity} × ${item.price.toFixed(2).replace('.', ',')} €</div>
                </div>
                <button class="cart-item-remove" data-id="${item.id}">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            cartDropdownItems.appendChild(cartItem);
        });
        
        // Ajouter des écouteurs d'événements pour les boutons de suppression
        document.querySelectorAll('.cart-item-remove').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const productId = this.getAttribute('data-id');
                removeFromCart(productId);
            });
        });
    }
    
    // Mettre à jour le total
    cartDropdownTotal.textContent = `${total.toFixed(2).replace('.', ',')} €`;
}

/**
 * Supprime un produit du panier
 */
function removeFromCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Filtrer le produit à supprimer
    cart = cart.filter(item => item.id !== productId);
    
    // Sauvegarder le panier mis à jour
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Mettre à jour l'affichage
    updateCartDisplay();
    
    // Afficher une confirmation
    showNotification('Produit retiré du panier');
}

/**
 * Affiche une notification temporaire
 */
function showNotification(message) {
    // Supprimer toute notification existante
    const existingNotif = document.querySelector('.cart-notification');
    if (existingNotif) {
        document.body.removeChild(existingNotif);
    }
    
    // Créer et afficher la nouvelle notification
    const notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Masquer et supprimer après un délai
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 500);
    }, 2000);
}

/**
 * Vide complètement le panier
 */
function clearCart() {
    localStorage.removeItem('cart');
    updateCartDisplay();
    showNotification('Votre panier a été vidé');
}

/**
 * Met à jour la quantité d'un produit dans le panier
 */
function updateCartItemQuantity(productId, newQuantity) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Trouver l'article à mettre à jour
    const itemIndex = cart.findIndex(item => item.id === productId);
    
    if (itemIndex > -1) {
        if (newQuantity <= 0) {
            // Si la quantité est 0 ou négative, supprimer l'article
            removeFromCart(productId);
        } else {
            // Mettre à jour la quantité
            cart[itemIndex].quantity = newQuantity;
            
            // Sauvegarder le panier mis à jour
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Mettre à jour l'affichage
            updateCartDisplay();
        }
    }
}

/**
 * Calcule le sous-total du panier (sans frais de livraison, etc.)
 * Retourne un nombre décimal
 */
function calculateCartSubtotal() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

/**
 * Exporter les fonctions pour les rendre accessibles globalement
 */
window.cartFunctions = {
    checkProductStock,
    addToCart,
    removeFromCart,
    updateCartDisplay,
    showNotification,
    clearCart,
    updateCartItemQuantity,
    calculateCartSubtotal
};