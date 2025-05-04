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

// Configuration des variables pour le header
$relativePath = "../..";
$pageTitle = "Connexion - Elixir du Temps";
$pageDescription = "Connectez-vous à votre compte Elixir du Temps pour accéder à vos commandes et préférences.";

// CSS spécifique à la page
$additionalCss = '
<link rel="stylesheet" href="'.$relativePath.'/assets/css/auth.css">
';

// Code supplémentaire dans le head
$additionalHead = '
<!-- Meta tags SEO -->
<meta name="keywords" content="connexion, compte, elixir du temps, montres de luxe">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:title" content="Connexion - Elixir du Temps">
<meta property="og:description" content="Accédez à votre espace client Elixir du Temps.">
<meta property="og:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
<meta property="og:url" content="https://elixirdutemps.com/pages/auth/login">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:title" content="Connexion - Elixir du Temps">
<meta property="twitter:description" content="Accédez à votre espace client Elixir du Temps.">
<meta property="twitter:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
';

// Important: Définir la valeur correcte pour que le menu fonctionne
$currentPage = "auth/login.php";
$headerClass = "dark-header fixed-header"; // Ajout de classes pour styliser le header
$hideUserIcon = true; // Masquer l'icône utilisateur car nous sommes sur la page de connexion

// Inclusion du header
require_once "../../Includes/header.php";
?>

<style>
    /* Styles spécifiques à la page de connexion */
    .auth-section {
        padding: 60px 0;
        position: relative;
        z-index: 1;
        min-height: calc(100vh - 180px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .auth-container {
        max-width: 480px;
        width: 100%;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        padding: 40px;
        position: relative;
        overflow: hidden;
    }
    
    .auth-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #d4af37, #f5e7ba, #d4af37);
        border-radius: 12px 12px 0 0;
    }
    
    .auth-title {
        color: #1a1a1a;
        font-size: 2rem;
        text-align: center;
        margin-bottom: 30px;
        font-family: 'Playfair Display', serif;
        position: relative;
        padding-bottom: 15px;
    }
    
    .auth-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 2px;
        background-color: #d4af37;
    }
    
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }
    
    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }
    
    .form-group {
        position: relative;
        margin-bottom: 25px;
    }
    
    .form-icon {
        position: absolute;
        left: 0;
        top: 12px;
        color: #666;
    }
    
    .form-group input {
        width: 100%;
        padding: 12px 12px 12px 30px;
        border: none;
        background: transparent;
        font-size: 1rem;
        color: #333;
        border-bottom: 1px solid #ddd;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus {
        outline: none;
        border-bottom-color: #d4af37;
    }
    
    .form-group label {
        position: absolute;
        left: 30px;
        top: 12px;
        color: #666;
        font-size: 1rem;
        transition: all 0.3s ease;
        pointer-events: none;
    }
    
    .form-group input:focus + label,
    .form-group label.active {
        top: -10px;
        font-size: 0.85rem;
        color: #d4af37;
    }
    
    .form-border {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 0;
        background-color: #d4af37;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus ~ .form-border {
        width: 100%;
    }
    
    .forgot-password {
        text-align: right;
        margin-bottom: 25px;
    }
    
    .forgot-password a {
        color: #666;
        font-size: 0.9rem;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .forgot-password a:hover {
        color: #d4af37;
        text-decoration: underline;
    }
    
    .btn-primary.auth-button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #d4af37 0%, #f5e7ba 50%, #d4af37 100%);
        background-size: 200% auto;
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    }
    
    .btn-primary.auth-button:hover {
        background-position: right center;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    }
    
    .auth-divider {
        display: flex;
        align-items: center;
        margin: 25px 0;
    }
    
    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background-color: #ddd;
    }
    
    .auth-divider span {
        padding: 0 15px;
        color: #666;
        font-size: 0.9rem;
    }
    
    .auth-footer {
        text-align: center;
    }
    
    .auth-footer p {
        margin-bottom: 15px;
        color: #666;
    }
    
    .btn-outline {
        display: inline-block;
        padding: 10px 25px;
        background: transparent;
        color: #333;
        border: 2px solid #d4af37;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-outline:hover {
        background-color: #d4af37;
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 576px) {
        .auth-container {
            padding: 30px 20px;
            max-width: 95%;
        }
        
        .auth-title {
            font-size: 1.8rem;
        }
    }
</style>

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

<!-- Scripts chargés à la fin pour optimiser le chargement -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/collection-sorting.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-detail.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-filters.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/quick-view.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/wishlist-manager.js"></script>

<?php
// Inclusion du footer
require_once "../../Includes/footer.php";
?>

<!-- Scripts spécifiques à la page -->
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
});
</script>

<script>
    // Script de diagnostic pour le panier
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Diagnostic du panier : chargement de la page");
        
        const cartIcon = document.querySelector('.cart-icon');
        const cartDropdown = document.querySelector('.cart-dropdown');
        
        console.log("Éléments trouvés:", {
            cartIcon: !!cartIcon,
            cartDropdown: !!cartDropdown
        });
        
        if (cartIcon && cartDropdown) {
            // Ajouter un attribut data pour indiquer que le diagnostic est actif
            cartIcon.setAttribute('data-diagnostic', 'active');
            
            // Gestionnaire d'événement direct
            cartIcon.addEventListener('click', function(e) {
                console.log("CLIC SUR LE PANIER DÉTECTÉ!");
                e.preventDefault();
                e.stopPropagation();
                
                // Force l'affichage en ajoutant un style inline
                if (cartDropdown.classList.contains('show')) {
                    console.log("Masquage du dropdown");
                    cartDropdown.classList.remove('show');
                    cartDropdown.style.display = 'none';
                } else {
                    console.log("Affichage du dropdown");
                    cartDropdown.classList.add('show');
                    cartDropdown.style.display = 'block';
                }
            });
            
            console.log("Gestionnaire d'événement ajouté au panier");
        }
    });
</script>