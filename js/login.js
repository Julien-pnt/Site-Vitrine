document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('user-icon');
    const logoutButton = document.getElementById('logout-button');

    // Vérifier l'état de connexion via une requête API
    function checkLoginStatus() {
        fetch('../php/check_login.php')
            .then(handleFetchErrors)
            .then(data => {
                updateUserIcon(data.isLoggedIn);
            })
            .catch(error => {
                console.error('Error checking login status:', error);
                showToast('Erreur de vérification du statut de connexion', 'error');
            });
    }

    function updateUserIcon(isLoggedIn) {
        if (userIcon) {
            userIcon.classList.toggle('logged-in', isLoggedIn);
            userIcon.classList.toggle('logged-out', !isLoggedIn);
        }
    }

    if (logoutButton) {
        logoutButton.addEventListener('click', handleLogout);
    }

    function handleLogout(e) {
        e.preventDefault();
        fetch('../php/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(handleFetchErrors)
        .then(data => {
            if (data.success) {
                updateUserIcon(false);
                showToast('Déconnexion réussie');
                setTimeout(() => window.location.href = '../html/login.html', 1500);
            }
        })
        .catch(error => {
            console.error('Logout error:', error);
            showToast('Erreur lors de la déconnexion', 'error');
        });
    }

    // Vérifier l'état de connexion au chargement
    checkLoginStatus();
});