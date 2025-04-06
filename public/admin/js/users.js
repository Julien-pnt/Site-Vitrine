document.addEventListener('DOMContentLoaded', function() {
    // Gestion du modal de suppression
    const modal = document.getElementById('deleteModal');
    if (!modal) return;
    
    const deleteButtons = document.querySelectorAll('.delete-user');
    const closeButtons = document.querySelectorAll('.close-modal');
    const userName = document.getElementById('userName');
    const deleteUserId = document.getElementById('deleteUserId');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            deleteUserId.value = id;
            userName.textContent = name;
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });
    
    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });
    
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
    
    // Gestion des mots de passe
    initPasswordToggles();
    
    // Validation du mot de passe en temps réel
    initPasswordValidation();
    
    // Export de données
    initExportButtons();
    
    // Autocomplétion pour les villes basée sur le code postal
    initPostalCodeAutocomplete();
    
    // Animation des messages flash
    initFlashMessages();
});

// Gestion des toggles de mot de passe
function initPasswordToggles() {
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
}

// Validation du mot de passe en temps réel
function initPasswordValidation() {
    const passwordInput = document.getElementById('mot_de_passe');
    const confirmInput = document.getElementById('confirmer_mot_de_passe');
    
    if (!passwordInput) return;
    
    const passwordRules = document.createElement('div');
    passwordRules.className = 'password-rules';
    passwordRules.innerHTML = `
        <div class="rule length"><i class="fas fa-circle"></i> Au moins 8 caractères</div>
        <div class="rule uppercase"><i class="fas fa-circle"></i> Au moins 1 majuscule</div>
        <div class="rule lowercase"><i class="fas fa-circle"></i> Au moins 1 minuscule</div>
        <div class="rule number"><i class="fas fa-circle"></i> Au moins 1 chiffre</div>
        <div class="rule match"><i class="fas fa-circle"></i> Les mots de passe correspondent</div>
    `;
    
    // Ajouter les règles après le conteneur du mot de passe
    passwordInput.closest('.form-group').appendChild(passwordRules);
    
    function validatePassword() {
        const password = passwordInput.value;
        const confirm = confirmInput ? confirmInput.value : '';
        
        // Vérifier chaque règle
        passwordRules.querySelector('.length').className = 
            password.length >= 8 ? 'rule length valid' : 'rule length invalid';
            
        passwordRules.querySelector('.uppercase').className = 
            /[A-Z]/.test(password) ? 'rule uppercase valid' : 'rule uppercase invalid';
            
        passwordRules.querySelector('.lowercase').className = 
            /[a-z]/.test(password) ? 'rule lowercase valid' : 'rule lowercase invalid';
            
        passwordRules.querySelector('.number').className = 
            /[0-9]/.test(password) ? 'rule number valid' : 'rule number invalid';
            
        // Vérifier la correspondance uniquement si les deux champs sont remplis
        if (confirmInput && (password || confirm)) {
            passwordRules.querySelector('.match').className = 
                password === confirm ? 'rule match valid' : 'rule match invalid';
        }
        
        // Mettre à jour les icônes
        passwordRules.querySelectorAll('.valid i').forEach(icon => {
            icon.className = 'fas fa-check';
        });
        
        passwordRules.querySelectorAll('.invalid i').forEach(icon => {
            icon.className = 'fas fa-circle';
        });
    }
    
    // Valider à chaque frappe
    passwordInput.addEventListener('input', validatePassword);
    if (confirmInput) {
        confirmInput.addEventListener('input', validatePassword);
    }
    
    // Validation initiale
    validatePassword();
}

// Exportation des données utilisateurs
function initExportButtons() {
    const exportBtn = document.getElementById('exportBtn');
    if (!exportBtn) return;
    
    // Créer le menu dropdown d'exportation s'il n'existe pas déjà
    if (!document.querySelector('.export-menu')) {
        const exportMenu = document.createElement('div');
        exportMenu.className = 'export-menu';
        exportMenu.innerHTML = `
            <a href="#" data-format="csv"><i class="fas fa-file-csv"></i> Exporter en CSV</a>
            <a href="#" data-format="excel"><i class="fas fa-file-excel"></i> Exporter en Excel</a>
            <a href="#" data-format="pdf"><i class="fas fa-file-pdf"></i> Exporter en PDF</a>
        `;
        
        const exportDropdown = document.createElement('div');
        exportDropdown.className = 'export-dropdown';
        exportDropdown.appendChild(exportBtn);
        exportDropdown.appendChild(exportMenu);
        
        exportBtn.parentNode.replaceChild(exportDropdown, exportBtn);
        
        // Ajouter les gestionnaires d'événements
        document.querySelectorAll('.export-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const format = this.getAttribute('data-format');
                
                // Récupérer les filtres actuels
                const currentUrl = new URL(window.location.href);
                const params = new URLSearchParams(currentUrl.search);
                params.append('export', format);
                
                // Rediriger vers la version d'exportation
                window.location.href = `export.php?${params.toString()}`;
            });
        });
    }
}

// Autocomplétion pour les villes basée sur le code postal
function initPostalCodeAutocomplete() {
    const postalCodeInput = document.getElementById('code_postal');
    const villeInput = document.getElementById('ville');
    
    if (!postalCodeInput || !villeInput) return;
    
    let typingTimer;
    
    postalCodeInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        
        const postalCode = this.value.trim();
        
        if (postalCode.length === 5 && /^\d{5}$/.test(postalCode)) {
            typingTimer = setTimeout(() => {
                // Simuler la recherche d'une ville (à remplacer par une vraie API)
                fetchCity(postalCode).then(city => {
                    if (city) {
                        villeInput.value = city;
                        // Effet visuel subtil pour indiquer l'autocomplétude
                        villeInput.style.backgroundColor = '#e8f5e9';
                        setTimeout(() => {
                            villeInput.style.backgroundColor = '';
                        }, 500);
                    }
                });
            }, 500);
        }
    });
    
    // Fonction qui simule la recherche d'une ville par code postal
    // À remplacer par une vraie API comme https://geo.api.gouv.fr/communes?codePostal=XXXXX
    async function fetchCity(postalCode) {
        // Exemple de simulation - à remplacer par une vraie API
        const mockDatabase = {
            '75001': 'Paris 1er',
            '75002': 'Paris 2e',
            '69001': 'Lyon 1er',
            '69002': 'Lyon 2e',
            '13001': 'Marseille 1er',
            '13002': 'Marseille 2e',
            '33000': 'Bordeaux',
            '31000': 'Toulouse',
            '59000': 'Lille'
        };
        
        return new Promise(resolve => {
            setTimeout(() => {
                resolve(mockDatabase[postalCode] || null);
            }, 300);
        });
    }
}

// Animation des messages flash
function initFlashMessages() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Disparition automatique après 5 secondes
        setTimeout(() => {
            fadeOut(alert);
        }, 5000);
        
        // Permettre la fermeture manuelle
        const closeBtn = alert.querySelector('.close-alert');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => fadeOut(alert));
        }
    });
    
    function fadeOut(element) {
        element.style.opacity = '1';
        
        (function fade() {
            if ((element.style.opacity -= 0.1) < 0) {
                element.style.display = 'none';
            } else {
                requestAnimationFrame(fade);
            }
        })();
    }
}