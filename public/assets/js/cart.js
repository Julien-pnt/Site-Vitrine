/**
 * Système de panier centralisé pour Elixir du Temps
 * Ce script gère toutes les fonctionnalités du panier à travers le site
 */

// Initialiser le panier au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initializeCart();
});

/**
 * Initialise le panier et configure les écouteurs d'événements
 */
function initializeCart() {
    // Mettre à jour l'affichage du panier
    updateCartDisplay();
    
    // Configurer les écouteurs d'événements pour le panier
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.querySelector('.cart-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        });
    }
    
    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function() {
        const dropdown = document.querySelector('.cart-dropdown.show');
        if (dropdown) {
            dropdown.classList.remove('show');
        }
    });
    
    // Rechercher et configurer tous les boutons d'ajout au panier
    configureAddToCartButtons();
}

/**
 * Configure les boutons d'ajout au panier
 */
function configureAddToCartButtons() {
    // Boutons standards d'ajout au panier
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', handleAddToCartClick);
    });
    
    // Boutons d'ajout au panier dans les modals/aperçus rapides
    document.querySelectorAll('#modal-add-to-cart').forEach(button => {
        button.addEventListener('click', handleAddToCartClick);
    });
}

/**
 * Gère l'événement de clic sur un bouton d'ajout au panier
 */
function handleAddToCartClick() {
    const productId = this.getAttribute('data-product-id');
    const productCard = this.closest('.product-card') || this.closest('.product-modal');
    
    if (!productCard) {
        console.error("Impossible de trouver les informations du produit");
        return;
    }
    
    const productName = productCard.querySelector('.product-title') ? 
        productCard.querySelector('.product-title').textContent : 
        document.getElementById('modal-product-title').textContent;
    
    const productPriceEl = productCard.querySelector('.product-price') || document.getElementById('modal-product-price');
    const productPriceText = productPriceEl.textContent;
    const productPrice = parseFloat(productPriceText.replace(/[^0-9,.-]+/g, '').replace(',', '.'));
    
    const productImageEl = productCard.querySelector('.product-image') || document.getElementById('modal-product-image');
    const productImage = productImageEl.getAttribute('src');
    
    // Vérifier le stock avant d'ajouter au panier
    checkProductStock(productId, productName, productPrice, productImage);
}

/**
 * Vérifie le stock d'un produit avant de l'ajouter au panier
 */
function checkProductStock(productId, productName, productPrice, productImage) {
    // Chemin absolu vers l'API pour éviter les problèmes de chemin relatif
    const apiUrl = `/Site-Vitrine/public/php/api/products/check-stock.php?id=${productId}`;
    
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                showNotification(data.error, 'error');
                return;
            }
            
            // Utiliser le prix de l'API si disponible
            let finalPrice = data.prix ? parseFloat(data.prix) : productPrice;
            if (data.prix_promo && parseFloat(data.prix_promo) < finalPrice) {
                finalPrice = parseFloat(data.prix_promo);
            }
            
            if (data.stock > 0) {
                // Ajouter au panier avec information de stock
                addToCart({
                    id: productId,
                    name: productName,
                    price: finalPrice,
                    image: productImage,
                    quantity: 1,
                    availableStock: data.stock,
                    stockAlerte: data.stock_alerte || 5,
                    reference: data.reference || `ELX-${productId}`
                });
                
                showNotification(`${productName} ajouté au panier`, 'success');
            } else {
                showNotification(`"${productName}" est en rupture de stock`, 'error');
            }
        })
        .catch(error => {
            console.error("Erreur lors de la vérification du stock:", error);
            showNotification("Impossible de vérifier le stock. Veuillez réessayer.", 'error');
        });
}

/**
 * Ajoute un produit au panier ou incrémente sa quantité
 */
