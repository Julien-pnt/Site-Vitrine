/**
 * Gestion du panier d'achat
 * Script qui gère les fonctionnalités du panier sur toutes les pages
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le panier
    initCart();

    // Écouter les clics sur les boutons "Ajouter au panier" dans la grille de produits
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('.product-title').textContent;
            const productPriceText = productCard.querySelector('.product-price').textContent;
            const productPrice = parseFloat(productPriceText.replace(/[^0-9,.-]+/g, '').replace(',', '.'));
            const productImage = productCard.querySelector('.product-image').getAttribute('src');
            
            addToCart({
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: 1
            });
        });
    });

    // Écouter les clics sur le bouton "Ajouter au panier" dans la modal
    const modalAddToCartBtn = document.getElementById('modal-add-to-cart');
    if (modalAddToCartBtn) {
        modalAddToCartBtn.addEventListener('click', function() {
            const productName = document.getElementById('modal-product-title').textContent;
            const productPriceText = document.getElementById('modal-product-price').textContent;
            const productPrice = parseFloat(productPriceText.replace(/[^0-9,.-]+/g, '').replace(',', '.'));
            const productImage = document.getElementById('modal-product-image').getAttribute('src');
            // Utilisation d'un ID unique basé sur le nom pour éviter les duplications
            const productId = 'modal-' + productName.toLowerCase().replace(/\s+/g, '-');
            
            addToCart({
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: 1
            });
            
            // Fermer la modal après ajout au panier
            const modal = document.getElementById('quick-view-modal');
            if (modal) modal.style.display = 'none';
            document.body.style.overflow = '';
        });
    }
});

/**
 * Initialise le panier depuis le localStorage
 */
function initCart() {
    const cart = getCart();
    updateCartUI(cart);
}

/**
 * Récupère le panier depuis le localStorage
 */
function getCart() {
    const cart = localStorage.getItem('elixirCart');
    return cart ? JSON.parse(cart) : [];
}

/**
 * Sauvegarde le panier dans le localStorage
 */
function saveCart(cart) {
    localStorage.setItem('elixirCart', JSON.stringify(cart));
}

/**
 * Ajoute un produit au panier
 */
function addToCart(product) {
    const cart = getCart();
    
    // Vérifier si le produit existe déjà dans le panier
    const existingProductIndex = cart.findIndex(item => item.id === product.id);
    
    if (existingProductIndex > -1) {
        // Augmenter la quantité si le produit existe déjà
        cart[existingProductIndex].quantity++;
    } else {
        // Ajouter le nouveau produit
        cart.push(product);
    }
    
    // Sauvegarder et mettre à jour l'UI
    saveCart(cart);
    updateCartUI(cart);
    
    // Animation d'ajout
    const cartIcon = document.querySelector('.cart-icon');
    cartIcon.classList.add('cart-item-added');
    setTimeout(() => {
        cartIcon.classList.remove('cart-item-added');
    }, 400);

    // Notification visuelle pour confirmer l'ajout
    showNotification(`"${product.name}" ajouté au panier`);
}

/**
 * Supprime un produit du panier
 */
function removeFromCart(productId) {
    let cart = getCart();
    const productToRemove = cart.find(item => item.id === productId);
    const productName = productToRemove ? productToRemove.name : '';
    
    cart = cart.filter(item => item.id !== productId);
    
    saveCart(cart);
    updateCartUI(cart);
    
    if (productName) {
        showNotification(`"${productName}" retiré du panier`);
    }
}

/**
 * Diminue la quantité d'un produit dans le panier
 */
function decreaseQuantity(productId) {
    const cart = getCart();
    const productIndex = cart.findIndex(item => item.id === productId);
    
    if (productIndex > -1) {
        if (cart[productIndex].quantity > 1) {
            cart[productIndex].quantity--;
            
            saveCart(cart);
            updateCartUI(cart);
        } else {
            removeFromCart(productId);
        }
    }
}

/**
 * Calcule le total du panier
 */
