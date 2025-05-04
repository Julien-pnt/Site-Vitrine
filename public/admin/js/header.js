/**
 * Script pour gérer le menu déroulant du header d'administration
 */
document.addEventListener('DOMContentLoaded', function() {
    // Menu déroulant utilisateur
    const userDropdown = document.getElementById('userProfileDropdown');
    const dropdownMenu = document.getElementById('userDropdownMenu');
    const dropdownArrow = document.querySelector('.dropdown-arrow');
    
    if (userDropdown && dropdownMenu) {
        // Initialisation: menu fermé
        dropdownMenu.style.display = 'none';
        dropdownMenu.style.opacity = '0';
        dropdownMenu.style.transform = 'translateY(-10px)';
        
        // Toggle du menu au clic
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const isOpen = dropdownMenu.style.display === 'block';
            
            if (isOpen) {
                // Fermeture du menu
                dropdownArrow.style.transform = 'rotate(0deg)';
                dropdownMenu.style.opacity = '0';
                dropdownMenu.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    dropdownMenu.style.display = 'none';
                }, 200);
            } else {
                // Ouverture du menu
                dropdownMenu.style.display = 'block';
                dropdownArrow.style.transform = 'rotate(180deg)';
                
                // Force reflow pour permettre la transition
                void dropdownMenu.offsetWidth;
                
                dropdownMenu.style.opacity = '1';
                dropdownMenu.style.transform = 'translateY(0)';
            }
        });
        
        // Fermeture du menu en cliquant ailleurs
        document.addEventListener('click', function() {
            if (dropdownMenu.style.display === 'block') {
                dropdownArrow.style.transform = 'rotate(0deg)';
                dropdownMenu.style.opacity = '0';
                dropdownMenu.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    dropdownMenu.style.display = 'none';
                }, 200);
            }
        });
        
        // Empêcher la fermeture quand on clique sur les éléments du menu
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});