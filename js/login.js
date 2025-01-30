// Sélectionnez l'icône utilisateur et le menu déroulant
const userIcon = document.querySelector('.user-icon');
const dropdownMenu = userIcon.querySelector('.dropdown-menu');

// Ajoutez un écouteur d'événement pour afficher/cacher le menu
userIcon.addEventListener('click', (e) => {
    e.stopPropagation(); // Empêche la propagation de l'événement
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
});

// Fermez le menu si l'utilisateur clique ailleurs sur la page
document.addEventListener('click', () => {
    dropdownMenu.style.display = 'none';
});