function calculateCartTotal(cart) {
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

/**
 * Met à jour l'interface utilisateur du panier
 */
function updateCartUI(cart) {
    const cartBadge = document.querySelector('.cart-badge');
    const cartItemsContainer = document.querySelector('.cart-dropdown-items');
    const cartEmptyMessage = document.querySelector('.cart-dropdown-empty');
    const cartTotalValue = document.querySelector('.cart-dropdown-total-value');
    
    // Mettre à jour le nombre d'articles
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    cartBadge.textContent = totalItems;
    
    // Gérer l'affichage des éléments du panier ou du message "panier vide"
    if (cart.length === 0) {
        if (cartEmptyMessage) cartEmptyMessage.style.display = 'block';
        if (cartItemsContainer) cartItemsContainer.innerHTML = '';
    } else {
        if (cartEmptyMessage) cartEmptyMessage.style.display = 'none';
        
        if (cartItemsContainer) {
            // Générer le HTML pour les articles
            cartItemsContainer.innerHTML = '';
            cart.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'cart-item';
                itemElement.innerHTML = `
                    <div class="cart-item-image">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="cart-item-details">
                        <h4>${item.name}</h4>
                        <div class="cart-item-price">${formatPrice(item.price)} €</div>
                        <div class="cart-item-quantity">
                            <button class="qty-btn decrease" data-id="${item.id}">-</button>
                            <span>${item.quantity}</span>
                            <button class="qty-btn increase" data-id="${item.id}">+</button>
                        </div>
                    </div>
                    <button class="cart-item-remove" data-id="${item.id}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                `;
                cartItemsContainer.appendChild(itemElement);
            });
            
            // Ajouter les écouteurs d'événements
            attachCartItemEvents();
        }
    }
    
    // Mettre à jour le total
    const total = calculateCartTotal(cart);
    if (cartTotalValue) cartTotalValue.textContent = `${formatPrice(total)} €`;

    // Mettre à jour les liens du panier avec les bons chemins
    updateCartLinks();
}

/**
 * Met à jour les liens du panier en fonction de l'emplacement actuel de la page
 */
function updateCartLinks() {
    const viewCartLink = document.querySelector('.cart-dropdown-button.secondary');
    const discoverWatchesLink = document.querySelector('.cart-dropdown-button.primary');
    
    if (viewCartLink) {
        // Déterminer le chemin relatif correct
        if (window.location.pathname.includes('/collections/')) {
            viewCartLink.href = '../products/panier.html';
        } else if (window.location.pathname.includes('/products/')) {
            viewCartLink.href = 'panier.html';
        } else {
            viewCartLink.href = 'products/panier.html';
        }
    }
    
    if (discoverWatchesLink) {
        // Déterminer le chemin relatif correct pour la page des montres
        if (window.location.pathname.includes('/collections/')) {
            discoverWatchesLink.href = '../products/Montres.html';
        } else if (window.location.pathname.includes('/products/')) {
            discoverWatchesLink.href = 'Montres.html';
        } else {
            discoverWatchesLink.href = 'products/Montres.html';
        }
    }
}

/**
 * Attache les écouteurs d'événements aux articles du panier
 */
function attachCartItemEvents() {
    // Boutons de suppression
    document.querySelectorAll('.cart-item-remove').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            removeFromCart(productId);
        });
    });
    
    // Boutons de quantité
    document.querySelectorAll('.qty-btn.decrease').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            decreaseQuantity(productId);
        });
    });
    
    document.querySelectorAll('.qty-btn.increase').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const cart = getCart();
            const product = cart.find(item => item.id === productId);
            if (product) {
                addToCart({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image: product.image,
                    quantity: 0
                });
            }
        });
    });
}

/**
 * Affiche une notification temporaire
 */
function showNotification(message) {
    // Créer l'élément de notification s'il n'existe pas déjà
    let notification = document.getElementById('cart-notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'cart-notification';
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
            box-shadow: 0 3px 6px rgba(0,0,0,0.16);
        `;
        document.body.appendChild(notification);
    }
    
    // Mettre à jour le message et afficher
    notification.textContent = message;
    notification.style.opacity = '1';
    
    // Cacher après 3 secondes
    setTimeout(() => {
        notification.style.opacity = '0';
    }, 3000);
}

/**
 * Formate un nombre en prix
 */
function formatPrice(price) {
    return price.toFixed(2).replace('.', ',');
}