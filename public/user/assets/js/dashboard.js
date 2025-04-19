document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la barre latérale responsive
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.dashboard-sidebar');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Fermer la barre latérale en cliquant en dehors
    document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
        
        if (window.innerWidth < 992 && !isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });
    
    // Masquer les alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });
    
    // Validation du formulaire de mot de passe
    const passwordForm = document.querySelector('.password-form');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert("Les mots de passe ne correspondent pas.");
                return false;
            }
            
            // Vérification de la complexité du mot de passe
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            
            if (!passwordRegex.test(newPassword)) {
                e.preventDefault();
                alert("Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, un chiffre et un caractère spécial.");
                return false;
            }
        });
    }

    // Gestion des animations pour les badges de statut
    const statusBadges = document.querySelectorAll('.status-badge');
    
    statusBadges.forEach(badge => {
        badge.style.opacity = '0';
        badge.style.transform = 'translateY(10px)';
        
        setTimeout(() => {
            badge.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            badge.style.opacity = '1';
            badge.style.transform = 'translateY(0)';
        }, 100);
    });
    
    // Animation pour la barre de statut
    const statusSteps = document.querySelectorAll('.status-step');
    const statusLines = document.querySelectorAll('.status-line');
    
    if (statusSteps.length > 0) {
        statusSteps.forEach((step, index) => {
            setTimeout(() => {
                step.style.opacity = '1';
                step.style.transform = 'translateY(0)';
                
                if (index < statusLines.length) {
                    setTimeout(() => {
                        statusLines[index].style.width = '100%';
                    }, 300);
                }
            }, index * 300);
        });
    }
});

// Filtres des commandes
const statusFilter = document.getElementById('status-filter');
if (statusFilter) {
    statusFilter.addEventListener('change', function() {
        // Soumettre automatiquement le formulaire lorsque le filtre change
        this.closest('form').submit();
    });
}