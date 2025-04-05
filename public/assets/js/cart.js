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
            
            // Vérifier le stock avant d'ajouter au panier
            checkProductStock(productId).then(stockInfo => {
                if (stockInfo.error) {
                    showNotification(stockInfo.error, 'error');
                    return;
                }
                
                if (stockInfo.stock > 0) {
                    addToCart({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        image: productImage,
                        quantity: 1,
                        availableStock: stockInfo.stock
                    });
                } else {
                    showNotification(`"${productName}" est en rupture de stock`, 'error');
                }
            }).catch(error => {
                console.error("Erreur lors de la vérification du stock:", error);
                showNotification("Impossible de vérifier le stock. Veuillez réessayer.", 'error');
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
            const productId = this.getAttribute('data-product-id') || 'modal-' + productName.toLowerCase().replace(/\s+/g, '-');
            
            // Vérifier le stock avant d'ajouter au panier
            checkProductStock(productId).then(stockInfo => {
                if (stockInfo.error) {
                    showNotification(stockInfo.error, 'error');
                    return;
                }
                
                if (stockInfo.stock > 0) {
                    addToCart({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        image: productImage,
                        quantity: 1,
                        availableStock: stockInfo.stock
                    });
                } else {
                    showNotification(`"${productName}" est en rupture de stock`, 'error');
                }
            }).catch(error => {
                console.error("Erreur lors de la vérification du stock:", error);
                showNotification("Impossible de vérifier le stock. Veuillez réessayer.", 'error');
            });
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
 * Ajoute un produit au panier avec vérification de stock
 */
function addToCart(product) {
    const cart = getCart();
    
    // Vérifier si le produit existe déjà dans le panier
    const existingProductIndex = cart.findIndex(item => item.id === product.id);
    
    if (existingProductIndex > -1) {
        // Le produit est déjà dans le panier
        const currentQuantity = cart[existingProductIndex].quantity;
        
        // Vérifier si l'ajout est possible
        if (currentQuantity < product.availableStock) {
            // Stock suffisant, augmenter la quantité
            cart[existingProductIndex].quantity++;
            cart[existingProductIndex].availableStock = product.availableStock; // Mettre à jour le stock disponible
            cart[existingProductIndex].stockAlerte = product.stockAlerte; // Stocker le seuil d'alerte
            
            // Sauvegarder et mettre à jour l'UI
            saveCart(cart);
            updateCartUI(cart);
            
            // Animation et notification
            animateCartIcon();
            showNotification(`"${product.name}" ajouté au panier`);
        } else {
            // Stock insuffisant
            showNotification(`Stock maximum atteint pour "${product.name}"`, 'error');
        }
    } else {
        // Nouveau produit, ajouter avec l'info de stock
        cart.push(product);
        
        // Sauvegarder et mettre à jour l'UI
        saveCart(cart);
        updateCartUI(cart);
        
        // Animation et notification
        animateCartIcon();
        showNotification(`"${product.name}" ajouté au panier`);
    }
}

/**
 * Vérifie le stock disponible d'un produit en base de données
 */
function checkProductStock(productId) {
    return fetch(`/public/php/api/products/check-stock.php?id=${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau lors de la vérification du stock');
            }
            return response.json();
        });
}

/**
 * Animation de l'icône du panier
 */
function animateCartIcon() {
    const cartIcon = document.querySelector('.cart-icon');
    cartIcon.classList.add('cart-item-added');
    setTimeout(() => {
        cartIcon.classList.remove('cart-item-added');
    }, 400);
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
    // Mettre à jour le nombre d'éléments dans le badge
    const cartBadge = document.querySelector('.cart-badge');
    if (cartBadge) {
        const itemCount = cart.reduce((total, item) => total + item.quantity, 0);
        cartBadge.textContent = itemCount;
    }
    
    // Mettre à jour le contenu du dropdown du panier
    const cartItems = document.querySelector('.cart-dropdown-items');
    const cartEmpty = document.querySelector('.cart-dropdown-empty');
    const cartTotal = document.querySelector('.cart-dropdown-total-value');
    
    if (cartItems && cartEmpty && cartTotal) {
        if (cart.length === 0) {
            cartItems.innerHTML = '';
            cartEmpty.style.display = 'block';
            cartTotal.textContent = '0,00 €';
            return;
        }
        
        cartEmpty.style.display = 'none';
        cartItems.innerHTML = '';
        
        let total = 0;
        
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            const itemElement = document.createElement('div');
            itemElement.className = 'cart-dropdown-item';
            
            // Déterminer si le stock est faible selon le seuil d'alerte
            const isLowStock = item.quantity >= item.availableStock || 
                               (item.availableStock <= (item.stockAlerte || 5));
            
            itemElement.innerHTML = `
                <div class="cart-dropdown-item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="cart-dropdown-item-info">
                    <h4>${item.name}</h4>
                    <p>${item.quantity} × ${item.price.toFixed(2).replace('.', ',')} €</p>
                    ${isLowStock ? `<small class="stock-warning">Stock limité</small>` : ''}
                </div>
                <button class="cart-dropdown-item-remove" data-id="${item.id}">×</button>
            `;
            
            cartItems.appendChild(itemElement);
        });
        
        // Mettre à jour le total
        cartTotal.textContent = total.toFixed(2).replace('.', ',') + ' €';
        
        // Ajouter les écouteurs d'événements pour les boutons de suppression
        document.querySelectorAll('.cart-dropdown-item-remove').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();  // Empêcher la propagation jusqu'au document
                const productId = this.getAttribute('data-id');
                removeFromCart(productId);
            });
        });
    }
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
    // Boutons de suppression (inchangés)
    document.querySelectorAll('.cart-item-remove').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            removeFromCart(productId);
        });
    });
    
    // Boutons de diminution de quantité (inchangés)
    document.querySelectorAll('.qty-btn.decrease').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            decreaseQuantity(productId);
        });
    });
    
    // Boutons d'augmentation de quantité (avec vérification de stock)
    document.querySelectorAll('.qty-btn.increase').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const cart = getCart();
            const product = cart.find(item => item.id === productId);
            
            if (product) {
                // Vérifier le stock en temps réel
                checkProductStock(productId)
                    .then(stockInfo => {
                        if (stockInfo.error) {
                            showNotification(stockInfo.error, 'error');
                            return;
                        }
                        
                        const availableStock = stockInfo.stock;
                        
                        // Vérifier si l'augmentation est possible
                        if (product.quantity < availableStock) {
                            product.quantity++;
                            product.availableStock = availableStock; // Mettre à jour le stock disponible
                            
                            saveCart(cart);
                            updateCartUI(cart);
                        } else {
                            showNotification(`Stock maximum atteint pour "${product.name}"`, 'error');
                        }
                    })
                    .catch(error => {
                        console.error("Erreur lors de la vérification du stock:", error);
                        showNotification("Impossible de vérifier le stock. Veuillez réessayer.", 'error');
                    });
            }
        });
    });
}

/**
 * Affiche une notification avec support des types (succès, erreur, etc.)
 */
function showNotification(message, type = 'success') {
    // Créer l'élément de notification s'il n'existe pas déjà
    let notification = document.getElementById('cart-notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'cart-notification';
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 4px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        `;
        document.body.appendChild(notification);
    }
    
    // Déterminer le style en fonction du type
    let backgroundColor, icon;
    switch (type) {
        case 'error':
            backgroundColor = '#f44336';
            icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
            break;
        case 'warning':
            backgroundColor = '#ff9800';
            icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
            break;
        default: // success
            backgroundColor = '#4CAF50';
            icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
    }
    
    // Mise à jour du style
    notification.style.backgroundColor = backgroundColor;
    notification.style.color = 'white';
    
    // Mettre à jour le contenu
    notification.innerHTML = `${icon}<span>${message}</span>`;
    
    // Animer l'apparition
    notification.style.opacity = '0';
    notification.style.transform = 'translateY(10px)';
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    }, 10);
    
    // Cacher après 3 secondes
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(10px)';
    }, 3000);
}

/**
 * Formate un nombre en prix
 */
function formatPrice(price) {
    return price.toFixed(2).replace('.', ',');
}