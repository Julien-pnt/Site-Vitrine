/**
 * Gestion des favoris pour les pages produits
 */
document.addEventListener('DOMContentLoaded', function() {
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist-btn');
    
    // Vérifier si l'utilisateur est connecté
    function isUserLoggedIn() {
        // Cette fonction vérifie si l'élément "account-link" est visible, ce qui indique que l'utilisateur est connecté
        return document.getElementById('account-link') && 
               document.getElementById('account-link').style.display !== 'none';
    }
    
    // Vérifier l'état des favoris pour tous les produits
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
                    }
                });
            }
        })
        .catch(error => console.error('Erreur lors de la vérification des favoris:', error));
    }
    
    // Gérer le clic sur les boutons de favoris
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            
            if (!isUserLoggedIn()) {
                // Rediriger vers la page de connexion
                window.location.href = '../auth/login.html?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            const productId = this.getAttribute('data-product-id');
            const isActive = this.classList.contains('active');
            const action = isActive ? 'remove' : 'add';
            
            // Animation pendant le chargement
            this.classList.add('loading');
            
            fetch('/Site-Vitrine/php/api/wishlist/manage.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=${action}&product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                this.classList.remove('loading');
                
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
            })
            .catch(error => {
                this.classList.remove('loading');
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue lors de la communication avec le serveur', 'error');
            });
        });
    });
    
    // Afficher une notification
    function showNotification(message, type = 'success') {
        // Vérifier si la fonction existe déjà dans main.js
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
            return;
        }
        
        // Sinon, implémenter notre propre système de notification
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Afficher la notification avec une animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Vérifier l'état des favoris au chargement
    checkWishlistStatus();
});