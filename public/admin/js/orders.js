// JavaScript pour gérer l'affichage des détails de commande dans une fenêtre modale
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour ouvrir le modal avec les détails de la commande
    window.openOrderDetails = function(orderId) {
        const modal = document.getElementById('orderDetailsModal');
        const modalContent = modal.querySelector('.modal-body');
        const modalTitle = modal.querySelector('.modal-title');
        
        // Afficher le modal de chargement
        modalContent.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Chargement des détails...</p></div>';
        modalTitle.textContent = 'Détail de la commande';
        document.getElementById('orderModal').style.display = 'block';
        
        // Charger les détails de la commande via AJAX
        fetch(`api/get-order-details.php?id=${orderId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                console.log('Données reçues:', data); // Pour déboguer
                
                // Formater les données pour l'affichage
                let statusClass = {
                    'en_attente': 'warning',
                    'payee': 'info',
                    'en_preparation': 'primary',
                    'expediee': 'secondary',
                    'livree': 'success',
                    'annulee': 'danger',
                    'remboursee': 'dark'
                };
                
                let statusLabel = {
                    'en_attente': 'En attente',
                    'payee': 'Payée',
                    'en_preparation': 'En préparation',
                    'expediee': 'Expédiée',
                    'livree': 'Livrée',
                    'annulee': 'Annulée',
                    'remboursee': 'Remboursée'
                };
                
                // Mise en forme de la date
                const dateCommande = new Date(data.date_commande);
                const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                const dateFormatee = dateCommande.toLocaleDateString('fr-FR', options);
                
                // Construire le contenu HTML pour les infos générales
                let html = `
                <div class="order-details">
                    <div class="order-header">
                        <div class="order-ref">
                            <h3>Commande #${data.reference}</h3>
                            <span class="badge badge-${statusClass[data.statut]}">${statusLabel[data.statut]}</span>
                        </div>
                        <div class="order-date">
                            <p><i class="fas fa-calendar-alt"></i> ${dateFormatee}</p>
                        </div>
                    </div>
                    
                    <div class="order-info-grid">
                        <div class="order-info-card">
                            <h4><i class="fas fa-user"></i> Client</h4>
                            <p><strong>Nom:</strong> ${data.client_nom}</p>
                            <p><strong>Email:</strong> ${data.client_email}</p>
                            <p><strong>Téléphone:</strong> ${data.client_telephone || 'Non renseigné'}</p>
                        </div>
                        
                        <div class="order-info-card">
                            <h4><i class="fas fa-truck"></i> Livraison</h4>
                            <p>${data.adresse_livraison.replace(/\n/g, '<br>')}</p>
                        </div>
                        
                        <div class="order-info-card">
                            <h4><i class="fas fa-file-invoice"></i> Facturation</h4>
                            <p>${data.adresse_facturation.replace(/\n/g, '<br>')}</p>
                        </div>
                        
                        <div class="order-info-card">
                            <h4><i class="fas fa-money-check-alt"></i> Paiement</h4>
                            <p><strong>Mode:</strong> ${data.mode_paiement.replace('_', ' ').charAt(0).toUpperCase() + data.mode_paiement.replace('_', ' ').slice(1)}</p>
                            <p><strong>Total:</strong> ${parseFloat(data.total).toFixed(2)} €</p>
                            <p><strong>Frais de livraison:</strong> ${parseFloat(data.frais_livraison).toFixed(2)} €</p>
                            <p><strong>TVA:</strong> ${parseFloat(data.total_taxe).toFixed(2)} €</p>
                        </div>
                    </div>`;
                
                // Vérifier si les données des articles sont présentes et ajouter la section articles
                if (data.items && data.items.length > 0) {
                    html += `
                    <div class="order-items">
                        <h4><i class="fas fa-box-open"></i> Articles commandés</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    
                    data.items.forEach(item => {
                        html += `
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            ${item.image ? `<img src="/Site-Vitrine/public/images/produits/${item.image}" alt="${item.nom_produit}" class="product-thumb">` : ''}
                                            <div>
                                                <p class="product-name">${item.nom_produit}</p>
                                                <p class="product-ref">Réf: ${item.reference_produit || 'N/A'}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${parseFloat(item.prix_unitaire).toFixed(2)} €</td>
                                    <td>${item.quantite}</td>
                                    <td>${parseFloat(item.prix_total).toFixed(2)} €</td>
                                </tr>`;
                    });
                    
                    html += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Sous-total:</strong></td>
                                    <td>${parseFloat(data.total_produits).toFixed(2)} €</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Frais de livraison:</strong></td>
                                    <td>${parseFloat(data.frais_livraison).toFixed(2)} €</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>TVA (20%):</strong></td>
                                    <td>${parseFloat(data.total_taxe).toFixed(2)} €</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                    <td><strong>${parseFloat(data.total).toFixed(2)} €</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>`;
                } else {
                    html += `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Aucun article trouvé pour cette commande.
                    </div>`;
                }
                
                // Ajouter les notes si disponibles
                if (data.notes) {
                    html += `
                    <div class="order-notes">
                        <h4><i class="fas fa-sticky-note"></i> Notes</h4>
                        <div class="notes-content">
                            ${data.notes.replace(/\n/g, '<br>')}
                        </div>
                    </div>`;
                }
                
                // Formulaire de mise à jour du statut
                html += `
                <div class="order-actions">
                    <h4><i class="fas fa-cog"></i> Actions</h4>
                    <form action="orders.php" method="post" class="status-form">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" value="${data.id}">
                        <input type="hidden" name="redirect_to" value="orders.php">
                        
                        <div class="form-group">
                            <label for="status">Mettre à jour le statut:</label>
                            <select name="status" id="status" class="form-control">
                                <option value="en_attente" ${data.statut === 'en_attente' ? 'selected' : ''}>En attente</option>
                                <option value="payee" ${data.statut === 'payee' ? 'selected' : ''}>Payée</option>
                                <option value="en_preparation" ${data.statut === 'en_preparation' ? 'selected' : ''}>En préparation</option>
                                <option value="expediee" ${data.statut === 'expediee' ? 'selected' : ''}>Expédiée</option>
                                <option value="livree" ${data.statut === 'livree' ? 'selected' : ''}>Livrée</option>
                                <option value="annulee" ${data.statut === 'annulee' ? 'selected' : ''}>Annulée</option>
                                <option value="remboursee" ${data.statut === 'remboursee' ? 'selected' : ''}>Remboursée</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="note">Ajouter une note:</label>
                            <textarea name="note" id="note" class="form-control" rows="3" placeholder="Entrez une note facultative..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </form>
                </div>`;
                
                // Injecter le HTML dans le modal
                modalContent.innerHTML = html;
                modalTitle.textContent = `Commande #${data.reference}`;
            })
            .catch(error => {
                console.error('Erreur:', error);
                modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <h4><i class="fas fa-exclamation-circle"></i> Erreur</h4>
                    <p>Impossible de charger les détails de la commande: ${error.message}</p>
                </div>`;
            });
    };
    
    // Fermer le modal
    window.closeOrderModal = function() {
        document.getElementById('orderModal').style.display = 'none';
    };
    
    // Fermer le modal quand on clique en dehors
    window.onclick = function(event) {
        const modal = document.getElementById('orderModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
    
    // Fermer le modal avec la touche Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('orderModal').style.display = 'none';
        }
    });
});