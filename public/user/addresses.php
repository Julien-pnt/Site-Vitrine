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

// Traiter le formulaire de mise à jour d'adresse
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adresse = trim($_POST['adresse']);
    $code_postal = trim($_POST['code_postal']);
    $ville = trim($_POST['ville']);
    $pays = trim($_POST['pays']);
    
    // Validation de base
    if (empty($adresse) || empty($code_postal) || empty($ville)) {
        $error = "L'adresse, le code postal et la ville sont obligatoires.";
    } else {
        // Mise à jour de l'adresse
        $stmt = $conn->prepare("
            UPDATE utilisateurs 
            SET adresse = ?, code_postal = ?, ville = ?, pays = ?, date_modification = NOW()
            WHERE id = ?
        ");
        
        $result = $stmt->execute([$adresse, $code_postal, $ville, $pays, $userId]);
        
        if ($result) {
            $success = true;
            
            // Rafraîchir les données utilisateur
            $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "Une erreur s'est produite lors de la mise à jour de l'adresse.";
        }
    }
}

// Titre de la page
$pageTitle = "Mes adresses";
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
                <h1>Mes adresses</h1>
                <p>Gérez vos adresses de livraison et de facturation</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Votre adresse a été mise à jour avec succès.
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="dashboard-section">
                <div class="section-header">
                    <h2>Adresse principale</h2>
                </div>
                <div class="section-content">
                    <form action="addresses.php" method="POST" class="address-form">
                        <div class="form-group">
                            <label for="adresse">Adresse <span class="required">*</span></label>
                            <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($user['adresse']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="code_postal">Code postal <span class="required">*</span></label>
                            <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($user['code_postal']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="ville">Ville <span class="required">*</span></label>
                            <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($user['ville']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="pays">Pays</label>
                            <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($user['pays']); ?>">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Enregistrer l'adresse</button>
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