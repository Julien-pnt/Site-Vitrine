// Simuler l'état de connexion (true = connecté, false = déconnecté)
let isLoggedIn = false; // Changez cette valeur pour tester les deux états

// Sélectionnez l'icône utilisateur
const userIcon = document.getElementById('user-icon');

// Fonction pour mettre à jour l'affichage en fonction de l'état de connexion
function updateUserIcon() {
    if (userIcon) {
        if (isLoggedIn) {
            userIcon.classList.remove('logged-out');
            userIcon.classList.add('logged-in');
        } else {
            userIcon.classList.remove('logged-in');
            userIcon.classList.add('logged-out');
        }
    }
}

// Mettre à jour l'affichage au chargement de la page
updateUserIcon();

// Gérer la déconnexion
const logoutButton = document.getElementById('logout-button');
if (logoutButton) {
    logoutButton.addEventListener('click', (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut du lien
        isLoggedIn = false; // Simuler la déconnexion
        updateUserIcon(); // Mettre à jour l'affichage
        alert('Vous êtes déconnecté.'); // Afficher un message de déconnexion
    });
}