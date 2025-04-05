/**
 * Script pour le tableau de bord administrateur d'Elixir du Temps
 */
document.addEventListener('DOMContentLoaded', function() {
    // Collapse du sidebar en version mobile
    const toggleSidebarBtn = document.getElementById('toggle-sidebar');
    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('expanded');
        });
    }

    // Initialisation des tableaux avec DataTables si disponible
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.data-table').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
            }
        });
    }

    // Gestionnaire pour les alertes fermables
    document.querySelectorAll('.alert .close').forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });

    // Gestionnaire de recherche en temps réel
    const searchInput = document.querySelector('.header-search input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            // Implémentation de la recherche en temps réel
            const searchTerm = e.target.value.toLowerCase();
            
            // Si la recherche est sur la page products.php
            if (window.location.pathname.includes('products.php')) {
                document.querySelectorAll('tbody tr').forEach(row => {
                    const productName = row.querySelector('td:first-child').textContent.toLowerCase();
                    const productRef = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    
                    if (productName.includes(searchTerm) || productRef.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    }
});

/**
 * Fonction pour confirmer une action de suppression
 * @param {string} message - Message de confirmation
 * @return {boolean} True si confirmé, false sinon
 */
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément?') {
    return confirm(message);
}

/**
 * Fonction pour prévisualiser une image avant téléchargement
 * @param {HTMLElement} input - L'élément input file
 * @param {string} previewId - L'ID de l'élément pour la prévisualisation
 */
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(previewId).style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Affiche un toast de notification
 * @param {string} message - Message à afficher
 * @param {string} type - Type de notification (success, error, warning, info)
 */
function showToast(message, type = 'success') {
    // Créer l'élément toast s'il n'existe pas
    let toast = document.getElementById('admin-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'admin-toast';
        toast.className = 'admin-toast';
        document.body.appendChild(toast);
    }
    
    // Définir la classe en fonction du type
    toast.className = `admin-toast toast-${type} toast-show`;
    toast.textContent = message;
    
    // Masquer après 3 secondes
    setTimeout(() => {
        toast.className = toast.className.replace('toast-show', '');
    }, 3000);
}