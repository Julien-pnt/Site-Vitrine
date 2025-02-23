<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hacher le mot de passe
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insérer l'utilisateur dans la base de données
    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)");
    $stmt->execute(['email' => $email, 'password_hash' => $password_hash]);

    // Rediriger vers la page de connexion
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
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
                <li><a href="../html/Acceuil.html">Accueil</a></li>
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
        <video class="video-bg" autoplay muted loop>
            <source src="../video/background.mp4" type="video/mp4">
            Votre navigateur ne supporte pas la vidéo.
        </video>
        <div class="video-overlay"></div>
    </div>

    <!-- Formulaire d'inscription -->
    <div class="login-wrapper">
        <form method="POST" class="login-form">
            <h2>Inscription</h2>
            <div class="input-field">
                <input type="email" id="email" name="email" required>
                <label for="email">Email</label>
            </div>
            <div class="input-field">
                <input type="password" id="password" name="password" required>
                <label for="password">Mot de passe</label>
            </div>
            <button type="submit">S'inscrire</button>
            <div class="register">
                <p>Déjà un compte ? <a href="../html/login.html">Se connecter</a></p>
            </div>
        </form>
    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2025 Elixir du Temps. Tous droits réservés.</p>
            <ul class="footer-links">
                <li><a href="mailto:contact@elixirdutemps.com" class="mon-email">Contact</a></li>
                <li><a href="../html/PrivacyPolicy.html">Politique de confidentialité</a></li>
            </ul>
        </div>
    </footer>
</body>
</html>