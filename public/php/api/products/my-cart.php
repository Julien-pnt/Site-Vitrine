<?php
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer le contenu du panier
$stmt = $pdo->prepare("
    SELECT p.id, p.nom, p.prix, p.image, pa.quantite
    FROM panier pa
    JOIN produits p ON pa.produit_id = p.id
    WHERE pa.utilisateur_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$articles = $stmt->fetchAll();

// Calculer le total du panier
$total = 0;
foreach ($articles as $article) {
    $total += $article['prix'] * $article['quantite'];
}

// Générer un token CSRF s'il n'existe pas
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - Elixir du Temps</title>
    <link rel="stylesheet" href="../css/Styles.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
    <style>
        .cart-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .cart-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #ffd700;
            font-size: 2.5rem;
        }
        
        .cart-empty {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 1rem;
            padding: 3rem;
            text-align: center;
        }
        
        .cart-empty h3 {
            color: #ffd700;
            margin-bottom: 1rem;
        }
        
        .cart-empty p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1.5rem;
        }
        
        .cart-items {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr auto auto;
            gap: 1rem;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            align-items: center;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        
        .cart-item-details h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.2rem;
            color: #fff;
        }
        
        .cart-item-details p {
            margin: 0;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .cart-item-price {
            color: #ffd700;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .quantity-btn:hover {
            background: rgba(255, 215, 0, 0.3);
        }
        
        .quantity-input {
            width: 40px;
            height: 30px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.25rem;
            color: #fff;
            text-align: center;
            margin: 0 0.5rem;
        }
        
        .cart-item-remove {
            color: #ff4500;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0.7;
            transition: all 0.3s;
        }
        
        .cart-item-remove:hover {
            opacity: 1;
            transform: scale(1.1);
        }
        
        .cart-summary {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        
        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .cart-summary-row:last-child {
            border-bottom: none;
            padding-top: 1.5rem;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .cart-summary-row:last-child .label {
            color: #ffd700;
        }
        
        .cart-summary-row:last-child .value {
            color: #ffd700;
            font-size: 1.5rem;
        }
        
        .checkout-button {
            display: block;
            width: 100%;
            background: linear-gradient(90deg, #ffd700, #ff7f50);
            border: none;
            border-radius: 0.5rem;
            padding: 1rem;
            color: #000;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        
        .checkout-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .checkout-button:active {
            transform: translateY(-1px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }
        
        .continue-shopping {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .continue-shopping:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="logo-container">
            <img src="../img/logo.png" alt="Elixir du Temps Logo" class="logo">
        </div>
        <nav>
            <ul class="menu-bar">
                <li><a href="../html/Accueil.html">Accueil</a></li>
                <li><a href="../html/Collections.html">Collections</a></li>
                <li><a href="../html/Montres.html">Montres</a></li>
                <li><a href="../html/DescriptionProduits.html">Description produits</a></li>
                <li><a href="../html/APropos.html">À propos</a></li>
                <li><a href="../html/Organigramme.html">Organigramme</a></li>
            </ul>
        </nav>
    </header>

    <!-- Video Background -->
    <div class="video-background">
        <video class="video-bg" autoplay muted loop playsinline>
            <source src="../video/background.mp4" type="video/mp4">
            Votre navigateur ne supporte pas la vidéo.
        </video>
        <div class="video-overlay"></div>
    </div>

    <!-- Cart Section -->
    <div class="cart-container">
        <h1 class="cart-title">Mon Panier</h1>
        
        <?php if (empty($articles)): ?>
            <div class="cart-empty">
                <h3>Votre panier est vide</h3>
                <p>Découvrez nos montres d'exception et ajoutez-les à votre panier.</p>
                <a href="../html/Montres.html" class="continue-shopping">Explorer la collection</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($articles as $article): ?>
                    <div class="cart-item" data-id="<?php echo $article['id']; ?>">
                        <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['nom']); ?>" class="cart-item-image">
                        
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($article['nom']); ?></h3>
                            <p>Réf: ELX-<?php echo $article['id']; ?></p>
                        </div>
                        
                        <div class="cart-item-price"><?php echo number_format($article['prix'], 2, ',', ' '); ?> €</div>
                        
                        <div class="cart-item-quantity">
                            <button class="quantity-btn decrease">-</button>
                            <input type="number" value="<?php echo $article['quantite']; ?>" min="1" max="10" class="quantity-input" readonly>
                            <button class="quantity-btn increase">+</button>
                            <button class="cart-item-remove">×</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span class="label">Sous-total</span>
                    <span class="value"><?php echo number_format($total, 2, ',', ' '); ?> €</span>
                </div>
                <div class="cart-summary-row">
                    <span class="label">Livraison</span>
                    <span class="value">Gratuite</span>
                </div>
                <div class="cart-summary-row">
                    <span class="label">Total</span>
                    <span class="value"><?php echo number_format($total, 2, ',', ' '); ?> €</span>
                </div>
                
                <button class="checkout-button">Passer la commande</button>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="../html/Montres.html" class="continue-shopping">Continuer mes achats</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Elixir du Temps. Tous droits réservés.</p>
            <ul class="footer-links">
                <li><a href="mailto:contact@elixirdutemps.com" class="mon-email">Contact</a></li>
                <li><a href="privacy-policy.php">Politique de confidentialité</a></li>
            </ul>
        </div>
    </footer>
    
    <script src="../js/login.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des quantités
            const decreaseBtns = document.querySelectorAll('.quantity-btn.decrease');
            const increaseBtns = document.querySelectorAll('.quantity-btn.increase');
            const removeBtns = document.querySelectorAll('.cart-item-remove');
            const checkoutBtn = document.querySelector('.checkout-button');
            
            decreaseBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const item = this.closest('.cart-item');
                    const input = item.querySelector('.quantity-input');
                    const productId = item.dataset.id;
                    let value = parseInt(input.value);
                    
                    if (value > 1) {
                        value--;
                        input.value = value;
                        updateCartItem(productId, value);
                    }
                });
            });
            
            increaseBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const item = this.closest('.cart-item');
                    const input = item.querySelector('.quantity-input');
                    const productId = item.dataset.id;
                    let value = parseInt(input.value);
                    
                    if (value < 10) {
                        value++;
                        input.value = value;
                        updateCartItem(productId, value);
                    }
                });
            });
            
            removeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const item = this.closest('.cart-item');
                    const productId = item.dataset.id;
                    
                    if (confirm('Voulez-vous vraiment supprimer cet article du panier?')) {
                        removeCartItem(productId);
                    }
                });
            });
            
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', function() {
                    checkout();
                });
            }
            
            function updateCartItem(productId, quantity) {
                const formData = new FormData();
                formData.append('produit_id', productId);
                formData.append('quantite', quantity);
                formData.append('update_panier', 1);
                
                fetch('panier.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        updateCartSummary();
                    } else {
                        showToast(data.message, true);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Une erreur est survenue', true);
                });
            }
            
            function removeCartItem(productId) {
                const formData = new FormData();
                formData.append('produit_id', productId);
                formData.append('supprimer_article', 1);
                
                fetch('panier.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        // Supprimer l'article du DOM
                        const item = document.querySelector(`.cart-item[data-id="${productId}"]`);
                        if (item) {
                            item.remove();
                        }
                        // Mettre à jour le résumé du panier
                        updateCartSummary();
                        // Vérifier si le panier est vide
                        const cartItems = document.querySelectorAll('.cart-item');
                        if (cartItems.length === 0) {
                            location.reload(); // Recharger la page pour afficher le message "panier vide"
                        }
                    } else {
                        showToast(data.message, true);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Une erreur est survenue', true);
                });
            }
            
            function updateCartSummary() {
                // Recalculer le total
                let total = 0;
                const cartItems = document.querySelectorAll('.cart-item');
                
                cartItems.forEach(item => {
                    const price = parseFloat(item.querySelector('.cart-item-price').textContent.replace('€', '').replace(',', '.').trim());
                    const quantity = parseInt(item.querySelector('.quantity-input').value);
                    total += price * quantity;
                });
                
                // Mettre à jour l'affichage du total
                const totalElement = document.querySelector('.cart-summary-row:last-child .value');
                if (totalElement) {
                    totalElement.textContent = total.toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' €';
                }
                
                // Mettre à jour le sous-total également
                const subtotalElement = document.querySelector('.cart-summary-row:first-child .value');
                if (subtotalElement) {
                    subtotalElement.textContent = total.toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' €';
                }
            }
            
            function checkout() {
                // Création du formulaire pour passer la commande
                const formData = new FormData();
                formData.append('passer_commande', 1);
                
                fetch('panier.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Commande effectuée avec succès!');
                        // Rediriger vers la page de confirmation après 2 secondes
                        setTimeout(() => {
                            window.location.href = 'confirmation-commande.php?id=' + data.data.commande_id;
                        }, 2000);
                    } else {
                        showToast(data.message, true);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Une erreur est survenue lors de la commande', true);
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
        });
    </script>
</body>
</html>

