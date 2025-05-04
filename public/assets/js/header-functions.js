/**
 * Fichier de fonctions pour le header
 */

// Au début de votre fichier header-functions.js
console.log("Chargement du header-functions.js");

document.addEventListener('DOMContentLoaded', function() {
    // Nettoyer tout écouteur d'événement existant sur l'icône du panier
    const oldCartIcon = document.querySelector('.cart-icon');
    if (oldCartIcon) {
        const newCartIcon = oldCartIcon.cloneNode(true);
        oldCartIcon.parentNode.replaceChild(newCartIcon, oldCartIcon);
        console.log("Remplacement de l'icône du panier pour éviter les conflits");
    }

    // Vérifier si l'utilisateur est connecté dès le chargement
    checkUserLoggedIn();
    
    // Gestion du menu déroulant utilisateur
    setupUserMenu();
    
    // Gestion du panier
    setupCart();
    
    // Initialisation de l'affichage du panier
    updateCartDisplay();
    
    // Configuration des boutons d'ajout au panier
    setupAddToCartButtons();
    
    // Vérifier périodiquement l'état de connexion (toutes les 5 minutes)
    setInterval(checkUserLoggedIn, 300000);
});

// Gestion du menu déroulant utilisateur
function setupUserMenu() {
    const userIconWrapper = document.querySelector('.user-icon-wrapper');
    const userDropdown = document.querySelector('.user-dropdown');
    const guestOptions = document.querySelector('.guest-options');
    const userOptions = document.querySelector('.user-options');
    
    // Masquer les deux options par défaut jusqu'à ce que nous connaissions l'état de connexion
    if (guestOptions && userOptions) {
        guestOptions.style.display = 'none';
        userOptions.style.display = 'none';
    }
    
    // Toggle du menu au clic sur l'icône
    if (userIconWrapper && userDropdown) {
        userIconWrapper.addEventListener('click', function(e) {
            e.stopPropagation();
            userIconWrapper.classList.toggle('active');
            userDropdown.classList.toggle('show');
        });
        
        // Fermer le menu au clic en dehors
        document.addEventListener('click', function(e) {
            if (!userIconWrapper.contains(e.target) && !userDropdown.contains(e.target)) {
                userIconWrapper.classList.remove('active');
                userDropdown.classList.remove('show');
            }
        });
    }
}

// Vérifier si l'utilisateur est connecté
function checkUserLoggedIn() {
    const guestOptions = document.querySelector('.guest-options');
    const userOptions = document.querySelector('.user-options');
    const userIconWrapper = document.querySelector('.user-icon-wrapper');
    
    if (!guestOptions || !userOptions) return;
    
    fetch('/Site-Vitrine/php/api/auth/check-status.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.isLoggedIn) {
                // Utilisateur connecté
                guestOptions.style.display = 'none';
                userOptions.style.display = 'block';
                
                // Ajouter une indication visuelle que l'utilisateur est connecté
                if (userIconWrapper) {
                    userIconWrapper.classList.add('logged-in');
                }
            } else {
                // Utilisateur déconnecté
                guestOptions.style.display = 'block';
                userOptions.style.display = 'none';
                
                // Enlever l'indication visuelle
                if (userIconWrapper) {
                    userIconWrapper.classList.remove('logged-in');
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la vérification de connexion:', error);
            // En cas d'erreur, afficher les options par défaut (pour invités)
            if (guestOptions && userOptions) {
                guestOptions.style.display = 'block';
                userOptions.style.display = 'none';
            }
        });
}

// Configuration du dropdown du panier
function setupCart() {
    console.log("Initialisation du menu du panier");
    
    const cartIcon = document.querySelector('.cart-icon');
    const cartDropdown = document.querySelector('.cart-dropdown');
    
    if (cartIcon && cartDropdown) {
        console.log("Éléments du panier trouvés");
        
        // Nettoyer les gestionnaires existants (pour éviter les duplications)
        const newCartIcon = cartIcon.cloneNode(true);
        cartIcon.parentNode.replaceChild(newCartIcon, cartIcon);
        
        // Nouvelle référence après clonage
        const updatedCartIcon = document.querySelector('.cart-icon');
        
        // Gestionnaire d'événement direct avec style forcé
        updatedCartIcon.addEventListener('click', function(e) {
            console.log("Clic sur le panier détecté");
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle avec style forcé (comme dans le script de diagnostic)
            if (cartDropdown.classList.contains('show')) {
                console.log("Masquage du dropdown");
                cartDropdown.classList.remove('show');
                cartDropdown.style.display = 'none';
            } else {
                console.log("Affichage du dropdown");
                cartDropdown.classList.add('show');
                cartDropdown.style.display = 'block';
            }
        });
        
        // Fermer le dropdown quand on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.cart-dropdown') && !e.target.closest('.cart-icon')) {
                cartDropdown.classList.remove('show');
                cartDropdown.style.display = 'none';
            }
        });
    } else {
        console.error("Éléments du panier introuvables", {
            cartIconExists: !!cartIcon,
            cartDropdownExists: !!cartDropdown
        });
    }
}

// Configuration des boutons d'ajout au panier
function setupAddToCartButtons() {
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
}

// Vérifier le stock d'un produit
function checkProductStock(productId, productName, productPrice, productImage) {
    // Pour éviter l'ajout en double, on vérifie si on a déjà effectué cette action récemment
    const lastAddTime = parseInt(localStorage.getItem(`last_add_${productId}`)) || 0;
    const now = Date.now();
    if (now - lastAddTime < 2000) { // Ignorer les clics multiples dans un intervalle de 2 secondes
        return;
    }
    localStorage.setItem(`last_add_${productId}`, now);
    
    // Ajouter directement au panier (sans vérification de stock pour simplifier)
    addToCart(productId, productName, 1, productPrice, productImage);
    showNotification('Produit ajouté au panier');
}

// Ajouter un produit au panier
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

// Mettre à jour l'affichage du panier
function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartBadge = document.querySelector('.cart-badge');
    const cartDropdownItems = document.querySelector('.cart-dropdown-items');
    const cartDropdownEmpty = document.querySelector('.cart-dropdown-empty');
    const cartDropdownTotal = document.querySelector('.cart-dropdown-total-value');
    
    if (!cartBadge || !cartDropdownItems || !cartDropdownEmpty || !cartDropdownTotal) {
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

// Supprimer un produit du panier
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

// Afficher une notification
function showNotification(message, type = 'success') {
    // Supprimer toute notification existante
    const existingNotif = document.querySelector('.cart-notification');
    if (existingNotif) {
        document.body.removeChild(existingNotif);
    }
    
    // Créer et afficher la nouvelle notification
    const notification = document.createElement('div');
    notification.className = 'cart-notification ' + type;
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