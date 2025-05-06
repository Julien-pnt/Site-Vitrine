<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/auth/login.php');
    exit;
}

// Connexion à la base de données
require_once '../../php/config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Définir le répertoire d'upload correct
$uploadDir = "../uploads/users/";

// Vérifier s'il existe
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

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

// Pour le chemin relatif
$relativePath = "..";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil | Elixir du Temps</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/layout/icon2.png" type="image/x-icon">
    
    <!-- Styles du tableau de bord -->
    <link rel="stylesheet" href="assets/css/dashboard-vars.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    
    <style>
        /* Styles spécifiques à la page profil */
        .dashboard-header {
            margin-bottom: var(--spacing-xl);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }
        
        .dashboard-header h1 {
            font-family: var(--font-primary);
            font-size: var(--font-size-xl);
            color: var(--secondary-color);
            margin-bottom: var(--spacing-xs);
            font-weight: 600;
        }
        
        .dashboard-header p {
            color: var(--medium-gray);
            font-size: var(--font-size-sm);
        }
        
        .dashboard-header-left {
            flex: 1;
        }
        
        .user-profile-photo {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }
        
        .profile-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-xl);
            font-weight: 600;
            overflow: hidden;
            box-shadow: 0 0 0 3px white, 0 0 0 5px rgba(212, 175, 55, 0.3);
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-info-name {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: var(--font-size-lg);
        }
        
        .user-info-member {
            color: var(--medium-gray);
            font-size: var(--font-size-sm);
        }
        
        /* Dashboard sections */
        .dashboard-section {
            background-color: var(--light-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: var(--spacing-xl);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg) var(--spacing-xl);
            border-bottom: 1px solid var(--border-color);
            background-color: var(--light-gray);
        }
        
        .section-header h2 {
            font-family: var(--font-primary);
            font-size: var(--font-size-lg);
            color: var(--secondary-color);
            margin: 0;
            font-weight: 600;
        }
        
        .section-header p {
            color: var(--medium-gray);
            font-size: var(--font-size-sm);
            margin-top: var(--spacing-xs);
        }
        
        .section-content {
            padding: var(--spacing-xl);
        }
        
        /* Forms */
        .form-group {
            margin-bottom: var(--spacing-lg);
        }
        
        .form-row {
            display: flex;
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
        }
        
        .form-column {
            flex: 1;
            min-width: 0;
        }
        
        label {
            display: block;
            margin-bottom: var(--spacing-xs);
            font-weight: 500;
            color: var(--secondary-color);
            font-size: var(--font-size-sm);
        }
        
        .required {
            color: var(--danger-color);
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: var(--spacing-md);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-family: var(--font-secondary);
            font-size: var(--font-size-md);
            color: var(--dark-gray);
            transition: var(--transition);
            background-color: var(--light-color);
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="tel"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
        }
        
        .form-hint {
            font-size: var(--font-size-xs);
            color: var(--medium-gray);
            margin-top: var(--spacing-xs);
        }
        
        .form-actions {
            padding-top: var(--spacing-md);
            display: flex;
            gap: var(--spacing-md);
            justify-content: flex-end;
            border-top: 1px solid var(--border-color);
            margin-top: var(--spacing-xl);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
            color: var(--light-color);
            border: none;
            padding: var(--spacing-md) var(--spacing-xl);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-sm);
            transition: var(--transition);
            font-weight: 500;
            box-shadow: var(--shadow-sm);
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--secondary-color));
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: var(--light-gray);
            color: var(--dark-gray);
            border: 1px solid var(--border-color);
            padding: var(--spacing-md) var(--spacing-xl);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-sm);
            transition: var(--transition);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background-color: var(--border-color);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-sm);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .btn-outline i {
            margin-right: var(--spacing-sm);
        }
        
        .btn-outline:hover {
            background-color: var(--primary-color);
            color: var(--light-color);
        }
        
        /* Alerts */
        .alert {
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--radius-sm);
            margin-bottom: var(--spacing-lg);
            display: flex;
            align-items: center;
            font-size: var(--font-size-sm);
        }
        
        .alert i {
            margin-right: var(--spacing-md);
            font-size: var(--font-size-md);
        }
        
        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(76, 175, 80, 0.2);
        }
        
        .alert-danger {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(244, 67, 54, 0.2);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--spacing-sm);
            }
            
            .form-row {
                flex-direction: column;
                gap: var(--spacing-md);
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-primary, .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <!-- Inclure la sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Main Content -->
    <main class="dashboard-content">
        <div class="dashboard-header">
            <div class="dashboard-header-left">
                <h1>Mon profil</h1>
                <p>Gérez vos informations personnelles</p>
            </div>
            
            <div class="user-profile-photo">
                <div class="profile-avatar">
                    <?php if (!empty($user['photo']) && file_exists($relativePath . "/uploads/users/" . $user['photo'])): ?>
                        <img src="<?php echo $relativePath; ?>/uploads/users/<?php echo htmlspecialchars($user['photo']); ?>" 
                             alt="<?php echo htmlspecialchars($user['prenom']); ?>">
                    <?php else: ?>
                        <?php 
                        $initials = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
                        echo $initials;
                        ?>
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <span class="user-info-name"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></span>
                    <span class="user-info-member">Membre depuis <?php echo date('M Y', strtotime($user['date_creation'])); ?></span>
                </div>
            </div>
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
            <div class="section-header">
                <div>
                    <h2>Informations personnelles</h2>
                    <p>Modifiez vos informations personnelles</p>
                </div>
            </div>
            
            <div class="section-content">
                <form action="profile.php" method="POST" class="profile-form">
                    <div class="form-row">
                        <div class="form-column">
                            <div class="form-group">
                                <label for="nom">Nom <span class="required">*</span></label>
                                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-column">
                            <div class="form-group">
                                <label for="prenom">Prénom</label>
                                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-column">
                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-column">
                            <div class="form-group">
                                <label for="telephone">Téléphone</label>
                                <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="index.php" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i>&nbsp;Retour
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>&nbsp;Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="dashboard-section">
            <div class="section-header">
                <div>
                    <h2>Modifier mon mot de passe</h2>
                    <p>Changez votre mot de passe pour sécuriser votre compte</p>
                </div>
            </div>
            
            <div class="section-content">
                <form action="change-password.php" method="POST" class="password-form">
                    <div class="form-row">
                        <div class="form-column">
                            <div class="form-group">
                                <label for="current_password">Mot de passe actuel <span class="required">*</span></label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-column">
                            <div class="form-group">
                                <label for="new_password">Nouveau mot de passe <span class="required">*</span></label>
                                <input type="password" id="new_password" name="new_password" required>
                                <p class="form-hint">Minimum 8 caractères, avec au moins une majuscule, un chiffre et un caractère spécial.</p>
                            </div>
                        </div>
                        
                        <div class="form-column">
                            <div class="form-group">
                                <label for="confirm_password">Confirmer le mot de passe <span class="required">*</span></label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-lock"></i>&nbsp;Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="dashboard-section">
            <div class="section-header">
                <div>
                    <h2>Photo de profil</h2>
                    <p>Téléchargez ou mettez à jour votre photo de profil</p>
                </div>
            </div>
            
            <div class="section-content">
                <form action="update-photo.php" method="POST" enctype="multipart/form-data" class="photo-form">
                    <div class="form-group">
                        <label for="profile_photo">Choisir une nouvelle photo</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                        <p class="form-hint">Formats acceptés : JPG, PNG. Taille maximale : 2 Mo.</p>
                    </div>
                    
                    <div class="form-actions">
                        <?php if (!empty($user['photo'])): ?>
                        <a href="delete-photo.php" class="btn-secondary" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')">
                            <i class="fas fa-trash"></i>&nbsp;Supprimer la photo
                        </a>
                        <?php endif; ?>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-upload"></i>&nbsp;Télécharger la photo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validation du formulaire de mot de passe
        const passwordForm = document.querySelector('.password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Les mots de passe ne correspondent pas.');
                    return false;
                }
                
                // Valider la complexité du mot de passe
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                if (!passwordRegex.test(newPassword)) {
                    e.preventDefault();
                    alert('Le mot de passe doit comporter au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.');
                    return false;
                }
            });
        }
    });
</script>

</body>
</html>