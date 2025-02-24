<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Utiliser htmlspecialchars au lieu de FILTER_SANITIZE_STRING
    $nom = htmlspecialchars(trim($_POST['nom']), ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Validation
    if (empty($nom)) {
        $_SESSION['error'] = "Le nom est requis";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Format d'email invalide";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (strlen($password) < 8) {
        $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if ($password !== $confirmPassword) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    try {
        // Vérifier si l'email existe déjà
        $check = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            $_SESSION['error'] = "Cet email est déjà utilisé";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Hacher le mot de passe
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Insérer l'utilisateur dans la base de données
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $email, $password_hash]);

        $_SESSION['success'] = "Compte créé avec succès";
        header("Location: ../html/login.html");
        exit;
    } catch (PDOException $e) {
        error_log("Erreur d'inscription : " . $e->getMessage());
        $_SESSION['error'] = "Une erreur est survenue lors de l'inscription";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Elixir du Temps</title>
    <link rel="stylesheet" href="../css/Styles.css">
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

    <!-- Formulaire d'inscription -->
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
            <h2>Inscription</h2>
            <div class="input-field">
                <input type="text" id="nom" name="nom" required>
                <label for="nom">Nom</label>
            </div>
            <div class="input-field">
                <input type="email" id="email" name="email" required 
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                <label for="email">Email</label>
            </div>
            <div class="input-field">
                <input type="password" id="password" name="password" required 
                       minlength="8">
                <label for="password">Mot de passe</label>
                <small>Minimum 8 caractères</small>
            </div>
            <div class="input-field">
                <input type="password" id="confirm-password" name="confirm-password" required>
                <label for="confirm-password">Confirmer le mot de passe</label>
            </div>
            <button type="submit">S'inscrire</button>
            <div class="register">
                <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
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
</body>
</html>