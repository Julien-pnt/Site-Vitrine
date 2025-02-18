document.addEventListener('DOMContentLoaded', function () {
    // Ajouter un produit au panier
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const produit_id = this.getAttribute('data-produit-id');
            const quantite = 1; // Par défaut, on ajoute 1 unité

            fetch('panier.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ajouter_panier=1&produit_id=${produit_id}&quantite=${quantite}`,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        updateCartIcon();
                    } else {
                        showToast(data.message || 'Erreur lors de l\'ajout au panier', 'error');
                    }
                });
        });
    });

    // Passer à la caisse
    const checkoutButton = document.querySelector('.checkout-button');
    if (checkoutButton) {
        checkoutButton.addEventListener('click', function () {
            fetch('panier.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'passer_commande=1',
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        updateCartIcon();
                        window.location.href = '../html/CommandeConfirmee.html'; // Rediriger vers une page de confirmation
                    } else {
                        showToast(data.message || 'Erreur lors de la commande', 'error');
                    }
                });
        });
    }

    // Mettre à jour l'icône du panier
    function updateCartIcon() {
        fetch('panier.php?recuperer_panier=1')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCount = document.querySelector('.cart-count');
                    const totalItems = data.panier.reduce((sum, item) => sum + item.quantite, 0);
                    cartCount.textContent = totalItems;
                }
            });
    }

    // Afficher une notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Récupérer le panier au chargement de la page
    updateCartIcon();
});