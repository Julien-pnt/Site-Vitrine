<?php
// Script de création d'un utilisateur administrateur
require_once __DIR__ . '/../php/config/database.php';

// Information de l'administrateur à créer
$admin_username = 'admin';
$admin_password = 'admin';
$admin_email = 'admin@elixirdutemps.com';

// Vérifier si l'admin existe déjà
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$stmt->execute([$admin_email]);
$exists = $stmt->fetch();

if ($exists) {
    echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 20px;">
            <h2>Attention</h2>
            <p>Un administrateur avec l\'email ' . htmlspecialchars($admin_email) . ' existe déjà.</p>
            <p>Utilisez cet utilisateur existant ou supprimez-le d\'abord si vous souhaitez le recréer.</p>
          </div>';
} else {
    try {
        // Hasher le mot de passe
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        
        // Insérer le nouvel administrateur
        $stmt = $pdo->prepare("
            INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, actif) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'Administrateur', // nom
            'Admin',          // prenom
            $admin_email,     // email
            $hashed_password, // mot_de_passe
            'admin',          // role
            true              // actif
        ]);
        
        echo '<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 20px;">
                <h2>Succès</h2>
                <p>L\'administrateur a été créé avec succès !</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($admin_email) . '</p>
                <p><strong>Mot de passe:</strong> ' . htmlspecialchars($admin_password) . '</p>
                <p><a href="../../public/admin/index.php" style="background: #155724; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 10px;">Accéder au tableau de bord</a></p>
              </div>';
    } catch (PDOException $e) {
        echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 20px;">
                <h2>Erreur</h2>
                <p>Impossible de créer l\'administrateur : ' . htmlspecialchars($e->getMessage()) . '</p>
              </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création d'un compte administrateur - Elixir du Temps</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #d4af37;
            text-align: center;
            margin-bottom: 30px;
        }
        .security-notice {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        hr {
            border: 0;
            height: 1px;
            background: #ddd;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <h1>Création d'un compte administrateur</h1>
    
    <div class="security-notice">
        <strong>⚠️ Attention :</strong> Ce script crée un administrateur avec le mot de passe "admin". 
        Pour des raisons de sécurité, veuillez modifier ce mot de passe dès que possible ou supprimer ce script 
        après son utilisation.
    </div>
    
    <hr>
    
    <p><strong>Étapes suivantes :</strong></p>
    <ol>
        <li>Connectez-vous au <a href="../../public/admin/index.php">tableau de bord</a></li>
        <li>Changez immédiatement le mot de passe admin par défaut</li>
        <li>Supprimez ce script de création d'administrateur</li>
    </ol>
</body>
</html>