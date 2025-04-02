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

// Traitement du formulaire de demande de réinitialisation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Erreur de validation du formulaire";
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        
        // Validation de l'email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Veuillez fournir une adresse email valide";
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
                
                // Vérifier si l'email existe dans la base de données
                $stmt = $pdo->prepare("SELECT id, first_name FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Générer un token unique
                    $token = bin2hex(random_bytes(32));
                    $userId = $user['id'];
                    $expiresAt = time() + 3600; // 1 heure
                    
                    // Supprimer tout token existant pour cet utilisateur
                    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    
                    // Enregistrer le nouveau token
                    $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))");
                    $stmt->execute([$userId, password_hash($token, PASSWORD_DEFAULT), $expiresAt]);
                    
                    // Construire le lien de réinitialisation
                    $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/public/php/api/auth/reset-password.php?token=" . $token . "&email=" . urlencode($email);
                    
                    // Préparer l'email
                    $subject = "Réinitialisation de votre mot de passe - Elixir du Temps";
                    $message = "Bonjour " . htmlspecialchars($user['first_name']) . ",\n\n";
                    $message .= "Vous avez demandé la réinitialisation de votre mot de passe sur Elixir du Temps.\n\n";
                    $message .= "Pour créer un nouveau mot de passe, veuillez cliquer sur le lien suivant :\n";
                    $message .= $resetLink . "\n\n";
                    $message .= "Ce lien est valable pendant 1 heure.\n\n";
                    $message .= "Si vous n'avez pas effectué cette demande, vous pouvez ignorer cet email.\n\n";
                    $message .= "Cordialement,\n";
                    $message .= "L'équipe Elixir du Temps";
                    
                    $headers = "From: noreply@elixirdutemps.com\r\n";
                    $headers .= "Reply-To: contact@elixirdutemps.com\r\n";
                    
                    // Envoi de l'email
                    if (mail($email, $subject, $message, $headers)) {
                        $success = "Un email avec les instructions de réinitialisation vous a été envoyé.";
                    } else {
                        $error = "Impossible d'envoyer l'email. Veuillez réessayer plus tard.";
                    }
                } else {
                    // Par sécurité, ne pas indiquer si l'email existe ou non
                    $success = "Si cette adresse email est associée à un compte, un email avec les instructions de réinitialisation vous sera envoyé.";
                }
            } catch (Exception $e) {
                $error = "Une erreur est survenue. Veuillez réessayer plus tard.";
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
    <meta name="description" content="Réinitialisation de mot de passe pour votre compte Elixir du Temps.">
    <meta name="keywords" content="mot de passe oublié, réinitialisation, compte, elixir du temps, montres de luxe">
    
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
    <meta property="og:title" content="Réinitialisation de mot de passe - Elixir du Temps">
    <meta property="og:description" content="Réinitialisez votre mot de passe pour accéder à votre compte Elixir du Temps.">
    <meta property="og:image" content="/public/assets/img/layout/social-share.jpg">
    
    <!-- Ressources -->
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/auth.css">
    <link rel="shortcut icon" href="/public/assets/img/layout/icon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <title>Mot de passe oublié - Elixir du Temps</title>
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
            <h1 class="auth-title">Mot de passe oublié</h1>
            
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
            
            <form id="resetForm" class="auth-form" method="POST" novalidate>
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
                
                <p class="reset-instructions">Entrez l'adresse email associée à votre compte pour recevoir un lien de réinitialisation de mot de passe.</p>
                
                <button type="submit" id="resetButton" class="btn-primary auth-button">Réinitialiser mon mot de passe</button>
                
                <div class="auth-divider">
                    <span>ou</span>
                </div>
                
                <div class="auth-footer">
                    <p>Vous vous souvenez de votre mot de passe ?</p>
                    <a href="login.php" class="btn-outline">Retour à la connexion</a>
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
            
            // Auto-masquage des messages d'alerte après 5 secondes
            const alertDiv = document.getElementById('alert');
            if (alertDiv && alertDiv.classList.contains('show')) {
                setTimeout(() => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => {
                        alertDiv.style.display = 'none';
                    }, 300);
                }, 5000);
            }
            
            // Validation du formulaire
            const resetForm = document.getElementById('resetForm');
            if (resetForm) {
                resetForm.addEventListener('submit', function(e) {
                    const email = document.getElementById('email').value.trim();
                    let isValid = true;
                    
                    // Vérification de l'email
                    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        isValid = false;
                        // Créer ou afficher un message d'erreur
                        let errorDiv = document.getElementById('alert');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.id = 'alert';
                            errorDiv.className = 'alert alert-error';
                            resetForm.parentNode.insertBefore(errorDiv, resetForm);
                        }
                        errorDiv.textContent = "Veuillez entrer une adresse email valide";
                        errorDiv.style.display = 'block';
                        errorDiv.classList.add('show');
                        
                        // Auto-masquer l'erreur après 4 secondes
                        setTimeout(() => {
                            errorDiv.classList.remove('show');
                            setTimeout(() => {
                                errorDiv.style.display = 'none';
                            }, 300);
                        }, 4000);
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
    
    <!-- Style spécifique pour la page de réinitialisation -->
    <style>
        .reset-instructions {
            margin: 15px 0 25px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }
        
        /* Correction pour les labels des formulaires */
        .form-group input {
            width: 100%;
            padding: 15px 0 5px 30px; /* Augmenté le padding-left à 30px */
            border: none;
            background: transparent;
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #ddd;
        }

        .form-group label {
            position: absolute;
            left: 30px; /* Décalé les labels pour éviter la superposition avec les icônes */
            top: 15px;
            font-size: 16px;
            color: #888;
            pointer-events: none;
            transition: 0.3s ease all;
        }

        /* Ajuster le décalage lors de la saisie ou du focus */
        .form-group input:focus ~ label,
        .form-group input:valid ~ label,
        .form-group.active label {
            top: -5px;
            font-size: 12px;
            color: #c9a86b;
            left: 30px; /* Maintenir le décalage lorsque le label remonte */
        }
    </style>
</body>
</html>