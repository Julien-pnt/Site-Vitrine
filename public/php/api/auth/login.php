<?php
// Inclure la configuration et les services nécessaires
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/AuthService.php';

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Générer un token CSRF si nécessaire
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialiser les variables d'erreur/succès
$error = null;
$success = null;

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    // Rediriger vers la page d'accueil ou le tableau de bord
    header('Location: /public/pages/Accueil.html');
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Erreur de validation du formulaire";
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validation des données
        if (empty($email) || empty($password)) {
            $error = "Veuillez remplir tous les champs";
        } else {
            try {
                // Initialiser la connexion à la base de données
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASSWORD,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                
                $authService = new AuthService($pdo);
                $userData = $authService->login($email, $password);
                
                // Définir un cookie si "Se souvenir de moi" est coché
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $userId = $_SESSION['user_id'];
                    $expiresAt = time() + 30 * 24 * 60 * 60; // 30 jours
                    
                    // Hasher le token pour stockage sécurisé
                    $tokenHash = password_hash($token, PASSWORD_DEFAULT);
                    
                    // Stocker le token en base de données
                    $stmt = $pdo->prepare("INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))");
                    $stmt->execute([$userId, $tokenHash, $expiresAt]);
                    
                    // Définir le cookie sécurisé
                    setcookie(
                        'remember_token',
                        $userId . ':' . $token,
                        $expiresAt,
                        '/',
                        '',
                        true, // Secure
                        true  // HttpOnly
                    );
                }
                
                // Rediriger vers l'accueil
                header('Location: /public/pages/Accueil.html');
                exit;
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}

// Récupérer les messages flash de session
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
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
    <meta property="og:image" content="/public/assets/img/layout/social-share.jpg">
    
    <!-- Ressources -->
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/auth.css">
    <link rel="shortcut icon" href="/public/assets/img/layout/icon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <title>Connexion - Elixir du Temps</title>
</head>

<body class="auth-page video-loaded">
    <!-- Header section with logo and navigation menu -->
    <header class="header">
        <div class="logo-container">
            <a href="/public/pages/Accueil.html" aria-label="Accueil Elixir du Temps">
                <img src="/public/assets/img/layout/logo.png" alt="Logo Elixir du Temps" class="logo" width="180" height="60">
            </a>
        </div>
        <nav aria-label="Navigation principale">
            <ul class="menu-bar">
                <li><a href="/public/pages/Accueil.html">Accueil</a></li>
                <li><a href="/public/pages/Collections.html">Collections</a></li>
                <li><a href="/public/pages/Montres.html">Montres</a></li>
                <li><a href="/public/pages/APropos.html">À propos</a></li>
                <li><a href="/public/pages/Contact.html">Contact</a></li>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-cart">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span class="cart-badge">0</span>
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
            
            <?php if ($error): ?>
                <div id="alert" class="alert alert-error show">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div id="alert" class="alert alert-success show">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" class="auth-form" method="POST" novalidate>
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <!-- Icône email -->
                    <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <input type="email" id="email" name="email" required>
                    <label for="email">Adresse email</label>
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
                
                <div class="form-options">
                    <label class="remember-label">
                        <input type="checkbox" id="remember" name="remember">
                        <span class="checkbox-custom"></span>
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="password-reset.php" class="forgot-link">Mot de passe oublié ?</a>
                </div>
                
                <button type="submit" id="loginButton" class="btn-primary auth-button">Se connecter</button>
                
                <div class="auth-divider">
                    <span>ou</span>
                </div>
                
                <div class="auth-footer">
                    <p>Vous n'avez pas de compte ?</p>
                    <a href="userCreation.php" class="btn-outline">Créer un compte</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer Section (simplifié) -->
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
                        <li><a href="/public/pages/APropos.html">À propos</a></li>
                        <li><a href="/public/pages/Contact.html">Contact</a></li>
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
                <p>&copy; <?php echo date('Y'); ?> Elixir du Temps. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Script d'animation et de validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fix pour le problème de fondu blanc
            document.body.classList.add('video-loaded');
            
            // Animation des labels de formulaire
            const formInputs = document.querySelectorAll('.form-group input');
            if (formInputs) {
                formInputs.forEach(input => {
                    // Vérifier si l'input a déjà une valeur au chargement
                    if (input.value) {
                        input.parentElement.classList.add('active');
                    }
                    
                    // Ajouter/supprimer la classe active au focus/blur
                    input.addEventListener('focus', () => {
                        input.parentElement.classList.add('active');
                    });
                    
                    input.addEventListener('blur', () => {
                        if (!input.value) {
                            input.parentElement.classList.remove('active');
                        }
                    });
                });
            }
            
            // Animation de checkbox personnalisée
            const checkboxes = document.querySelectorAll('.remember-label input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const customCheckbox = this.nextElementSibling;
                    if (this.checked) {
                        customCheckbox.classList.add('checked');
                    } else {
                        customCheckbox.classList.remove('checked');
                    }
                });
            });
            
            // Auto-masquage des messages d'alerte après 4 secondes
            const alertDiv = document.getElementById('alert');
            if (alertDiv) {
                setTimeout(() => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => {
                        alertDiv.style.display = 'none';
                    }, 300);
                }, 4000);
            }
        });
    </script>
</body>
</html>
