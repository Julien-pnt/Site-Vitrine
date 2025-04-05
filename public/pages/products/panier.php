<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\public\pages\products\panier.php
// Connexion à la base de données
require_once '../../../php/config/database.php';

// Démarrage de la session
session_start();

// Vérification de l'état de connexion de l'utilisateur
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$accountLink = $isLoggedIn ? '../account/dashboard.php' : '../account/login.php';
$accountText = $isLoggedIn ? 'Mon espace' : 'Connexion';

// Si l'utilisateur est connecté, charger ses informations
$userInfo = null;
if ($isLoggedIn) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Gérer l'erreur silencieusement
    }
}

// Pour les notifications côté serveur
$notification = [
    'show' => false,
    'message' => '',
    'type' => 'info' // info, success, error, warning
];

// Traitement des actions du panier (si un utilisateur est connecté)
if ($isLoggedIn && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // Actions possibles : add, update, remove, clear
    switch ($action) {
        case 'sync':
            // Synchroniser le panier du localStorage avec la session PHP
            if (isset($_POST['cart']) && is_array($_POST['cart'])) {
                $_SESSION['cart'] = $_POST['cart'];
                echo json_encode(['success' => true]);
                exit;
            }
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier | Elixir du Temps</title>
    <meta name="description" content="Consultez votre panier et procédez au paiement de vos montres de luxe Elixir du Temps.">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Styles spécifiques à la page panier */
        .cart-page {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .cart-page-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .cart-page-header h1 {
            font-size: 2.5rem;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .cart-empty {
            text-align: center;
            padding: 50px 0;
        }

        .cart-empty p {
            margin-bottom: 20px;
            font-size: 1.2rem;
            color: #666;
        }

        .cart-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        .cart-items {
            flex: 2;
            min-width: 300px;
        }

        .cart-summary {
            flex: 1;
            min-width: 300px;
            background: #f9f9f9;
            border-radius: 8px;
            padding: 25px;
            align-self: flex-start;
            position: sticky;
            top: 100px;
        }

        .cart-summary h2 {
            margin-top: 0;
            font-size: 1.5rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .cart-total {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .cart-actions {
            margin-top: 25px;
        }

        .cart-actions button {
            width: 100%;
            padding: 15px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            margin-bottom: 10px;
            border: none;
            transition: all 0.3s ease;
        }

        .checkout-btn {
            background-color: #d4af37;
            color: white;
            font-size: 1.1rem;
        }

        .checkout-btn:hover {
            background-color: #c19b26;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .continue-shopping {
            background-color: #f0f0f0;
            color: #333;
            font-size: 1rem;
        }

        .continue-shopping:hover {
            background-color: #e0e0e0;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table th {
            text-align: left;
            padding: 15px 10px;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 500;
            color: #555;
        }

        .cart-item-row {
            border-bottom: 1px solid #f0f0f0;
        }

        .cart-item-cell {
            padding: 20px 10px;
            vertical-align: middle;
        }

        .cart-product {
            display: flex;
            align-items: center;
        }

        .cart-product-image {
            width: 80px;
            height: 80px;
            border-radius: 6px;
            overflow: hidden;
            margin-right: 15px;
        }

        .cart-product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-product-name {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .cart-quantity {
            display: flex;
            align-items: center;
            max-width: 100px;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1px solid #ddd;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: #f0f0f0;
        }

        .qty-input {
            width: 40px;
            height: 28px;
            text-align: center;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 8px;
        }

        .cart-remove {
            color: #999;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.2s;
        }

        .cart-remove:hover {
            color: #e74c3c;
        }

        @media (max-width: 768px) {
            .cart-container {
                flex-direction: column;
            }

            .cart-summary {
                position: relative;
                top: 0;
            }

            .cart-table, .cart-table tbody, .cart-item-row {
                display: block;
            }

            .cart-table thead {
                display: none;
            }

            .cart-item-row {
                position: relative;
                padding: 15px 0;
            }

            .cart-item-cell {
                display: block;
                padding: 5px 0;
                text-align: right;
            }

            .cart-item-cell:before {
                content: attr(data-label);
                float: left;
                font-weight: 500;
            }

            .cart-product {
                text-align: left;
            }

            .cart-remove {
                position: absolute;
                top: 15px;
                right: 0;
            }
        }

        /* Fond noir pour le menu de navigation */
        .header {
            background-color: #000000; /* Noir pur */
        }

        /* Ajustement des couleurs de texte pour une meilleure lisibilité */
        .menu-bar a {
            color: #ffffff; /* Texte blanc pour les liens du menu */
        }

        .menu-bar a:hover {
            color: #d4af37; /* Doré au survol pour garder le thème luxe */
        }

        /* Couleur des icônes dans l'en-tête */
        .user-icon svg, .cart-icon svg {
            stroke: #ffffff; /* Icônes en blanc */
        }

        /* Badge du panier plus visible sur fond noir */
        .cart-badge {
            background: #d4af37; /* Badge doré */
            color: #000000; /* Texte noir sur le badge */
            border: 1px solid rgba(255,255,255,0.3); /* Bordure subtile */
        }

        /* Style actif pour l'élément de menu actuel */
        .menu-bar a.active {
            color: #d4af37; /* Élément actif en doré */
            border-bottom: 2px solid #d4af37; /* Soulignement doré */
        }

        /* Styles pour les indicateurs de stock */
        .stock-indicator {
            display: inline-block;
            margin-top: 5px;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8rem;
        }

        .in-stock {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .stock-low {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ff9800;
        }

        .stock-warning {
            background-color: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }

        .out-of-stock {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        /* Style pour les boutons désactivés */
        .qty-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #e0e0e0;
        }

        /* Style pour les notifications */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 15px;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            opacity: 1;
            transition: opacity 0.5s;
        }

        .notification.warning {
            background-color: #ff9800;
        }

        .notification.error {
            background-color: #f44336;
        }

        /* Style pour les prix barrés (anciens prix) */
        .price-old {
            text-decoration: line-through;
            color: #888;
            font-size: 0.85em;
            margin-right: 10px;
        }

        /* Style pour les prix en promotion */
        .price-promo {
            color: #e74c3c;
            font-weight: 600;
        }

        /* Affichage de la référence produit */
        .cart-product-ref {
            font-size: 0.8em;
            color: #666;
            margin: 3px 0;
        }
        
        /* Navigation controls - pour accéder au compte client */
        .navigation-controls {
            max-width: 1400px;
            margin: 1rem auto 0;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .back-button, .account-access {
            display: flex;
            align-items: center;
            padding: 0.65rem 1.2rem;
            text-decoration: none;
            color: #333;
            font-family: 'Raleway', sans-serif;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background-color: white;
            border: 1px solid #eaeaea;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .back-button:hover, .account-access:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .back-button i, .account-access i {
            margin-right: 0.6rem;
            font-size: 16px;
            transition: transform 0.2s ease;
        }

        /* Préchargeur pour éviter le fondu blanc */
        body {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        body.loaded {
            visibility: visible;
            opacity: 1;
        }

        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000000;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader {
            position: relative;
            width: 80px;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader-logo {
            position: absolute;
            z-index: 2;
        }

        .circular {
            animation: rotate 2s linear infinite;
            height: 80px;
            width: 80px;
            position: absolute;
        }

        .path {
            stroke: #d4af37;
            stroke-dasharray: 90, 150;
            stroke-dashoffset: 0;
            stroke-linecap: round;
            animation: dash 1.5s ease-in-out infinite;
        }

        @keyframes rotate {
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes dash {
            0% {
                stroke-dasharray: 1, 150;
                stroke-dashoffset: 0;
            }
            50% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -35;
            }
            100% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -124;
            }
        }
    </style>
</head>
<body>
    <!-- Préchargeur pour éviter le flash blanc -->
    <div class="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10"/>
            </svg>
            <div class="loader-logo">
                <img src="../../assets/img/layout/logo-small.png" alt="Elixir du Temps" width="50">
            </div>
        </div>
    </div>

    <!-- Header Section -->
    <header class="header">
        <div class="logo-container">
            <a href="../Accueil.html" aria-label="Accueil Elixir du Temps">
                <img src="../../assets/img/layout/logo.png" alt="Elixir du Temps Logo" class="logo" width="180" height="60">
            </a>
        </div>
        <nav>
            <ul class="menu-bar">
                <li><a href="../Accueil.html">Accueil</a></li>
                <li><a href="../collections/Collections.html">Collections</a></li>
                <li><a href="Montres.html">Montres</a></li>
                <li><a href="DescriptionProduits.html">Description produits</a></li>
                <li><a href="../APropos.html">À propos</a></li>
            </ul>
        </nav>
        
        <!-- User and Cart Icons -->
        <div class="user-cart-container">
            <?php if ($isLoggedIn): ?>
            <a href="../account/dashboard.php" class="user-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-user">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <div class="dropdown-menu">
                    <a href="../account/dashboard.php" id="account-link">Mon compte</a>
                    <a href="../account/orders.php" id="orders-link">Mes commandes</a>
                    <a href="../../php/api/auth/logout.php" id="logout-link" class="logout">Déconnexion</a>
                </div>
            </a>
            <?php else: ?>
            <a href="../auth/login.php" class="user-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-user">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <div class="dropdown-menu">
                    <a href="../auth/login.php" id="login-link">Se connecter</a>
                    <a href="../auth/register.php" id="register-link">S'inscrire</a>
                </div>
            </a>
            <?php endif; ?>
            
            <div class="cart-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span class="cart-badge">0</span>
            </div>
        </div>
    </header>
    
    <!-- Main Cart Content -->
    <main class="cart-page">
        <div class="cart-page-header">
            <h1>Mon Panier</h1>
        </div>

        <!-- Empty Cart Message (initially hidden) -->
        <div class="cart-empty" style="display: none;">
            <p>Votre panier est vide</p>
            <a href="Montres.html" class="btn-primary">Découvrir nos montres</a>
        </div>

        <!-- Cart Content -->
        <div class="cart-container">
            <div class="cart-items">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cart-items-container">
                        <!-- Les articles du panier seront ajoutés ici dynamiquement -->
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <h2>Résumé de la commande</h2>
                <div class="cart-summary-row">
                    <span>Sous-total</span>
                    <span id="cart-subtotal">0,00 €</span>
                </div>
                <div class="cart-summary-row">
                    <span>Livraison</span>
                    <span id="cart-shipping">Gratuite</span>
                </div>
                <div class="cart-summary-row cart-total">
                    <span>Total</span>
                    <span id="cart-total">0,00 €</span>
                </div>
                <div class="cart-actions">
                    <button class="checkout-btn" id="checkout-btn">Procéder au paiement</button>
                    <button class="continue-shopping" id="continue-shopping">Continuer mes achats</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>Navigation</h3>
                    <ul>
                        <li><a href="../Accueil.html">Accueil</a></li>
                        <li><a href="../collections/Collections.html">Collections</a></li>
                        <li><a href="Montres.html">Montres</a></li>
                        <li><a href="../APropos.html">À propos</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Informations</h3>
                    <ul>
                        <li><a href="../Contact.html">Contact</a></li>
                        <li><a href="../PrivacyPolicy.html">Politique de confidentialité</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact</h3>
                    <address>
                        <p>15 rue de la Paix<br>75002 Paris, France</p>
                        <p>Tél: <a href="tel:+33145887766">+33 (0)1 45 88 77 66</a></p>
                        <p>Email: <a href="mailto:contact@elixirdutemps.com">contact@elixirdutemps.com</a></p>
                    </address>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Elixir du Temps. Tous droits réservés.</p>
                <div class="social-icons">
                    <a href="https://instagram.com" aria-label="Instagram" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    </a>
                    <a href="https://facebook.com" aria-label="Facebook" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Affiche la notification si nécessaire -->
    <?php if ($notification['show']): ?>
    <div id="server-notification" class="notification <?php echo $notification['type']; ?>">
        <?php echo $notification['message']; ?>
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('server-notification').style.opacity = '0';
            setTimeout(() => {
                document.getElementById('server-notification').style.display = 'none';
            }, 500);
        }, 5000);
    </script>
    <?php endif; ?>

    <!-- Script de gestion du panier -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Récupérer les éléments du DOM
            const cartItemsContainer = document.getElementById('cart-items-container');
            const cartEmptyMessage = document.querySelector('.cart-empty');
            const cartContainer = document.querySelector('.cart-container');
            const subtotalElement = document.getElementById('cart-subtotal');
            const totalElement = document.getElementById('cart-total');
            const checkoutBtn = document.getElementById('checkout-btn');
            const continueShoppingBtn = document.getElementById('continue-shopping');
            const cartBadge = document.querySelector('.cart-badge');

            /**
             * Fonction pour récupérer le panier depuis localStorage
             */
            function getCart() {
                return JSON.parse(localStorage.getItem('cart')) || [];
            }

            /**
             * Fonction pour sauvegarder le panier dans localStorage
             */
            function saveCart(cart) {
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartUI(cart);

                // Synchroniser avec le serveur si l'utilisateur est connecté
                <?php if ($isLoggedIn): ?>
                syncCartWithServer(cart);
                <?php endif; ?>
            }

            /**
             * Fonction pour synchroniser le panier avec le serveur
             */
            function syncCartWithServer(cart) {
                fetch('panier.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=sync&cart=${encodeURIComponent(JSON.stringify(cart))}`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Erreur de synchronisation du panier');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
            }

            /**
             * Mettre à jour l'interface utilisateur du panier (badge, etc.)
             */
            function updateCartUI(cart) {
                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                if (cartBadge) {
                    cartBadge.textContent = totalItems;
                    cartBadge.style.display = totalItems > 0 ? 'flex' : 'none';
                }
            }

            /**
             * Vérifie le stock disponible d'un produit via l'API
             */
            function checkProductStock(productId) {
                return fetch(`../../php/api/products/check-stock.php?id=${productId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau lors de la vérification du stock');
                        }
                        return response.json();
                    });
            }

            /**
             * Formate un prix
             */
            function formatPrice(price) {
                return Number(price).toFixed(2).replace('.', ',');
            }

            /**
             * Affiche une notification à l'utilisateur
             */
            function showNotification(message, type = 'success') {
                let notification = document.getElementById('notification');
                
                if (!notification) {
                    notification = document.createElement('div');
                    notification.id = 'notification';
                    document.body.appendChild(notification);
                }
                
                // Définir la classe en fonction du type
                notification.className = 'notification ' + type;
                notification.innerHTML = message;
                notification.style.display = 'block';
                notification.style.opacity = '1';
                
                // Faire disparaître après 3 secondes
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 500);
                }, 3000);
            }

            /**
             * Supprime un article du panier
             */
            function removeFromCart(productId) {
                let cart = getCart();
                cart = cart.filter(item => item.id !== productId);
                
                saveCart(cart);
                loadCart(); // Recharger l'affichage complet
            }

            /**
             * Diminue la quantité d'un article dans le panier
             */
            function decreaseCartQuantity(productId) {
                const cart = getCart();
                const productIndex = cart.findIndex(item => item.id === productId);
                
                if (productIndex > -1) {
                    if (cart[productIndex].quantity > 1) {
                        cart[productIndex].quantity--;
                        saveCart(cart);
                    } else {
                        removeFromCart(productId);
                    }
                }
            }

            /**
             * Augmente la quantité d'un article dans le panier
             */
            function increaseCartQuantity(productId) {
                const cart = getCart();
                const productIndex = cart.findIndex(item => item.id === productId);
                
                if (productIndex > -1) {
                    // Vérifier le stock avant d'augmenter
                    checkProductStock(productId)
                        .then(stockInfo => {
                            if (stockInfo.visible === false) {
                                showNotification(`Ce produit n'est plus disponible`, 'error');
                                removeFromCart(productId);
                                loadCart();
                                return;
                            }
                            
                            if (stockInfo.stock > cart[productIndex].quantity) {
                                cart[productIndex].quantity++;
                                
                                // Mettre à jour les informations du produit
                                cart[productIndex].availableStock = stockInfo.stock;
                                cart[productIndex].stockAlerte = stockInfo.stock_alerte || 5;
                                
                                // Mettre à jour le prix si nécessaire
                                if (stockInfo.prix_promo && parseFloat(stockInfo.prix_promo) < parseFloat(stockInfo.prix)) {
                                    cart[productIndex].price = parseFloat(stockInfo.prix_promo);
                                    cart[productIndex].regularPrice = parseFloat(stockInfo.prix);
                                    cart[productIndex].hasPromo = true;
                                } else {
                                    cart[productIndex].price = parseFloat(stockInfo.prix);
                                    cart[productIndex].hasPromo = false;
                                }
                                
                                saveCart(cart);
                                loadCart(); // Recharger l'affichage
                            } else {
                                showNotification(`Stock maximum atteint pour ce produit (${stockInfo.stock})`, 'warning');
                            }
                        })
                        .catch(error => {
                            console.error("Erreur lors de la vérification du stock:", error);
                            showNotification("Impossible de vérifier le stock disponible", 'error');
                        });
                }
            }

            /**
             * Vérifie le stock de tous les produits du panier avant le paiement
             */
            async function verifyAllStockBeforeCheckout() {
                const cart = getCart();
                let allValid = true;
                let message = "";
                let updatedCart = [...cart];

                for (let i = 0; i < cart.length; i++) {
                    try {
                        const stockInfo = await checkProductStock(cart[i].id);
                        
                        // Vérifier si le produit est visible/disponible
                        if (stockInfo.error || stockInfo.visible === false) {
                            allValid = false;
                            message += `"${cart[i].name}" n'est plus disponible et a été retiré de votre panier. `;
                            updatedCart = updatedCart.filter(item => item.id !== cart[i].id);
                            continue;
                        }
                        
                        // Si le stock est insuffisant
                        if (stockInfo.stock < cart[i].quantity) {
                            allValid = false;
                            
                            if (stockInfo.stock <= 0) {
                                // Produit supprimé du panier (rupture)
                                message += `"${cart[i].name}" a été supprimé (rupture de stock). `;
                                updatedCart = updatedCart.filter(item => item.id !== cart[i].id);
                            } else {
                                // Quantité ajustée
                                message += `La quantité de "${cart[i].name}" a été ajustée à ${stockInfo.stock}. `;
                                const idx = updatedCart.findIndex(item => item.id === cart[i].id);
                                if (idx !== -1) {
                                    updatedCart[idx].quantity = stockInfo.stock;
                                }
                            }
                        }
                        
                        // Mettre à jour les informations du produit dans le panier
                        const idx = updatedCart.findIndex(item => item.id === cart[i].id);
                        if (idx !== -1) {
                            updatedCart[idx].availableStock = stockInfo.stock;
                            updatedCart[idx].stockAlerte = stockInfo.stock_alerte || 5;
                            
                            // Mettre à jour le prix si un prix promo est disponible
                            if (stockInfo.prix_promo && parseFloat(stockInfo.prix_promo) < parseFloat(stockInfo.prix)) {
                                updatedCart[idx].regularPrice = parseFloat(stockInfo.prix);
                                updatedCart[idx].price = parseFloat(stockInfo.prix_promo);
                                updatedCart[idx].hasPromo = true;
                            } else {
                                updatedCart[idx].price = parseFloat(stockInfo.prix);
                                updatedCart[idx].hasPromo = false;
                            }
                        }
                    } catch (error) {
                        console.error(`Erreur pour le produit ${cart[i].id}:`, error);
                    }
                }

                // Si des modifications ont été faites, mettre à jour le panier
                if (!allValid) {
                    saveCart(updatedCart);
                }

                return { allValid, message };
            }

            /**
             * Ajoute les écouteurs d'événements aux boutons de quantité
             */
            function attachQuantityEvents() {
                // Boutons de diminution
                document.querySelectorAll('.qty-btn.decrease').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.getAttribute('data-id');
                        decreaseCartQuantity(productId);
                    });
                });
                
                // Boutons d'augmentation
                document.querySelectorAll('.qty-btn.increase').forEach(button => {
                    if (button.classList.contains('disabled')) {
                        return; // Ne pas attacher d'événement aux boutons désactivés
                    }
                    
                    button.addEventListener('click', function() {
                        const productId = this.getAttribute('data-id');
                        const input = document.querySelector(`.qty-input[data-id="${productId}"]`);
                        const currentStock = parseInt(input.getAttribute('data-stock'), 10);
                        const currentQuantity = parseInt(input.value, 10);
                        
                        if (currentQuantity < currentStock) {
                            increaseCartQuantity(productId);
                        } else {
                            showNotification('Quantité maximum atteinte pour ce produit', 'warning');
                        }
                    });
                });
                
                // Boutons de suppression
                document.querySelectorAll('.cart-remove').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.getAttribute('data-id');
                        removeFromCart(productId);
                    });
                });
            }

            /**
             * Charge et affiche le contenu du panier
             */
            function loadCart() {
                // Récupérer le panier depuis le localStorage
                const cart = getCart();
                
                if (cart.length === 0) {
                    // Afficher le message "panier vide"
                    cartEmptyMessage.style.display = 'block';
                    cartContainer.style.display = 'none';
                    return;
                }
                
                // Afficher le contenu du panier
                cartEmptyMessage.style.display = 'none';
                cartContainer.style.display = 'flex';
                
                // Vider le conteneur existant
                cartItemsContainer.innerHTML = '';
                
                // Vérifier le stock pour chaque produit
                Promise.all(cart.map(item => 
                    checkProductStock(item.id)
                        .then(stockInfo => {
                            return {
                                ...item,
                                currentStock: stockInfo.stock,
                                stockAlerte: stockInfo.stock_alerte || 5,
                                visible: stockInfo.visible !== false,
                                regularPrice: parseFloat(stockInfo.prix),
                                promoPrice: stockInfo.prix_promo ? parseFloat(stockInfo.prix_promo) : null
                            };
                        })
                        .catch(error => {
                            console.error(`Erreur pour le produit ${item.id}:`, error);
                            return {
                                ...item,
                                currentStock: 0,
                                visible: false
                            };
                        })
                ))
                .then(productsWithStock => {
                    // Filtrer les produits qui ne sont plus disponibles
                    const availableProducts = productsWithStock.filter(item => item.visible);
                    
                    if (availableProducts.length === 0) {
                        cartEmptyMessage.style.display = 'block';
                        cartContainer.style.display = 'none';
                        saveCart([]);
                        return;
                    }
                    
                    let subtotal = 0;
                    
                    availableProducts.forEach(item => {
                        // Utiliser le prix promotionnel si disponible
                        const displayPrice = item.promoPrice !== null && item.promoPrice < item.regularPrice ? 
                            item.promoPrice : item.regularPrice;
                        
                        const itemTotal = displayPrice * item.quantity;
                        subtotal += itemTotal;
                        
                        // Déterminer les classes et messages pour l'indicateur de stock
                        let stockClass = 'in-stock';
                        let stockMessage = '<i class="fas fa-check-circle"></i> En stock';
                        let disableIncrease = false;
                        
                        if (item.currentStock <= 0) {
                            stockClass = 'out-of-stock';
                            stockMessage = '<i class="fas fa-times-circle"></i> Rupture de stock';
                            disableIncrease = true;
                        } else if (item.quantity >= item.currentStock) {
                            stockClass = 'stock-warning';
                            stockMessage = `<i class="fas fa-exclamation-circle"></i> Maximum disponible`;
                            disableIncrease = true;
                        } else if (item.currentStock <= item.stockAlerte) {
                            stockClass = 'stock-low';
                            stockMessage = `<i class="fas fa-exclamation-circle"></i> Plus que ${item.currentStock} en stock`;
                        }
                        
                        const tr = document.createElement('tr');
                        tr.className = 'cart-item-row';
                        
                        // Préparer l'affichage du prix (normal ou promotion)
                        let priceDisplay = `${formatPrice(displayPrice)} €`;
                        if (item.promoPrice !== null && item.promoPrice < item.regularPrice) {
                            priceDisplay = `
                                <span class="price-old">${formatPrice(item.regularPrice)} €</span>
                                <span class="price-promo">${formatPrice(item.promoPrice)} €</span>
                            `;
                        }
                        
                        tr.innerHTML = `
                            <td class="cart-item-cell" data-label="Produit">
                                <div class="cart-product">
                                    <div class="cart-product-image">
                                        <img src="${item.image}" alt="${item.name}">
                                    </div>
                                    <div class="cart-product-info">
                                        <h3 class="cart-product-name">${item.name}</h3>
                                        <p class="cart-product-ref">Réf: ${item.reference || `ELX-${item.id}`}</p>
                                        <div class="stock-indicator ${stockClass}">${stockMessage}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="cart-item-cell" data-label="Prix">${priceDisplay}</td>
                            <td class="cart-item-cell" data-label="Quantité">
                                <div class="cart-quantity">
                                    <button type="button" class="qty-btn decrease" data-id="${item.id}">-</button>
                                    <input type="text" value="${item.quantity}" class="qty-input" readonly 
                                           data-stock="${item.currentStock}" data-id="${item.id}">
                                    <button type="button" class="qty-btn increase ${disableIncrease ? 'disabled' : ''}" 
                                            data-id="${item.id}" ${disableIncrease ? 'disabled' : ''}>+</button>
                                </div>
                            </td>
                            <td class="cart-item-cell" data-label="Total">${formatPrice(itemTotal)} €</td>
                            <td class="cart-item-cell">
                                <button type="button" class="cart-remove" data-id="${item.id}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </td>
                        `;
                        
                        cartItemsContainer.appendChild(tr);
                    });
                    
                    // Mettre à jour les totaux
                    subtotalElement.textContent = `${formatPrice(subtotal)} €`;
                    totalElement.textContent = `${formatPrice(subtotal)} €`; // Pas de frais de livraison
                    
                    // Ajouter les écouteurs d'événements aux boutons
                    attachQuantityEvents();
                    
                    // Si le panier a été modifié (produits non visibles supprimés)
                    if (availableProducts.length < cart.length) {
                        saveCart(availableProducts);
                        showNotification("Certains produits ne sont plus disponibles et ont été retirés du panier.", 'warning');
                    }
                });
            }

            // Charger le panier au chargement de la page
            loadCart();

            // Ajouter les écouteurs d'événements aux boutons
            continueShoppingBtn.addEventListener('click', function() {
                window.location.href = 'Montres.html';
            });

            checkoutBtn.addEventListener('click', function() {
                <?php if ($isLoggedIn): ?>
                // Si l'utilisateur est connecté, vérifier les stocks avant de procéder au paiement
                verifyAllStockBeforeCheckout()
                    .then(result => {
                        if (result.allValid) {
                            // Rediriger vers la page de paiement
                            window.location.href = 'checkout.php';
                        } else {
                            showNotification(`Attention: ${result.message}`, 'warning');
                            loadCart(); // Recharger pour refléter les ajustements
                        }
                    })
                    .catch(error => {
                        showNotification("Erreur lors de la vérification du stock. Veuillez réessayer.", 'error');
                        console.error("Erreur:", error);
                    });
                <?php else: ?>
                // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
                window.location.href = '../auth/login.php?redirect=products/panier.php';
                <?php endif; ?>
            });
        });

        // Gestion du préchargeur pour éviter le flash blanc
        window.addEventListener('load', function() {
            // Masquer le préchargeur après le chargement complet de la page
            setTimeout(function() {
                const preloader = document.querySelector('.preloader');
                document.body.classList.add('loaded');
                
                if (preloader) {
                    preloader.style.opacity = '0';
                    setTimeout(function() {
                        preloader.style.display = 'none';
                    }, 300);
                }
            }, 500); // Délai pour assurer l'affichage correct
        });
    </script>
</body>
</html>