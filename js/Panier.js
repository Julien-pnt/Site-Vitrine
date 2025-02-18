document.addEventListener('DOMContentLoaded', function () {
    // Ajouter un produit au panier
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const produit_id = this.getAttribute('data-produit-id'); // Récupérer l'ID du produit
            const quantite = 1; // Par défaut, on ajoute 1 unité

            // Envoyer une requête POST pour ajouter le produit au panier
            fetch('../php/panier.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ajouter_panier=1&produit_id=${produit_id}&quantite=${quantite}`,
            })
                .then(response => response.json()) // Convertir la réponse en JSON
                .then(data => {
                    if (data.success) {
                        showToast(data.message); // Afficher un message de succès
                        updateCartIcon(); // Mettre à jour l'icône du panier
                    } else {
                        showToast(data.message || 'Erreur lors de l\'ajout au panier', 'error'); // Afficher un message d'erreur
                    }
                });
        });
    });

    // Passer à la caisse
    const checkoutButton = document.querySelector('.checkout-button');
    if (checkoutButton) {
        checkoutButton.addEventListener('click', function () {
            // Envoyer une requête POST pour passer la commande
            fetch('panier.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'passer_commande=1',
            })
                .then(response => response.json()) // Convertir la réponse en JSON
                .then(data => {
                    if (data.success) {
                        showToast(data.message); // Afficher un message de succès
                        updateCartIcon(); // Mettre à jour l'icône du panier
                        window.location.href = '../html/CommandeConfirmee.html'; // Rediriger vers une page de confirmation
                    } else {
                        showToast(data.message || 'Erreur lors de la commande', 'error'); // Afficher un message d'erreur
                    }
                });
        });
    }

    // Mettre à jour l'icône du panier
    function updateCartIcon() {
        // Envoyer une requête GET pour récupérer le panier
        fetch('../php/panier.php?recuperer_panier=1')
            .then(response => response.json()) // Convertir la réponse en JSON
            .then(data => {
                if (data.success) {
                    const cartCount = document.querySelector('.cart-count'); // Sélectionner l'élément de l'icône du panier
                    const totalItems = data.panier.reduce((sum, item) => sum + item.quantite, 0); // Calculer le nombre total d'articles
                    cartCount.textContent = totalItems; // Mettre à jour le texte de l'icône du panier
                }
            });
    }

    // Afficher une notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div'); // Créer un élément div pour la notification
        toast.className = `toast ${type}`; // Ajouter des classes pour le style
        toast.textContent = message; // Ajouter le message de la notification
        document.body.appendChild(toast); // Ajouter la notification au corps du document

        // Supprimer la notification après 3 secondes
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Récupérer le panier au chargement de la page
    updateCartIcon();
});