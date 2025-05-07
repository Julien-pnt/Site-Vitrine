/**
 * Fichier de fonctions pour le header
 */

// Au début de votre fichier header-functions.js
console.log("Chargement du header-functions.js");

document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si l'utilisateur est connecté dès le chargement
    checkUserLoggedIn();
    
    // Gestion du menu déroulant utilisateur
    setupUserMenu();
    
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