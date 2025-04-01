<?php
// Inclure les fichiers de configuration et services
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/AuthService.php';

// Initialiser la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Générer un token CSRF si nécessaire
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    // Redirection vers l'accueil si l'utilisateur est déjà connecté
    header('Location: /public/pages/Accueil.html');
    exit;
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Erreur de validation du formulaire";
    } else {
        // Récupérer et nettoyer les données du formulaire
        $nom = trim($_POST['nom'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm-password'] ?? '';
        
        // Validation des données
        if (empty($nom) || empty($email) || empty($password) || empty($confirmPassword)) {
            $_SESSION['error'] = "Tous les champs sont obligatoires";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Format d'email invalide";
        } elseif (strlen($password) < 8) {
            $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères";
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $_SESSION['error'] = "Le mot de passe doit contenir au moins une majuscule";
        } elseif (!preg_match('/[0-9]/', $password)) {
            $_SESSION['error'] = "Le mot de passe doit contenir au moins un chiffre";
        } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $_SESSION['error'] = "Le mot de passe doit contenir au moins un caractère spécial";
        } elseif ($password !== $confirmPassword) {
            $_SESSION['error'] = "Les mots de passe ne correspondent pas";
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
                $userId = $authService->register($nom, $email, $password);
                
                $_SESSION['success'] = "Compte créé avec succès! Vous pouvez maintenant vous connecter.";
                header('Location: /public/php/api/auth/login.php');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        // Redirection en cas d'erreur
        if (isset($_SESSION['error'])) {
            header('Location: /public/php/api/auth/userCreation.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Elixir du Temps</title>
    
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
    
    <meta name="description" content="Créez votre compte Elixir du Temps et accédez à des fonctionnalités exclusives.">
    <meta name="keywords" content="inscription, compte, elixir du temps, montres de luxe">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Inscription - Elixir du Temps">
    <meta property="og:description" content="Créez votre compte Elixir du Temps">
    <meta property="og:image" content="/public/assets/img/layout/social-share.jpg">
    
    <!-- Ressources -->
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/auth.css">
    <link rel="shortcut icon" href="/public/assets/img/layout/icon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="auth-page video-loaded">
    <!-- Header Section -->
    <header class="header">
        <div class="logo-container">
            <a href="/public/pages/Accueil.html">
                <img src="/public/assets/img/layout/logo.png" alt="Elixir du Temps Logo" class="logo" width="180" height="60">
            </a>
        </div>
        <nav aria-label="Navigation principale">
            <ul class="menu-bar">
                <li><a href="/public/pages/Accueil.html">Accueil</a></li>
                <li><a href="/public/pages/collections/Collections.html">Collections</a></li>
                <li><a href="/public/pages/products/Montres.html">Montres</a></li>
                <li><a href="/public/pages/APropos.html">À propos</a></li>
                <li><a href="/public/pages/Contact.html">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Background pour authentification -->
    <div class="auth-background">
        <div class="auth-overlay"></div>
    </div>

    <!-- Formulaire d'inscription -->
    <div class="auth-wrapper">
        <div class="auth-form-container">
            <h1 class="auth-title">Créer un compte</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                        echo htmlspecialchars($_SESSION['error']); 
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo htmlspecialchars($_SESSION['success']); 
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form" novalidate>
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="input-field">
                    <input type="text" id="nom" name="nom" required minlength="2">
                    <label for="nom">Nom</label>
                </div>
                <div class="input-field">
                    <input type="email" id="email" name="email" required 
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    <label for="email">Email</label>
                </div>
                <div class="input-field">
                    <input type="password" id="password" name="password" required minlength="8">
                    <label for="password">Mot de passe</label>
                    <small>Minimum 8 caractères, incluant majuscule, chiffre et caractère spécial</small>
                </div>
                <div class="input-field">
                    <input type="password" id="confirm-password" name="confirm-password" required>
                    <label for="confirm-password">Confirmer le mot de passe</label>
                </div>
                
                <div class="password-strength-meter">
                    <div class="meter-label">Force du mot de passe:</div>
                    <div class="meter">
                        <div class="meter-progress"></div>
                    </div>
                </div>
                
                <div class="policy-agreement">
                    <label>
                        <input type="checkbox" name="agree_policy" required>
                        <span>J'accepte les <a href="/public/pages/legal/PrivacyPolicy.html" target="_blank">conditions d'utilisation</a> et la <a href="/public/pages/legal/PrivacyPolicy.html" target="_blank">politique de confidentialité</a>.</span>
                    </label>
                </div>
                
                <button type="submit" id="submit-btn" disabled>Créer mon compte</button>
                
                <div class="auth-alt">
                    <p>Déjà un compte ? <a href="/public/php/api/auth/login.php">Se connecter</a></p>
                </div>
            </form>
        </div>
    </div>

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
                        <li><a href="/public/pages/legal/PrivacyPolicy.html">Politique de confidentialité</a></li>
                        <li><a href="/public/pages/legal/CGV.html">Conditions de vente</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact</h3>
                    <address>
                        <p>15 rue de la Paix<br>75002 Paris, France</p>
                        <p>Email: <a href="mailto:contact@elixirdutemps.com">contact@elixirdutemps.com</a></p>
                        <p>Tél: <a href="tel:+33145887766">+33 (0)1 45 88 77 66</a></p>
                    </address>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Elixir du Temps. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fix pour le problème de fondu blanc
            document.body.classList.add('video-loaded');
            
            // Gestion de l'animation des labels
            const inputs = document.querySelectorAll('.input-field input');
            inputs.forEach(input => {
                // Si l'input a déjà une valeur (par exemple après une erreur de soumission)
                if (input.value) {
                    input.classList.add('has-content');
                }
                
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.classList.add('has-content');
                    } else {
                        this.classList.remove('has-content');
                    }
                });
            });
            
            const passwordField = document.getElementById('password');
            const confirmField = document.getElementById('confirm-password');
            const meterProgress = document.querySelector('.meter-progress');
            const submitBtn = document.getElementById('submit-btn');
            const policyCheckbox = document.querySelector('input[name="agree_policy"]');
            
            // Évaluer la force du mot de passe
            passwordField.addEventListener('input', function() {
                const value = this.value;
                let strength = 0;
                
                if (value.length >= 8) strength += 20;
                if (value.match(/[A-Z]/)) strength += 20;
                if (value.match(/[a-z]/)) strength += 20;
                if (value.match(/[0-9]/)) strength += 20;
                if (value.match(/[^A-Za-z0-9]/)) strength += 20;
                
                // Mettre à jour l'indicateur visuel
                meterProgress.style.width = strength + '%';
                
                // Changer la couleur selon la force
                if (strength < 40) {
                    meterProgress.style.backgroundColor = '#ff4d4d';
                } else if (strength < 80) {
                    meterProgress.style.backgroundColor = '#ffd633';
                } else {
                    meterProgress.style.backgroundColor = '#47d147';
                }
                
                checkFormValidity();
            });
            
            // Vérifier la correspondance des mots de passe
            confirmField.addEventListener('input', checkFormValidity);
            
            // Vérifier l'acceptation des conditions
            policyCheckbox.addEventListener('change', checkFormValidity);
            
            function checkFormValidity() {
                const password = passwordField.value;
                const confirmPwd = confirmField.value;
                const policyAccepted = policyCheckbox.checked;
                
                const isValidPassword = password.length >= 8 && 
                                       password.match(/[A-Z]/) && 
                                       password.match(/[0-9]/) && 
                                       password.match(/[^A-Za-z0-9]/);
                
                const passwordsMatch = password === confirmPwd && confirmPwd !== '';
                
                submitBtn.disabled = !(isValidPassword && passwordsMatch && policyAccepted);
            }
        });
    </script>
</body>
</html>
