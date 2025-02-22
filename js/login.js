document.addEventListener('DOMContentLoaded', function() {
    // Récupération des éléments DOM
    const loginForm = document.getElementById('login-form');
    const userIcon = document.getElementById('user-icon');
    const logoutButton = document.getElementById('logout-button');
    const errorMessage = document.getElementById('error-message');

    // Configuration
    const CONFIG = {
        MIN_PASSWORD_LENGTH: 8,
        LOGIN_ATTEMPTS_LIMIT: 3,
        LOCKOUT_DURATION: 15 * 60 * 1000, // 15 minutes en millisecondes
    };

    // Gestionnaire d'état de connexion
    const LoginState = {
        attempts: parseInt(localStorage.getItem('loginAttempts') || '0'),
        lockoutUntil: parseInt(localStorage.getItem('lockoutUntil') || '0'),

        // Incrémente le nombre de tentatives de connexion
        incrementAttempts() {
            this.attempts++;
            localStorage.setItem('loginAttempts', this.attempts.toString());
            
            if (this.attempts >= CONFIG.LOGIN_ATTEMPTS_LIMIT) {
                this.setLockout();
            }
        },

        // Définit la période de verrouillage
        setLockout() {
            const lockoutTime = Date.now() + CONFIG.LOCKOUT_DURATION;
            localStorage.setItem('lockoutUntil', lockoutTime.toString());
            this.lockoutUntil = lockoutTime;
        },

        // Réinitialise les tentatives de connexion
        resetAttempts() {
            this.attempts = 0;
            this.lockoutUntil = 0;
            localStorage.removeItem('loginAttempts');
            localStorage.removeItem('lockoutUntil');
        },

        // Vérifie si l'utilisateur est verrouillé
        isLockedOut() {
            return Date.now() < this.lockoutUntil;
        }
    };

    // Gestionnaire de session
    const SessionManager = {
        // Vérifie l'état de connexion
        checkLoginStatus() {
            return fetch('../php/check_login.php', {
                method: 'GET',
                credentials: 'same-origin'
            })
            .then(handleFetchErrors)
            .then(data => {
                updateUIState(data.isLoggedIn);
                return data.isLoggedIn;
            })
            .catch(error => {
                console.error('Error checking login status:', error);
                showToast('Erreur de vérification du statut de connexion', 'error');
                return false;
            });
        },

        // Gère la connexion
        login(formData) {
            if (LoginState.isLockedOut()) {
                const remainingTime = Math.ceil((LoginState.lockoutUntil - Date.now()) / 1000 / 60);
                throw new Error(`Compte temporairement bloqué. Réessayez dans ${remainingTime} minutes.`);
            }

            return fetch('../php/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify(formData)
            })
            .then(handleFetchErrors)
            .then(data => {
                if (data.success) {
                    LoginState.resetAttempts();
                    updateUIState(true);
                    return data;
                } else {
                    LoginState.incrementAttempts();
                    throw new Error(data.message || 'Erreur de connexion');
                }
            });
        },

        // Gère la déconnexion
        logout() {
            return fetch('../php/logout.php', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(handleFetchErrors)
            .then(data => {
                if (data.success) {
                    updateUIState(false);
                    return data;
                }
                throw new Error(data.message || 'Erreur de déconnexion');
            });
        }
    };

    // Gestionnaire de formulaire
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearError();

            try {
                // Validation des entrées
                const formData = {
                    email: this.email.value.trim(),
                    password: this.password.value
                };

                if (!validateEmail(formData.email)) {
                    throw new Error('Email invalide');
                }

                if (formData.password.length < CONFIG.MIN_PASSWORD_LENGTH) {
                    throw new Error(`Le mot de passe doit contenir au moins ${CONFIG.MIN_PASSWORD_LENGTH} caractères`);
                }

                showLoadingState(true);
                const response = await SessionManager.login(formData);
                
                if (response.success) {
                    showToast('Connexion réussie');
                    window.location.href = '../html/dashboard.html';
                }
            } catch (error) {
                showError(error.message);
            } finally {
                showLoadingState(false);
            }
        });
    }

    // Gestionnaire de déconnexion
    if (logoutButton) {
        logoutButton.addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const response = await SessionManager.logout();
                if (response.success) {
                    showToast('Déconnexion réussie');
                    setTimeout(() => window.location.href = '../html/login.html', 1500);
                }
            } catch (error) {
                showToast(error.message, 'error');
            }
        });
    }

    // Fonctions utilitaires

    // Met à jour l'état de l'interface utilisateur
    function updateUIState(isLoggedIn) {
        if (userIcon) {
            userIcon.classList.toggle('logged-in', isLoggedIn);
            userIcon.classList.toggle('logged-out', !isLoggedIn);
        }
        if (logoutButton) {
            logoutButton.style.display = isLoggedIn ? 'block' : 'none';
        }
    }

    // Valide l'email
    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Gère les erreurs de fetch
    function handleFetchErrors(response) {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    }

    // Affiche un message d'erreur
    function showError(message) {
        if (errorMessage) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
        }
    }

    // Efface le message d'erreur
    function clearError() {
        if (errorMessage) {
            errorMessage.textContent = '';
            errorMessage.style.display = 'none';
        }
    }

    // Affiche l'état de chargement
    function showLoadingState(isLoading) {
        if (loginForm) {
            const submitButton = loginForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = isLoading;
                submitButton.textContent = isLoading ? 'Connexion...' : 'Se connecter';
            }
        }
    }

    // Affiche un toast
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Vérifier l'état de connexion au chargement
    SessionManager.checkLoginStatus();
});