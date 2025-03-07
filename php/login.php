<?php
session_start();
require_once 'db.php';
require_once 'AuthService.php';

// Rediriger si déjà connecté
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validation des données
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs";
    } else {
        try {
            $authService = new AuthService($pdo);
            $user = $authService->login($email, $password);
            
            // Définir un cookie si "Se souvenir de moi" est coché
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/', '', true, true);
                
                // Stocker le token en base de données (ajoutez une table pour cela)
                // Cette partie est simplifiée pour l'exemple
            }
            
            // Rediriger vers le tableau de bord
            header('Location: dashboard.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Elixir du Temps</title>
    <link rel="stylesheet" href="../css/Styles.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
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

    <!-- Formulaire de connexion -->
    <div class="login-wrapper">
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
        
        <form method="POST" class="login-form" novalidate>
            <h2>Connexion</h2>
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
                <p>Pas encore de compte ? <a href="userCreation.php">S'inscrire</a></p>
            </div>
        </form>
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
</body>
</html>
