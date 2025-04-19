<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/auth/login.html');
    exit;
}

// Connexion à la base de données
require_once '../../php/config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Récupérer les informations de l'utilisateur
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Traiter le formulaire de mise à jour
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    
    // Validation de base
    if (empty($nom) || empty($email)) {
        $error = "Le nom et l'email sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    } else {
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Cet email est déjà utilisé par un autre compte.";
        } else {
            // Mise à jour des informations
            $stmt = $conn->prepare("
                UPDATE utilisateurs 
                SET nom = ?, prenom = ?, email = ?, telephone = ?, date_modification = NOW()
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$nom, $prenom, $email, $telephone, $userId]);
            
            if ($result) {
                $success = true;
                
                // Mettre à jour les données en session
                $_SESSION['user_email'] = $email;
                
                // Rafraîchir les données utilisateur
                $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Une erreur s'est produite lors de la mise à jour du profil.";
            }
        }
    }
}

// Titre de la page
$pageTitle = "Mon profil";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | Elixir du Temps</title>
    
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components/header.css">
    <link rel="stylesheet" href="../assets/css/components/footer.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- En-tête -->
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <!-- Barre latérale -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Contenu principal -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <h1>Mon profil</h1>
                <p>Gérez vos informations personnelles</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Vos informations ont été mises à jour avec succès.
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="dashboard-section">
                <div class="section-content">
                    <form action="profile.php" method="POST" class="profile-form">
                        <div class="form-group">
                            <label for="nom">Nom <span class="required">*</span></label>
                            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="dashboard-section">
                <div class="section-header">
                    <h2>Modifier mon mot de passe</h2>
                </div>
                
                <div class="section-content">
                    <form action="change-password.php" method="POST" class="password-form">
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel <span class="required">*</span></label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe <span class="required">*</span></label>
                            <input type="password" id="new_password" name="new_password" required>
                            <p class="form-hint">Minimum 8 caractères, avec au moins une majuscule, un chiffre et un caractère spécial.</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le mot de passe <span class="required">*</span></label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Changer le mot de passe</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Pied de page -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/video-background.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>