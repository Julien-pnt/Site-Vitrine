/**
 * Gestion des favoris pour les pages produits
 * Version 3.0
 */

// Variables globales pour l'état d'authentification
let userAuthStatus = {
    isLoggedIn: false,
    isAdmin: false,
    userId: null
};

// Vérifier l'état de connexion via l'API
async function checkAuthStatus() {
    try {
        const response = await fetch('/Site-Vitrine/php/api/auth/check-status.php');
        const data = await response.json();
        
        if (data.success) {
            userAuthStatus = {
                isLoggedIn: data.isLoggedIn,
                isAdmin: data.isAdmin,
                userId: data.userId
            };
            return userAuthStatus;
        }
        return { isLoggedIn: false, isAdmin: false };
    } catch (error) {
        console.error('Erreur lors de la vérification de l\'authentification:', error);
        return { isLoggedIn: false, isAdmin: false };
    }
}

// Vérifier si l'utilisateur est connecté (méthode améliorée)
function isUserLoggedIn() {
    // Avant de tester la variable globale, essayer également de vérifier le DOM
    const domLoggedIn = (
        document.getElementById('account-link') && 
        document.getElementById('account-link').style.display !== 'none'
    ) || (
        document.querySelector('.user-menu') && 
        !document.querySelector('.login-link')
    );
    
    // Si l'utilisateur semble connecté selon le DOM, on considère qu'il l'est
    // même si la variable globale dit le contraire
    return domLoggedIn || userAuthStatus.isLoggedIn;
}

document.addEventListener('DOMContentLoaded', async function() {
    // Vérifier l'état d'authentification au chargement
    await checkAuthStatus();
    
    // Récupérer tous les boutons d'ajout aux favoris
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist-btn');
    
    // Vérifier l'état des favoris pour tous les produits
    checkWishlistStatus();
    
    // Fonction pour vérifier l'état des favoris
    function checkWishlistStatus() {
        if (!isUserLoggedIn()) return;
        
        // Récupérer tous les IDs de produits sur la page
        const productIds = [];
        wishlistButtons.forEach(button => {
            const productId = button.getAttribute('data-product-id');
            if (productId) {
                productIds.push(productId);
            }
        });
        
        if (productIds.length === 0) return;
        
        // Vérifier l'état de chaque produit avec une seule requête
        fetch('/Site-Vitrine/php/api/wishlist/batch-check.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'product_ids=' + JSON.stringify(productIds)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.favorites) {
                // Mettre à jour l'interface pour les produits en favoris
                wishlistButtons.forEach(button => {
                    const productId = button.getAttribute('data-product-id');
                    if (data.favorites.includes(parseInt(productId))) {
                        button.classList.add('active');
                        button.setAttribute('title', 'Retirer des favoris');
                    } else {
                        button.classList.remove('active');
                        button.setAttribute('title', 'Ajouter aux favoris');
                    }
                });
            }
        })
        .catch(error => console.error('Erreur lors de la vérification des favoris:', error));
    }
    
    // Gérer le clic sur les boutons de favoris
    wishlistButtons.forEach(button => {
        // Remplacer tous les gestionnaires existants
        button.onclick = async function(event) {
            event.preventDefault();
            
            // Ignorer si déjà en cours de traitement
            if (this.classList.contains('loading')) {
                return;
            }
            
            // Vérifier d'abord si l'utilisateur est connecté
            if (!isUserLoggedIn()) {
                // Double vérification avec l'API
                await checkAuthStatus();
                
                // Si toujours pas connecté, rediriger
                if (!userAuthStatus.isLoggedIn) {
                    window.location.href = '../auth/login.php?redirect=' + encodeURIComponent(window.location.href);
                    return;
                }
            }
            
            const productId = this.getAttribute('data-product-id');
            const isActive = this.classList.contains('active');
            const action = isActive ? 'remove' : 'add';
            
            // Animation pendant le chargement
            this.classList.add('loading');
            this.disabled = true;
            
            try {
                const response = await fetch('/Site-Vitrine/php/api/wishlist/manage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=${action}&product_id=${productId}`
                });
                
                const data = await response.json();
                
                this.classList.remove('loading');
                this.disabled = false;
                
                if (data.success) {
                    if (action === 'add') {
                        this.classList.add('active');
                        this.setAttribute('title', 'Retirer des favoris');
                        showNotification('Produit ajouté aux favoris', 'success');
                    } else {
                        this.classList.remove('active');
                        this.setAttribute('title', 'Ajouter aux favoris');
                        showNotification('Produit retiré des favoris', 'success');
                    }
                } else {
                    showNotification(data.message || 'Une erreur est survenue', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                this.classList.remove('loading');
                this.disabled = false;
                showNotification('Une erreur est survenue lors de la communication avec le serveur', 'error');
            }
        };
    });
    
    // Afficher une notification
    function showNotification(message, type = 'success') {
        // Réutiliser la fonction existante si disponible
        if (typeof window.showNotification === 'function') {
            window.showNotification(message);
            return;
        }
        
        // Supprimer les notifications existantes
        const existingNotification = document.querySelector('.cart-notification');
        if (existingNotification) {
            document.body.removeChild(existingNotification);
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
});