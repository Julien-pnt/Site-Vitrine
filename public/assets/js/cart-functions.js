// Créez ce nouveau fichier pour centraliser les fonctions du panier

// Fonction pour initialiser tous les gestionnaires d'événements des boutons du panier
function initCartButtonHandlers() {
    // 1. Gestionnaires pour les boutons de suppression
    document.querySelectorAll('.cart-dropdown .remove-cart-item').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            if (productId) {
                // Ajouter effet visuel de suppression
                const cartItem = this.closest('.cart-item');
                if (cartItem) {
                    cartItem.style.opacity = '0.5';
                    cartItem.style.pointerEvents = 'none';
                }
                
                removeCartItemFromDB(productId);
            }
        });
    });
    
    // 2. Gestionnaires pour les boutons d'augmentation de quantité
    document.querySelectorAll('.cart-dropdown .quantity-btn.increase').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            if (productId) {
                console.log("Augmenter quantité du produit:", productId);
                // Ajouter effet visuel
                this.disabled = true;
                this.style.opacity = '0.5';
                updateCartItemQuantity(productId, 1);
            }
        });
    });
    
    // 3. Gestionnaires pour les boutons de diminution de quantité
    document.querySelectorAll('.cart-dropdown .quantity-btn.decrease').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            if (productId) {
                console.log("Diminuer quantité du produit:", productId);
                // Ajouter effet visuel
                this.disabled = true;
                this.style.opacity = '0.5';
                updateCartItemQuantity(productId, -1);
            }
        });
    });
}

// Fonction pour supprimer un article du panier
function removeCartItemFromDB(productId) {
    showLoading();
    
    const apiUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    
    fetch(apiUrl + '/php/api/cart/remove-item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log("Réponse de suppression:", data);
        if (data.success) {
            showNotification('Article supprimé du panier', 'success');
        } else {
            showNotification(data.message || 'Erreur lors de la suppression', 'error');
        }
        // Recharger la page après 500ms pour voir la notification
        setTimeout(() => window.location.reload(), 500);
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', 'error');
        setTimeout(() => window.location.reload(), 500);
    });
}

// Fonction pour mettre à jour la quantité d'un article
function updateCartItemQuantity(productId, change) {
    console.log(`Mise à jour de la quantité: Produit ${productId}, Changement: ${change}`);
    showLoading();
    
    const apiUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const url = apiUrl + '/php/api/cart/update-quantity.php';
    
    console.log("URL d'appel:", url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&change=${change}`
    })
    .then(response => {
        console.log("Statut de la réponse:", response.status);
        if (!response.ok) {
            throw new Error('Erreur réseau: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log("Données reçues:", data);
        if (data.success) {
            const action = change > 0 ? 'augmentée' : 'diminuée';
            showNotification(`Quantité ${action}`, 'success');
        } else {
            showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
        }
        // Recharger la page après une courte pause
        setTimeout(() => window.location.reload(), 500);
    })
    .catch(error => {
        console.error('Erreur détaillée:', error);
        showNotification('Erreur de connexion', 'error');
        setTimeout(() => window.location.reload(), 500);
    });
}

// Fonction pour afficher une notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    // Ajouter le message
    const messageSpan = document.createElement('span');
    messageSpan.textContent = message;
    
    // Ajouter un bouton de fermeture
    const closeButton = document.createElement('button');
    closeButton.className = 'close-notification';
    closeButton.innerHTML = '×';
    closeButton.addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    });
    
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

// Fonction pour afficher un indicateur de chargement
function showLoading() {
    // Créer un overlay de chargement si nécessaire
    if (!document.getElementById('cart-loading')) {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'cart-loading';
        loadingOverlay.className = 'cart-loading-overlay';
        loadingOverlay.innerHTML = '<div class="cart-loading-spinner"></div>';
        document.body.appendChild(loadingOverlay);
        
        // Style pour l'overlay
        loadingOverlay.style.position = 'fixed';
        loadingOverlay.style.top = '0';
        loadingOverlay.style.left = '0';
        loadingOverlay.style.width = '100%';
        loadingOverlay.style.height = '100%';
        loadingOverlay.style.background = 'rgba(255, 255, 255, 0.7)';
        loadingOverlay.style.zIndex = '9999';
        loadingOverlay.style.display = 'flex';
        loadingOverlay.style.justifyContent = 'center';
        loadingOverlay.style.alignItems = 'center';
        
        // Style pour le spinner
        const spinner = loadingOverlay.querySelector('.cart-loading-spinner');
        spinner.style.width = '40px';
        spinner.style.height = '40px';
        spinner.style.border = '4px solid #f3f3f3';
        spinner.style.borderTop = '4px solid #8B5C2E';
        spinner.style.borderRadius = '50%';
        spinner.style.animation = 'spin 1s linear infinite';
        
        // Ajouter l'animation CSS
        if (!document.getElementById('spin-animation')) {
            const style = document.createElement('style');
            style.id = 'spin-animation';
            style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
            document.head.appendChild(style);
        }
    }
}

// Exporter les fonctions pour les rendre disponibles
window.cartFunctions = {
    initCartButtonHandlers,
    removeCartItemFromDB,
    updateCartItemQuantity,
    showNotification,
    showLoading
};