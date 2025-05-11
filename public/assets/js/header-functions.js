/**
 * Fichier de fonctions pour le header - v5.0
 * Optimisé pour une expérience fluide et réactive
 */

window.ElixirApp = window.ElixirApp || {};

// Exécution unique
if (!window.ElixirApp.headerInitialized) {
    window.ElixirApp.headerInitialized = true;
    window.ElixirApp.cartState = {
        isOpen: false,
        isLocked: false,
        closeTimer: null
    };
    
    // IIFE pour isoler les variables
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation optimisée
            initializeCart();
            initializeCartButtons();
            checkUserStatus();
            
            /**
             * Initialisation simplifiée et optimisée du panier
             */
            function initializeCart() {
                const cart = {
                    trigger: document.querySelector('.toggle-cart') || document.querySelector('.cart-icon'),
                    dropdown: document.querySelector('.cart-dropdown'),
                    closeBtn: document.querySelector('.close-cart-dropdown')
                };
                
                if (!cart.dropdown || !cart.trigger) return;
                
                // Transition CSS optimisée
                cart.dropdown.style.transition = 'opacity 0.2s ease, transform 0.2s ease, visibility 0s';
                
                // 1. Gestion du toggle panier - plus direct, moins de conditions
                cart.trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    toggleCart();
                });
                
                // 2. Fermeture avec le bouton X
                if (cart.closeBtn) {
                    cart.closeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        closeCart(true);
                    });
                }
                
                // 3. Empêcher la fermeture en cliquant dans le panier + interactions
                cart.dropdown.addEventListener('click', e => e.stopPropagation());
                cart.dropdown.addEventListener('mouseenter', () => lockCart(true));
                cart.dropdown.addEventListener('mouseleave', () => lockCart(false));
                
                // 4. Fermeture au clic ailleurs, plus directe
                document.addEventListener('click', function(e) {
                    if (window.ElixirApp.cartState.isOpen && 
                        !window.ElixirApp.cartState.isLocked &&
                        !cart.trigger.contains(e.target) && 
                        !cart.dropdown.contains(e.target)) {
                        closeCart();
                    }
                });
                
                // Fonctions simplifiées pour gérer l'état du panier
                function toggleCart() {
                    window.ElixirApp.cartState.isOpen ? closeCart(true) : openCart();
                }
                
                function openCart() {
                    // Annuler toute fermeture en cours
                    if (window.ElixirApp.cartState.closeTimer) {
                        clearTimeout(window.ElixirApp.cartState.closeTimer);
                        window.ElixirApp.cartState.closeTimer = null;
                    }
                    
                    // Ouverture immédiate avec animation CSS
                    requestAnimationFrame(() => {
                        cart.dropdown.classList.add('show');
                        window.ElixirApp.cartState.isOpen = true;
                    });
                }
                
                function closeCart(immediate = false) {
                    // Si verrouillé, ne pas fermer
                    if (window.ElixirApp.cartState.isLocked) return;
                    
                    // Si on demande une fermeture immédiate
                    if (immediate) {
                        cart.dropdown.classList.remove('show');
                        window.ElixirApp.cartState.isOpen = false;
                        return;
                    }
                    
                    // Sinon, légère temporisation (150ms est plus réactif que 300ms)
                    window.ElixirApp.cartState.closeTimer = setTimeout(() => {
                        if (!window.ElixirApp.cartState.isLocked) {
                            cart.dropdown.classList.remove('show');
                            window.ElixirApp.cartState.isOpen = false;
                        }
                        window.ElixirApp.cartState.closeTimer = null;
                    }, 150);
                }
                
                function lockCart(lock) {
                    window.ElixirApp.cartState.isLocked = lock;
                }
                
                // Exposer les fonctions pour usage externe
                window.ElixirApp.cart = { open: openCart, close: closeCart, toggle: toggleCart, lock: lockCart };
            }
            
            /**
             * Initialisation des boutons du panier avec retour visuel immédiat
             */
            function initializeCartButtons() {
                const buttons = {
                    increase: document.querySelectorAll('.quantity-btn.increase'),
                    decrease: document.querySelectorAll('.quantity-btn.decrease'),
                    remove: document.querySelectorAll('.remove-cart-item')
                };
                
                // Si aucun bouton, sortir
                if (buttons.increase.length === 0 && buttons.decrease.length === 0 && buttons.remove.length === 0) return;
                
                // Optimisation: stockage des quantités côté client pour plus de réactivité
                const quantities = {};
                document.querySelectorAll('.cart-item').forEach(item => {
                    const id = item.dataset.productId;
                    const qtyElement = item.querySelector('.quantity-value');
                    if (id && qtyElement) {
                        quantities[id] = parseInt(qtyElement.textContent) || 1;
                    }
                });
                
                // 1. Boutons d'augmentation - retour visuel immédiat
                buttons.increase.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const productId = this.dataset.productId;
                        const qtyElement = this.parentNode.querySelector('.quantity-value');
                        
                        // Mise à jour visuelle immédiate
                        if (qtyElement && quantities[productId]) {
                            quantities[productId]++;
                            qtyElement.textContent = quantities[productId];
                            
                            // Feedback visuel sur le bouton
                            this.classList.add('active');
                            setTimeout(() => this.classList.remove('active'), 200);
                            
                            // Mise à jour côté serveur en arrière-plan
                            updateCartQuantity(productId, 1);
                        }
                    });
                });
                
                // 2. Boutons de diminution
                buttons.decrease.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const productId = this.dataset.productId;
                        const qtyElement = this.parentNode.querySelector('.quantity-value');
                        
                        // Mise à jour visuelle immédiate
                        if (qtyElement && quantities[productId] && quantities[productId] > 1) {
                            quantities[productId]--;
                            qtyElement.textContent = quantities[productId];
                            
                            // Feedback visuel sur le bouton
                            this.classList.add('active');
                            setTimeout(() => this.classList.remove('active'), 200);
                            
                            // Mise à jour côté serveur en arrière-plan
                            updateCartQuantity(productId, -1);
                        }
                    });
                });
                
                // 3. Boutons de suppression
                buttons.remove.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const productId = this.dataset.productId;
                        const cartItem = this.closest('.cart-item');
                        
                        if (cartItem) {
                            // Verrouiller le panier brièvement
                            if (window.ElixirApp.cart) {
                                window.ElixirApp.cart.lock(true);
                            }
                            
                            // Animation de suppression immédiate 
                            cartItem.style.opacity = '0';
                            cartItem.style.height = cartItem.offsetHeight + 'px';
                            
                            setTimeout(() => {
                                cartItem.style.height = '0';
                                cartItem.style.margin = '0';
                                cartItem.style.padding = '0';
                                cartItem.style.overflow = 'hidden';
                                
                                // Supprimer après l'animation
                                setTimeout(() => {
                                    cartItem.remove();
                                    
                                    // Déverrouiller après suppression visuelle
                                    if (window.ElixirApp.cart) {
                                        window.ElixirApp.cart.lock(false);
                                    }
                                }, 250);
                            }, 100);
                            
                            // Supprimer côté serveur en arrière-plan
                            removeFromCart(productId);
                        }
                    });
                });
            }
            
            /**
             * Mise à jour de la quantité - optimisée pour l'expérience utilisateur
             */
            function updateCartQuantity(productId, change) {
                // Appel serveur en arrière-plan
                fetch('/Site-Vitrine/php/api/cart/update-quantity.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}&change=${change}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mise à jour du total et du compteur
                        updateCartSummary(data);
                        showMinimalNotification('Quantité mise à jour');
                    }
                })
                .catch(() => {
                    // En cas d'erreur, notification discrète
                    showMinimalNotification('Erreur de mise à jour', 'error');
                });
            }
            
            /**
             * Suppression - optimisée pour l'expérience utilisateur
             */
            function removeFromCart(productId) {
                // Décrémentation visuelle immédiate du compteur
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    const currentCount = parseInt(cartCount.textContent) || 0;
                    if (currentCount > 0) {
                        cartCount.textContent = currentCount - 1;
                    }
                }
                
                // Appel serveur en arrière-plan
                fetch('/Site-Vitrine/php/api/cart/remove-item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mise à jour du total uniquement (le compteur est déjà à jour)
                        updateCartSummary(data);
                        showMinimalNotification('Produit retiré');
                        
                        // Afficher panier vide si nécessaire
                        if (data.cartCount === 0) {
                            showEmptyCart();
                        }
                    }
                })
                .catch(() => {
                    // Notification discrète
                    showMinimalNotification('Erreur de suppression', 'error');
                });
            }
            
            /**
             * Mise à jour du résumé du panier (total et compteur)
             */
            function updateCartSummary(data) {
                // Mise à jour du total
                const cartTotals = document.querySelectorAll('#cart-dropdown-total');
                if (cartTotals.length && data.cartTotal) {
                    cartTotals.forEach(el => el.textContent = data.cartTotal);
                }
                
                // Mise à jour du compteur (si valeur différente de l'estimation visuelle)
                const cartCounts = document.querySelectorAll('#cart-count');
                if (cartCounts.length && data.cartCount !== undefined) {
                    cartCounts.forEach(el => el.textContent = data.cartCount);
                }
            }
            
            /**
             * Affichage du panier vide
             */
            function showEmptyCart() {
                const itemsContainers = document.querySelectorAll('.cart-dropdown-items');
                const emptyMessages = document.querySelectorAll('.cart-dropdown-empty');
                
                requestAnimationFrame(() => {
                    itemsContainers.forEach(el => el.style.display = 'none');
                    emptyMessages.forEach(el => el.style.display = 'flex');
                });
            }
            
            /**
             * Notification minimaliste et non-intrusive
             */
            function showMinimalNotification(message, type = 'success') {
                // Supprimer notifications existantes
                document.querySelectorAll('.cart-notification').forEach(n => n.remove());
                
                const notification = document.createElement('div');
                notification.className = `cart-notification mini ${type}`;
                notification.textContent = message;
                document.body.appendChild(notification);
                
                // Animation fluide
                requestAnimationFrame(() => {
                    notification.style.opacity = '1';
                    notification.style.transform = 'translateY(0)';
                    
                    // Auto-destruction rapide
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateY(10px)';
                        
                        setTimeout(() => notification.remove(), 300);
                    }, 1500);
                });
            }
            
            /**
             * Vérification du statut utilisateur - version simplifiée
             */
            function checkUserStatus() {
                const guestOptions = document.querySelector('.guest-options');
                const userOptions = document.querySelector('.user-options');
                
                if (!guestOptions || !userOptions) return;
                
                fetch('/Site-Vitrine/php/api/auth/check-status.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.isLoggedIn) {
                            requestAnimationFrame(() => {
                                guestOptions.style.display = 'none';
                                userOptions.style.display = 'block';
                            });
                        } else {
                            requestAnimationFrame(() => {
                                guestOptions.style.display = 'block';
                                userOptions.style.display = 'none';
                            });
                        }
                    })
                    .catch(() => {
                        requestAnimationFrame(() => {
                            guestOptions.style.display = 'block';
                            userOptions.style.display = 'none';
                        });
                    });
            }
        });
    })();
} else {
    // Version optimisée - moins de logs
    // console.log("Header déjà initialisé");
}