/**
 * Système de panier complet pour Elixir du Temps
 */
(function() {
    // Exécuter une fois que le DOM est complètement chargé
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initialisation du système de panier...');
        initCartButtonHandlers();
        
        // Surveiller les changements de DOM pour les composants chargés dynamiquement
        setupCartMutationObserver();
    });

    /**
     * Initialise tous les gestionnaires d'événements pour les boutons du panier
     */
    function initCartButtonHandlers() {
        console.log('Configuration des boutons du panier');
        
        // 1. Boutons de suppression
        document.querySelectorAll('.cart-dropdown .remove-cart-item').forEach(button => {
            if (button.getAttribute('data-initialized') !== 'true') {
                button.setAttribute('data-initialized', 'true');
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const productId = this.getAttribute('data-product-id');
                    if (productId) {
                        console.log('Suppression du produit:', productId);
                        removeCartItem(productId);
                    }
                });
            }
        });
        
        // 2. Boutons d'augmentation de quantité
        document.querySelectorAll('.cart-dropdown .quantity-btn.increase').forEach(button => {
            if (button.getAttribute('data-initialized') !== 'true') {
                button.setAttribute('data-initialized', 'true');
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const productId = this.getAttribute('data-product-id');
                    if (productId) {
                        console.log('Augmentation de la quantité pour le produit:', productId);
                        updateCartQuantity(productId, 1);
                    }
                });
            }
        });
        
        // 3. Boutons de diminution de quantité
        document.querySelectorAll('.cart-dropdown .quantity-btn.decrease').forEach(button => {
            if (button.getAttribute('data-initialized') !== 'true') {
                button.setAttribute('data-initialized', 'true');
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const productId = this.getAttribute('data-product-id');
                    if (productId) {
                        console.log('Diminution de la quantité pour le produit:', productId);
                        updateCartQuantity(productId, -1);
                    }
                });
            }
        });
        
        // 4. Boutons d'ajout au panier sur les pages produits
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            if (button.getAttribute('data-initialized') !== 'true') {
                button.setAttribute('data-initialized', 'true');
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const productId = this.getAttribute('data-product-id');
                    const quantity = document.querySelector('.quantity-input') ? 
                                    parseInt(document.querySelector('.quantity-input').value) : 1;
                    
                    if (productId) {
                        console.log('Ajout au panier:', productId, 'quantité:', quantity);
                        addToCart(productId, quantity);
                    }
                });
            }
        });
    }
    
    /**
     * Mise à jour de la quantité d'un article du panier
     */
    function updateCartQuantity(productId, change) {
        // Désactiver temporairement les boutons
        const buttons = document.querySelectorAll(`.cart-item[data-product-id="${productId}"] .quantity-btn`);
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.style.opacity = '0.5';
        });
        
        const quantityElement = document.querySelector(`.cart-item[data-product-id="${productId}"] .quantity-value`);
        if (quantityElement) {
            quantityElement.style.color = '#8B5C2E';
            quantityElement.style.fontWeight = 'bold';
        }
        
        // Obtenir l'URL de base
        const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
        
        // Construire l'URL complète
        const apiUrl = `${baseUrl}/php/api/cart/update-quantity.php`;
        console.log('URL d\'API pour mise à jour:', apiUrl);
        
        // Envoyer la requête
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&change=${change}`
        })
        .then(response => {
            console.log('Statut de réponse:', response.status);
            if (!response.ok) {
                throw new Error(`Erreur réseau: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            
            if (data.success) {
                // Animation sur le badge du panier
                animateCartBadge();
                
                // Notification
                showNotification(change > 0 ? 'Quantité augmentée' : 'Quantité diminuée', 'success');
                
                // Recharger la page pour refléter les changements
                setTimeout(() => window.location.reload(), 500);
            } else {
                showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
                
                // Réactiver les boutons
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                });
                
                if (quantityElement) {
                    quantityElement.style.color = '';
                    quantityElement.style.fontWeight = '';
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur de connexion', 'error');
            
            // Réactiver les boutons
            buttons.forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
            });
            
            if (quantityElement) {
                quantityElement.style.color = '';
                quantityElement.style.fontWeight = '';
            }
        });
    }
    
    /**
     * Suppression d'un article du panier
     */
    function removeCartItem(productId) {
        // Effet visuel
        const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
        if (cartItem) {
            cartItem.style.opacity = '0.5';
            cartItem.style.pointerEvents = 'none';
        }
        
        // Construire l'URL
        const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
        const apiUrl = `${baseUrl}/php/api/cart/remove-item.php`;
        
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur réseau: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                animateCartBadge();
                showNotification('Article supprimé du panier', 'success');
                setTimeout(() => window.location.reload(), 500);
            } else {
                showNotification(data.message || 'Erreur lors de la suppression', 'error');
                
                // Restaurer l'apparence
                if (cartItem) {
                    cartItem.style.opacity = '1';
                    cartItem.style.pointerEvents = 'auto';
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur de connexion', 'error');
            
            if (cartItem) {
                cartItem.style.opacity = '1';
                cartItem.style.pointerEvents = 'auto';
            }
        });
    }
    
    /**
     * Ajout d'un article au panier
     */
    function addToCart(productId, quantity = 1) {
        // Effet visuel sur le bouton
        const addButton = document.querySelector(`.add-to-cart-btn[data-product-id="${productId}"]`);
        if (addButton) {
            const originalText = addButton.textContent;
            addButton.disabled = true;
            addButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout...';
            
            // Restaurer après 2 secondes si la requête échoue
            setTimeout(() => {
                if (addButton.disabled) {
                    addButton.disabled = false;
                    addButton.innerHTML = originalText;
                }
            }, 2000);
        }
        
        // Construire l'URL
        const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
        const apiUrl = `${baseUrl}/php/api/cart/add-to-cart.php`;
        
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur réseau: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Animation du badge
                animateCartBadge();
                
                // Restaurer et mettre à jour le bouton
                if (addButton) {
                    addButton.disabled = false;
                    addButton.innerHTML = '<i class="fas fa-check"></i> Ajouté';
                    setTimeout(() => {
                        addButton.innerHTML = '<i class="fas fa-shopping-cart"></i> Ajouter au panier';
                    }, 1500);
                }
                
                showNotification('Produit ajouté au panier', 'success');
                
                // Mettre à jour le compteur sans recharger la page
                updateCartCount(data.cartCount || 0);
            } else {
                if (addButton) {
                    addButton.disabled = false;
                    addButton.innerHTML = originalText;
                }
                showNotification(data.message || 'Erreur lors de l\'ajout au panier', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur de connexion', 'error');
            
            if (addButton) {
                addButton.disabled = false;
                addButton.innerHTML = '<i class="fas fa-shopping-cart"></i> Ajouter au panier';
            }
        });
    }
    
    /**
     * Animation du badge du panier
     */
    function animateCartBadge() {
        const badge = document.getElementById('cart-count');
        if (badge) {
            // Ajouter puis retirer une classe pour l'animation
            badge.classList.add('updated');
            setTimeout(() => badge.classList.remove('updated'), 500);
        }
    }
    
    /**
     * Mise à jour du compteur de panier
     */
    function updateCartCount(count) {
        const countElement = document.getElementById('cart-count');
        if (countElement) {
            countElement.textContent = count;
            animateCartBadge();
        }
    }
    
    /**
     * Affichage d'une notification
     */
    function showNotification(message, type = 'success') {
        // Supprimer les notifications existantes
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notif => notif.remove());
        
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
    
    /**
     * Configuration d'un observateur pour les changements dynamiques
     */
    function setupCartMutationObserver() {
        // Observer les changements dans le DOM pour les composants chargés dynamiquement
        const observer = new MutationObserver(function(mutations) {
            let shouldReinitButtons = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Node.ELEMENT_NODE
                            if (node.querySelector('.quantity-btn') || 
                                node.querySelector('.remove-cart-item') ||
                                node.querySelector('.add-to-cart-btn')) {
                                shouldReinitButtons = true;
                            }
                        }
                    });
                }
            });
            
            if (shouldReinitButtons) {
                console.log('Nouveaux éléments du panier détectés, initialisation des boutons');
                initCartButtonHandlers();
            }
        });
        
        // Démarrer l'observation
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Exposer les fonctions nécessaires pour l'utilisation globale
    window.cartFunctions = {
        initCartButtonHandlers,
        updateCartQuantity,
        removeCartItem,
        addToCart,
        showNotification
    };
    
    console.log('Module de panier chargé avec succès');
})();