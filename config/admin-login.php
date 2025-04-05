<?php
session_start();
require_once '../php/config/database.php'; 
require_once '../php/utils/auth.php';  

// Si déjà connecté en tant qu'admin, rediriger vers le dashboard
if (isLoggedIn() && isAdmin()) {
    header('Location: ../public/admin/index.php');
    exit;
}

$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    // Validation basique
    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        try {
            // Vérifier les identifiants
            $stmt = $pdo->prepare('SELECT id, email, mot_de_passe, role, prenom, nom FROM utilisateurs WHERE email = ? AND role = "admin" AND actif = 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Créer la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_nom'] = $user['nom'];
                
                // Rediriger vers le dashboard
                header('Location: ../public/admin/index.php');
                exit;
            } else {
                $error = "Identifiants incorrects ou vous n'avez pas les droits d'administration.";
            }
        } catch (PDOException $e) {
            $error = "Erreur de base de données: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Connexion | Elixir du Temps</title>
    <!-- Styles critiques intégrés pour éviter le fondu blanc -->
    <style>
        /* Règle pour assurer la visibilité immédiate */
        html, body {
            background-color: #121212; /* Fond sombre pour éviter le flash blanc */
            color: #f0f0f0;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            transition: background-color 0.5s ease;
        }
        
        /* Animation de fade-in pour l'ensemble du contenu */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .login-container {
            background: #1a1a1a;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            padding: 30px;
            width: 400px;
            animation: fadeIn 0.6s ease-out forwards;
            color: #f0f0f0;
        }
        
        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 140px;
        }
        
        h1 {
            text-align: center;
            color: #d4af37;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 6px;
            color: #d4d4d4;
            font-size: 14px;
        }
        
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            background-color: #333;
            border: 1px solid #444;
            border-radius: 4px;
            font-size: 15px;
            box-sizing: border-box;
            color: #fff;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }
        
        input[type="email"]:focus, input[type="password"]:focus {
            background-color: #3a3a3a;
            border-color: #d4af37;
            outline: none;
        }
        
        button {
            background-color: #d4af37;
            color: #121212;
            border: none;
            border-radius: 4px;
            padding: 12px 0;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        
        button:hover {
            background-color: #e5c158;
            transform: translateY(-2px);
        }
        
        .error {
            color: #ff6b6b;
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
            background-color: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 4px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .back-link a {
            color: #d4af37;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .back-link a:hover {
            color: #e5c158;
            text-decoration: underline;
        }
        
        /* Assure que le contenu est toujours lisible pendant le chargement */
        * {
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
    </style>
    <!-- Script pour éviter le flash de contenu blanc -->
    <script>
        // Définir le thème sombre immédiatement
        document.documentElement.style.backgroundColor = '#121212';
        document.documentElement.style.color = '#f0f0f0';
        
        // Transition en douceur vers le thème final
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.body.style.backgroundColor = '#121212';
            }, 10);
        });
    </script>
</head>
<body>
    <div class="login-container">
        <img src="../public/assets/img/layout/logo.png" alt="Elixir du Temps" class="logo">
        <h1>Accès Administration</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Adresse Email</label>
                <input type="email" id="email" name="email" value="admin@elixirdutemps.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" value="admin" required>
            </div>
            
            <button type="submit">Connexion</button>
        </form>
        
        <div class="back-link">
            <a href="../public/pages/Accueil.html">Retour au site</a>
        </div>
    </div>
</body>
</html>