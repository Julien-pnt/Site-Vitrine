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
    <title>Connexion - Elixir du Temps</title>
    <link rel="stylesheet" href="/public/assets/css/Styles.css">
    <link rel="shortcut icon" href="/public/assets/img/layout/icon.png" type="image/x-icon">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="logo-container">
            <img src="/public/assets/img/layout/logo.png" alt="Elixir du Temps Logo" class="logo">
        </div>
        <nav>
            <ul class="menu-bar">
                <li><a href="/public/pages/Accueil.html">Accueil</a></li>
                <li><a href="/public/pages/collections/Collections.html">Collections</a></li>
                <li><a href="/public/pages/products/Montres.html">Montres</a></li>
                <li><a href="/public/pages/products/DescriptionProduits.html">Description produits</a></li>
                <li><a href="/public/pages/APropos.html">À propos</a></li>
            </ul>
        </nav>
    </header>

    <!-- Video Background -->
    <div class="video-background">
        <video class="video-bg" autoplay muted loop playsinline>
            <source src="/public/assets/video/background.mp4" type="video/mp4">
            Votre navigateur ne supporte pas la vidéo.
        </video>
        <div class="video-overlay"></div>
    </div>

    <!-- Formulaire de connexion -->
    <div class="login-wrapper">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form" novalidate>
            <h2>Connexion</h2>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="input-field">
                <input type="email" id="email" name="email" required>
                <label for="email">Email</label>
            </div>
            <div class="input-field">
                <input type="password" id="password" name="password" required>
                <label for="password">Mot de passe</label>
            </div>
            <div class="forget">
                <label>
                    <input type="checkbox" name="remember">
                    <p>Se souvenir de moi</p>
                </label>
                <a href="reset-password.php">Mot de passe oublié ?</a>
            </div>
            <button type="submit">Se connecter</button>
            <div class="register">
                <p>Pas encore de compte ? <a href="register.html">S'inscrire</a></p>
            </div>
        </form>
    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Elixir du Temps. Tous droits réservés.</p>
            <ul class="footer-links">
                <li><a href="mailto:contact@elixirdutemps.com" class="mon-email">Contact</a></li>
                <li><a href="/public/pages/legal/PrivacyPolicy.html">Politique de confidentialité</a></li>
            </ul>
        </div>
    </footer>
    
    <script src="/public/assets/js/modules/login.js"></script>
</body>
</html>