function addToCart(product) {
    let cart = getCart();
    const existingProduct = cart.find(item => item.id === product.id);
    
    if (existingProduct) {
        if (existingProduct.quantity < product.availableStock) {
            existingProduct.quantity++;
        } else {
            showNotification(`Stock maximum atteint pour "${product.name}"`, 'warning');
            return;
        }
    } else {
        cart.push(product);
    }
    
    saveCart(cart);
    updateCartDisplay();
}

/**
 * Récupère le panier depuis localStorage
 */
function getCart() {
    return JSON.parse(localStorage.getItem('cart')) || [];
}

/**
 * Sauvegarde le panier dans localStorage
 */
function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

/**
 * Met à jour l'affichage du panier (badge et dropdown)
 */
function updateCartDisplay() {
    const cart = getCart();
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    // Mettre à jour le badge du panier
    const cartBadges = document.querySelectorAll('.cart-badge');
    cartBadges.forEach(badge => {
        badge.textContent = totalItems;
        badge.style.display = totalItems > 0 ? 'flex' : 'none';
    });
    
    // Mettre à jour le dropdown du panier
    const cartDropdownEmpty = document.querySelector('.cart-dropdown-empty');
    const cartDropdownItems = document.querySelector('.cart-dropdown-items');
    const cartDropdownTotal = document.querySelector('.cart-dropdown-total-value');
    
    if (!cartDropdownItems || !cartDropdownEmpty) return;
    
    if (totalItems === 0) {
        cartDropdownEmpty.style.display = 'block';
        cartDropdownItems.innerHTML = '';
        if (cartDropdownTotal) cartDropdownTotal.textContent = '0,00 €';
        return;
    }
    
    // Panier non vide
    cartDropdownEmpty.style.display = 'none';
    cartDropdownItems.innerHTML = '';
    let totalPrice = 0;
    
    // Ajouter chaque produit au dropdown
    cart.forEach(item => {
        const itemPrice = item.price * item.quantity;
        totalPrice += itemPrice;
        
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <div class="cart-item-image">
                <img src="${item.image}" alt="${item.name}">
            </div>
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <div class="cart-item-price">${item.quantity} x ${item.price.toFixed(2).replace('.', ',')} €</div>
            </div>
            <button class="cart-item-remove" data-id="${item.id}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;
        cartDropdownItems.appendChild(cartItem);
    });
    
    // Mettre à jour le total
    if (cartDropdownTotal) {
        cartDropdownTotal.textContent = `${totalPrice.toFixed(2).replace('.', ',')} €`;
    }
    
    // Ajouter les événements de suppression
    document.querySelectorAll('.cart-item-remove').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            removeFromCart(productId);
        });
    });
}

/**
 * Supprime un produit du panier
 */
function removeFromCart(productId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== productId);
    saveCart(cart);
    updateCartDisplay();
    showNotification('Produit retiré du panier', 'info');
}

/**
 * Affiche une notification à l'utilisateur
 */
function showNotification(message, type = 'success') {
    // Supprimer les anciennes notifications
    const oldNotifications = document.querySelectorAll('.notification');
    oldNotifications.forEach(notification => notification.remove());
    
    // Créer la nouvelle notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    
    // Afficher la notification
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Faire disparaître la notification après un délai
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Ajouter cette fonction à votre fichier cart.js existant
function handleApiResponse(response) {
    if (response && response.success) {
        // Afficher le message de succès
        if (response.message) {
            showNotification(response.message);
        }
        
        // Si la réponse indique qu'un rafraîchissement est nécessaire
        if (response.refresh === true) {
            // Attendre que la notification soit visible avant de rafraîchir
            setTimeout(function() {
                window.location.reload();
            }, 1000); // Attendre 1 seconde pour que l'utilisateur puisse voir la notification
        }
    } else {
        // Gérer les erreurs
        showNotification(response.message || 'Une erreur s\'est produite', 'error');
    }
}

// Exporter les fonctions pour les rendre disponibles globalement
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartDisplay = updateCartDisplay;
window.getCart = getCart;
window.saveCart = saveCart;
window.showNotification = showNotification;
window.handleApiResponse = handleApiResponse;