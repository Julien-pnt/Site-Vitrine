/**
 * Script pour le tableau de bord administrateur d'Elixir du Temps
 */
(function($) {
    $(document).ready(function() {
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
            document.querySelectorAll('.data-table').forEach(table => {
                new DataTable(table, {
                    responsive: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
                    }
                });
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

        // Toggle sidebar on mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
            
            // Fermer la sidebar en cliquant à l'extérieur
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 992 && 
                    sidebar.classList.contains('show') && 
                    !sidebar.contains(e.target) && 
                    e.target !== sidebarToggle) {
                    sidebar.classList.remove('show');
                }
            });
        }
        
        // Fermer les messages d'alerte
        const closeButtons = document.querySelectorAll('.close-alert');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const alert = this.closest('.alert');
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 300);
            });
        });
        
        // Toggle password visibility
        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Gestion de la modal de suppression
        const deleteModal = document.getElementById('deleteModal');
        const deleteButtons = document.querySelectorAll('.delete-user');
        const closeModals = document.querySelectorAll('.close-modal');
        const userName = document.getElementById('userName');
        const deleteUserId = document.getElementById('deleteUserId');
        
        if (deleteModal && deleteButtons) {
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    
                    deleteUserId.value = id;
                    userName.textContent = name;
                    
                    deleteModal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                });
            });
            
            function closeModal() {
                deleteModal.style.display = 'none';
                document.body.style.overflow = '';
            }
            
            closeModals.forEach(button => {
                button.addEventListener('click', closeModal);
            });
            
            window.addEventListener('click', function(event) {
                if (event.target === deleteModal) {
                    closeModal();
                }
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && deleteModal.style.display === 'block') {
                    closeModal();
                }
            });
        }

        // Animations des cartes
        const cards = document.querySelectorAll('.card');
        if (cards.length) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
        }
    });
})(jQuery);

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