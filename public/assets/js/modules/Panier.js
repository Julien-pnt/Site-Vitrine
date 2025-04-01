document.addEventListener('DOMContentLoaded', function() {
    // Éléments relatifs au panier
    const cartIcon = document.querySelector('.cart-icon');
    const cartBadge = document.querySelector('.cart-badge');
    const cartDropdown = document.querySelector('.cart-dropdown');
    const cartDropdownItems = document.querySelector('.cart-dropdown-items');
    const cartDropdownTotal = document.querySelector('.cart-dropdown-total-value');
    const cartDropdownEmpty = document.querySelector('.cart-dropdown-empty');
    
    // Fonction pour mettre à jour le panier
    function updateCartDisplay() {
        // Si l'utilisateur est connecté, récupérer le contenu du panier
        fetch('../php/panier.php?recuperer_panier=1')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const { articles, total, nombre_articles } = data.data;
                    
                    // Mettre à jour le compteur
                    if (cartBadge) {
                        cartBadge.textContent = nombre_articles;
                        
                        if (nombre_articles > 0) {
                            cartBadge.classList.add('has-items');
                            cartBadge.style.display = 'flex';
                        } else {
                            cartBadge.classList.remove('has-items');
                            cartBadge.style.display = 'none';
                        }
                    }
                    
                    // Mettre à jour le contenu du dropdown
                    if (cartDropdownItems && cartDropdownTotal && cartDropdownEmpty) {
                        cartDropdownItems.innerHTML = '';
                        
                        if (articles.length === 0) {
                            cartDropdownItems.style.display = 'none';
                            cartDropdownEmpty.style.display = 'block';
                            document.querySelector('.cart-dropdown-total').style.display = 'none';
                        } else {
                            cartDropdownItems.style.display = 'block';
                            cartDropdownEmpty.style.display = 'none';
                            document.querySelector('.cart-dropdown-total').style.display = 'flex';
                            
                            // Afficher les articles
                            articles.forEach(article => {
                                const itemHTML = `
                                    <div class="cart-dropdown-item" data-id="${article.id}">
                                        <img src="${article.image}" alt="${article.nom}" class="cart-dropdown-item-image">
                                        <div class="cart-dropdown-item-details">
                                            <h4>${article.nom}</h4>
                                            <p>Qté: ${article.quantite}</p>
                                        </div>
                                                                               <div class="cart-dropdown-item-price">${parseFloat(article.prix).toLocaleString('fr-FR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        })} €</div>
                                        <button class="cart-dropdown-item-remove" data-id="${article.id}">×</button>
                                    </div>
                                `;
                                cartDropdownItems.innerHTML += itemHTML;
                            });
                            
                            // Mettre à jour le total
                            cartDropdownTotal.textContent = parseFloat(total).toLocaleString('fr-FR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + ' €';
                            
                            // Ajouter les gestionnaires d'événements pour les boutons de suppression
                            document.querySelectorAll('.cart-dropdown-item-remove').forEach(button => {
                                button.addEventListener('click', function(e) {
                                    e.stopPropagation(); // Empêcher la fermeture du dropdown
                                    const productId = this.dataset.id;
                                    removeFromCart(productId);
                                });
                            });
                        }
                    }
                } else {
                    console.error('Erreur lors de la récupération du panier:', data.message);
                    
                    // Utilisateur probablement déconnecté, masquer le badge
                    if (cartBadge) {
                        cartBadge.style.display = 'none';
                    }
                    
                    if (cartDropdownEmpty) {
                        cartDropdownItems.style.display = 'none';
                        cartDropdownEmpty.style.display = 'block';
                        document.querySelector('.cart-dropdown-total').style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Erreur réseau:', error);
            });
    }
    
    // Fonction pour supprimer un article du panier
    function removeFromCart(productId) {
        const formData = new FormData();
        formData.append('produit_id', productId);
        formData.append('supprimer_article', 1);
        
        fetch('../php/panier.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-Token': getCsrfToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                updateCartDisplay();
            } else {
                showToast(data.message, true);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Une erreur est survenue', true);
        });
    }
    
    // Fonction pour obtenir le token CSRF
    function getCsrfToken() {
        // Cette fonction suppose que le token CSRF est stocké dans un élément meta
        // ou est disponible via une API
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            return metaToken.getAttribute('content');
        }
        
        // Si nous n'avons pas de méta tag, on renvoie une chaîne vide (sera géré côté serveur)
        return '';
    }
    
    // Fonction pour ajouter un article au panier
    function addToCart(productId, quantity = 1) {
        const formData = new FormData();
        formData.append('produit_id', productId);
        formData.append('quantite', quantity);
        formData.append('ajouter_panier', 1);
        
        fetch('../php/panier.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-Token': getCsrfToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                updateCartDisplay();
            } else {
                showToast(data.message, true);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Une erreur est survenue', true);
        });
    }
    
    // Fonction pour afficher un message toast
    function showToast(message, isError = false) {
        let toast = document.querySelector('.toast');
        
        // Créer le toast s'il n'existe pas
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'toast';
            document.body.appendChild(toast);
        }
        
        // Appliquer le style d'erreur si nécessaire
        if (isError) {
            toast.classList.add('error');
        } else {
            toast.classList.remove('error');
        }
        
        // Afficher le message
        toast.textContent = message;
        toast.classList.add('show');
        
        // Cacher après 3 secondes
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
    
    // Mettre à jour le panier au chargement de la page
    updateCartDisplay();
    
    // Gérer les clics sur les boutons "Ajouter au panier" pour toutes les pages
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!this.closest('form')) { // Ne s'applique que pour les boutons autonomes
                e.preventDefault();
                const productId = this.dataset.id;
                
                if (productId) {
                    addToCart(productId);
                }
            }
        });
    });
    
    // Gérer les soumissions de formulaire pour ajouter au panier
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = this.querySelector('[name="produit_id"]').value;
            const quantity = this.querySelector('[name="quantite"]').value || 1;
            
            addToCart(productId, quantity);
        });
    });
});
