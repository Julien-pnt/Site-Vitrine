class AuthUI {
    constructor() {
        this.profileButton = document.getElementById('profile-button');
        this.initializeAuth();
    }

    async initializeAuth() {
        const isAuthenticated = await this.checkAuth();
        this.updateUI(isAuthenticated);
        this.setupEventListeners();
    }

    async checkAuth() {
        try {
            const response = await fetch('/auth/check.php');
            const data = await response.json();
            return data.authenticated;
        } catch (error) {
            console.error('Erreur de vérification auth:', error);
            return false;
        }
    }

    updateUI(isAuthenticated) {
        if (this.profileButton) {
            this.profileButton.textContent = isAuthenticated ? 'Mon compte' : 'Se connecter';
            this.profileButton.href = isAuthenticated ? '/compte' : '/login';
        }
    }

    setupEventListeners() {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        
        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleLogin(new FormData(loginForm));
            });
        }
        
        if (registerForm) {
            registerForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleRegister(new FormData(registerForm));
            });
        }
    }

    async handleRegister(formData) {
        try {
            const response = await fetch('/auth/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    nom: formData.get('nom'),
                    email: formData.get('email'),
                    password: formData.get('password')
                })
            });

            const data = await response.json();
            if (data.success) {
                // Rediriger vers la page de connexion
                window.location.href = '/login?registered=true';
            } else {
                alert(data.message || 'Erreur lors de l\'inscription');
            }
        } catch (error) {
            console.error('Erreur inscription:', error);
            alert('Erreur de connexion au serveur');
        }
    }

    async handleLogin(formData) {
        try {
            const response = await fetch('/auth/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: formData.get('email'),
                    password: formData.get('password')
                })
            });

            const data = await response.json();
            if (data.success) {
                window.location.href = '/compte';
            } else {
                alert(data.message || 'Erreur de connexion');
            }
        } catch (error) {
            console.error('Erreur login:', error);
            alert('Erreur de connexion au serveur');
        }
    }

    async logout() {
        try {
            const response = await fetch('/auth/logout.php');
            const data = await response.json();
            if (data.success) {
                window.location.href = '/';
            }
        } catch (error) {
            console.error('Erreur logout:', error);
        }
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    new AuthUI();
});