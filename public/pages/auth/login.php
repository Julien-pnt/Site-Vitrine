<?php
// Démarrer la session
session_start();

// Rediriger si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    // Rediriger vers le tableau de bord ou la page d'accueil selon le rôle
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: ../../admin/index.php');
    } else {
        header('Location: ../../user/index.php');
    }
    exit;
}

// Variables pour stocker les messages d'erreur et les valeurs soumises
$error_message = '';
$email = '';
$redirect = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../../php/config/database.php';
    
    // Récupérer et nettoyer les entrées
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    // Validation basique
    if (empty($email) || empty($password)) {
        $error_message = 'Veuillez remplir tous les champs';
    } else {
        try {
            // Connexion à la base de données
            $db = new Database();
            $conn = $db->getConnection();
            
            // Vérifier si l'utilisateur existe
            $stmt = $conn->prepare("SELECT id, email, mot_de_passe, role, nom, prenom, actif FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && $user['actif'] && password_verify($password, $user['mot_de_passe'])) {
                // Connexion réussie, créer la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                $_SESSION['logged_in_time'] = time();
                
                // Rediriger selon le rôle
                if ($user['role'] === 'admin' || $user['role'] === 'manager') {
                    header('Location: ../../admin/index.php');
                } else {
                    // Vérifier s'il y a une redirection demandée
                    if (!empty($redirect)) {
                        header('Location: ' . $redirect);
                    } else {
                        header('Location: ../../user/index.php');
                    }
                }
                exit;
            } else {
                // Échec de la connexion
                if ($user && !$user['actif']) {
                    $error_message = 'Votre compte a été désactivé. Veuillez contacter le support.';
                } else {
                    $error_message = 'Identifiants incorrects. Veuillez réessayer.';
                }
            }
        } catch (PDOException $e) {
            $error_message = 'Une erreur est survenue lors de la connexion. Veuillez réessayer plus tard.';
            // Pour le débogage: error_log('Erreur de connexion: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connectez-vous à votre compte Elixir du Temps pour accéder à vos commandes et préférences.">
    <meta name="keywords" content="connexion, compte, elixir du temps, montres de luxe">
    
    <!-- Script pour corriger le fondu blanc -->
    <script>
        // Force l'affichage immédiat du contenu
        document.documentElement.style.opacity = "1";
        function ensureVisibility() {
            document.body.classList.add('video-loaded');
        }
        // S'exécute dès que possible
        document.addEventListener('DOMContentLoaded', ensureVisibility);
        // Backup au cas où DOMContentLoaded ne se déclencherait pas
        setTimeout(ensureVisibility, 100);
    </script>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Connexion - Elixir du Temps">
    <meta property="og:description" content="Accédez à votre espace client Elixir du Temps.">
    <meta property="og:image" content="../../assets/img/layout/social-share.jpg">
    
    <!-- Ressources -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
    <link rel="shortcut icon" href="../../assets/img/layout/icon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <title>Connexion - Elixir du Temps</title>
</head>

<body class="video-loaded">
    <!-- Header section with logo and navigation menu -->
    <header class="header">
        <div class="logo-container">
            <a href="../Accueil.html" aria-label="Accueil Elixir du Temps">
                <img src="../../assets/img/layout/logo.png" alt="Logo Elixir du Temps" class="logo" width="180" height="60">
            </a>
        </div>
        <nav aria-label="Navigation principale">
            <ul class="menu-bar">
                <li><a href="../Accueil.html">Accueil</a></li>
                <li><a href="../collections/Collections.html">Collections</a></li>
                <li><a href="../products/Montres.html">Montres</a></li>
                <li><a href="../about/APropos.html">À propos</a></li>
                <li><a href="../contact/Contact.html">Contact</a></li>
            </ul>
        </nav>
        
        <!-- User and Cart Icons -->
        <div class="user-cart-container">
            <a href="login.php" class="user-icon active" aria-current="page">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-user">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
            
            <div class="cart-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span class="cart-badge">0</span>
                
                <div class="cart-dropdown">
                    <div class="cart-dropdown-header">
                        <h3>Mon Panier</h3>
                    </div>
                    <div class="cart-dropdown-items">
                        <!-- Le panier sera rempli dynamiquement via JavaScript -->
                    </div>
                    <div class="cart-dropdown-empty">Votre panier est vide</div>
                    <div class="cart-dropdown-total">
                        <span>Total:</span>
                        <span class="cart-dropdown-total-value">0,00 €</span>
                    </div>
                    <div class="cart-dropdown-buttons">
                        <a href="../products/panier.php" class="cart-dropdown-button secondary">Voir le panier</a>
                        <a href="../products/Montres.html" class="cart-dropdown-button primary">Découvrir nos montres</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Background avec image de secours -->
    <div class="auth-background">
        <div class="auth-overlay"></div>
    </div>
    
    <!-- Authentication Section -->
    <section class="auth-section">
        <div class="auth-container">
            <h1 class="auth-title">Connexion</h1>
            
            <?php if (!empty($error_message)): ?>
            <div id="alert" class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form id="loginForm" class="auth-form" method="post" action="login.php<?php echo !empty($redirect) ? '?redirect='.urlencode($redirect) : ''; ?>" novalidate>
                <div class="form-group">
                    <!-- Icône email -->
                    <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <label for="email" class="<?php echo !empty($email) ? 'active' : ''; ?>">Adresse email</label>
                    <div class="form-border"></div>
                </div>
                
                <div class="form-group">
                    <!-- Icône mot de passe -->
                    <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <input type="password" id="password" name="password" required>
                    <label for="password">Mot de passe</label>
                    <div class="form-border"></div>
                </div>
                
                <div class="forgot-password">
                    <a href="reset-password.php">Mot de passe oublié ?</a>
                </div>
                
                <button type="submit" id="loginButton" class="btn-primary auth-button">Se connecter</button>
                
                <div class="auth-divider">
                    <span>ou</span>
                </div>
                
                <div class="auth-footer">
                    <p>Vous n'avez pas de compte ?</p>
                    <a href="register.php" class="btn-outline">Créer un compte</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>Elixir du Temps</h3>
                    <p>L'excellence horlogère depuis 1985</p>
                </div>
                
                <div class="footer-column">
                    <h3>Informations</h3>
                    <ul>
                        <li><a href="../about/APropos.html">À propos</a></li>
                        <li><a href="../contact/Contact.html">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact</h3>
                    <address>
                        <p>15 rue de la Paix<br>75002 Paris, France</p>
                        <p>Tél: <a href="tel:+33145887766">+33 (0)1 45 88 77 66</a></p>
                    </address>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Elixir du Temps. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Script d'animation et de validation -->
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/gestion-cart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation des champs de formulaire
        const inputs = document.querySelectorAll('.form-group input');
        
        inputs.forEach(input => {
            // Ajouter la classe active si le champ a une valeur (utile pour les erreurs)
            if (input.value) {
                input.parentNode.querySelector('label').classList.add('active');
            }
            
            input.addEventListener('focus', function() {
                this.parentNode.querySelector('label').classList.add('active');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentNode.querySelector('label').classList.remove('active');
                }
            });
        });

        // Mise à jour de l'affichage du panier
        updateCartDisplay();
    });
    </script>
</body>
</html>