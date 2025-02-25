document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour vérifier l'état de connexion
    function checkLoginStatus() {
        fetch('../php/check_session.php')
            .then(response => response.json())
            .then(data => {
                const userIcon = document.querySelector('.user-icon');
                const profileImage = document.getElementById('profile-image');
                const loginLink = document.getElementById('login-link');
                const registerLink = document.getElementById('register-link');
                const accountLink = document.getElementById('account-link');
                const ordersLink = document.getElementById('orders-link');
                const logoutLink = document.getElementById('logout-link');
                
                if (data.logged_in) {
                    // Utilisateur connecté
                    userIcon.classList.add('logged-in');
                    
                    // Si l'utilisateur a une image de profil personnalisée
                    if (data.profile_image) {
                        profileImage.src = data.profile_image;
                    } else {
                        profileImage.src = '../img/user-logged.png'; // Image par défaut pour utilisateur connecté
                    }
                    
                    // Afficher les liens pour utilisateur connecté
                    loginLink.style.display = 'none';
                    registerLink.style.display = 'none';
                    accountLink.style.display = 'block';
                    ordersLink.style.display = 'block';
                    logoutLink.style.display = 'block';
                    
                    // Ajouter le nom d'utilisateur si disponible
                    if (data.username) {
                        const usernameElement = document.createElement('a');
                        usernameElement.href = '../php/dashboard.php';
                        usernameElement.textContent = 'Bonjour, ' + data.username;
                        usernameElement.style.fontWeight = 'bold';
                        usernameElement.style.color = '#ffd700';
                        usernameElement.style.borderBottom = '1px solid rgba(255, 215, 0, 0.3)';
                        usernameElement.style.marginBottom = '0.5rem';
                        usernameElement.style.paddingBottom = '0.5rem';
                        
                        // Insérer au début du menu déroulant
                        const dropdownMenu = document.querySelector('.dropdown-menu');
                        dropdownMenu.insertBefore(usernameElement, dropdownMenu.firstChild);
                    }
                } else {
                    // Utilisateur déconnecté
                    userIcon.classList.remove('logged-in');
                    profileImage.src = '../img/user-default.png';
                    
                    // Afficher les liens pour utilisateur déconnecté
                    loginLink.style.display = 'block';
                    registerLink.style.display = 'block';
                    accountLink.style.display = 'none';
                    ordersLink.style.display = 'none';
                    logoutLink.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Erreur lors de la vérification du statut:', error);
            });
    }
    
    // Appeler la fonction au chargement de la page
    checkLoginStatus();
    
    // Ajouter un effet visuel au survol de l'icône utilisateur
    const userIcon = document.querySelector('.user-icon');
    if (userIcon) {
        userIcon.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        userIcon.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
    
    // Gérer la déconnexion
    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Animation de déconnexion
            userIcon.classList.add('logging-out');
            
            // Appel au serveur pour la déconnexion
            fetch('../php/logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Afficher un message de succès
                        showToast('Déconnexion réussie');
                        
                        // Rediriger vers la page d'accueil après un court délai
                        setTimeout(() => {
                            window.location.href = '../html/Accueil.html';
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la déconnexion:', error);
                });
        });
    }
    
    // Fonction pour afficher un message toast
    function showToast(message, isError = false) {
        // Créer l'élément toast s'il n'existe pas déjà
        let toast = document.querySelector('.toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'toast';
            document.body.appendChild(toast);
        }
        
        // Configurer le message et le style
        toast.textContent = message;
        if (isError) {
            toast.classList.add('error');
        } else {
            toast.classList.remove('error');
        }
        
        // Afficher le toast
        toast.classList.add('show');
        
        // Masquer le toast après 3 secondes
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
});