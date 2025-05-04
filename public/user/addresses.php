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

// Pour le chemin relatif
$relativePath = "..";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes adresses | Elixir du Temps</title>
    
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
        /* Styles spécifiques à la page d'adresses */
        .dashboard-header {
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
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
        
        /* Dashboard sections */
        .dashboard-section {
            background-color: var(--light-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: var(--spacing-xl);
            overflow: hidden;
            border: 1px solid var(--border-color);
            animation: fadeIn 0.5s ease;
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
            display: flex;
            align-items: center;
        }
        
        .section-header h2 i {
            color: var(--primary-color);
            margin-right: var(--spacing-sm);
        }
        
        .section-content {
            padding: var(--spacing-xl);
        }
        
        /* Forms */
        .address-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--spacing-lg);
        }
        
        .form-group {
            margin-bottom: var(--spacing-lg);
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
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
        select {
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
        select:focus {
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
            grid-column: 1 / -1;
            padding-top: var(--spacing-md);
            display: flex;
            gap: var(--spacing-md);
            justify-content: flex-end;
            border-top: 1px solid var(--border-color);
            margin-top: var(--spacing-lg);
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
        
        .btn-primary i {
            margin-right: var(--spacing-sm);
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
        
        .btn-secondary i {
            margin-right: var(--spacing-sm);
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
        
        /* Adresse existante */
        .address-display {
            background-color: var(--light-gray);
            padding: var(--spacing-lg);
            border-radius: var(--radius-sm);
            margin-bottom: var(--spacing-lg);
            position: relative;
        }
        
        .address-actions {
            position: absolute;
            top: var(--spacing-md);
            right: var(--spacing-md);
            display: flex;
            gap: var(--spacing-sm);
        }
        
        .address-action {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-color);
            border-radius: 50%;
            color: var(--medium-gray);
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }
        
        .address-action:hover {
            background-color: var(--primary-color);
            color: var(--light-color);
            border-color: var(--primary-color);
        }
        
        .address-label {
            display: inline-block;
            background-color: var(--primary-light);
            color: var(--primary-dark);
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: 4px;
            font-size: var(--font-size-xs);
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .address-details {
            font-size: var(--font-size-sm);
            line-height: 1.6;
        }
        
        .address-details p {
            margin: 0;
            margin-bottom: 4px;
        }
        
        .no-address {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: var(--spacing-xl);
            text-align: center;
            color: var(--medium-gray);
        }
        
        .no-address i {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
            opacity: 0.3;
        }
        
        .no-address p {
            margin-bottom: var(--spacing-md);
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .address-form {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
                align-items: stretch;
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
                <h2><i class="fas fa-map-marker-alt"></i> Adresse principale</h2>
            </div>
            
            <div class="section-content">
                <?php if (!empty($user['adresse']) && !empty($user['code_postal']) && !empty($user['ville'])): ?>
                <div class="address-display">
                    <div class="address-label">Adresse de livraison et facturation</div>
                    <div class="address-details">
                        <p><strong><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></strong></p>
                        <p><?php echo htmlspecialchars($user['adresse']); ?></p>
                        <p><?php echo htmlspecialchars($user['code_postal']) . ' ' . htmlspecialchars($user['ville']); ?></p>
                        <p><?php echo htmlspecialchars($user['pays']); ?></p>
                    </div>
                </div>
                <?php else: ?>
                <div class="no-address">
                    <i class="fas fa-home"></i>
                    <p>Vous n'avez pas encore d'adresse enregistrée.</p>
                </div>
                <?php endif; ?>
                
                <form action="addresses.php" method="POST" class="address-form">
                    <div class="form-group full-width">
                        <label for="adresse">Adresse <span class="required">*</span></label>
                        <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($user['adresse']); ?>" required placeholder="Numéro et nom de rue">
                    </div>
                    
                    <div class="form-group">
                        <label for="code_postal">Code postal <span class="required">*</span></label>
                        <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($user['code_postal']); ?>" required placeholder="Code postal">
                    </div>
                    
                    <div class="form-group">
                        <label for="ville">Ville <span class="required">*</span></label>
                        <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($user['ville']); ?>" required placeholder="Ville">
                    </div>
                    
                    <div class="form-group">
                        <label for="pays">Pays</label>
                        <select id="pays" name="pays">
                            <option value="France" <?php echo $user['pays'] === 'France' ? 'selected' : ''; ?>>France</option>
                            <option value="Belgique" <?php echo $user['pays'] === 'Belgique' ? 'selected' : ''; ?>>Belgique</option>
                            <option value="Suisse" <?php echo $user['pays'] === 'Suisse' ? 'selected' : ''; ?>>Suisse</option>
                            <option value="Luxembourg" <?php echo $user['pays'] === 'Luxembourg' ? 'selected' : ''; ?>>Luxembourg</option>
                            <option value="Monaco" <?php echo $user['pays'] === 'Monaco' ? 'selected' : ''; ?>>Monaco</option>
                            <option value="Allemagne" <?php echo $user['pays'] === 'Allemagne' ? 'selected' : ''; ?>>Allemagne</option>
                            <option value="Espagne" <?php echo $user['pays'] === 'Espagne' ? 'selected' : ''; ?>>Espagne</option>
                            <option value="Italie" <?php echo $user['pays'] === 'Italie' ? 'selected' : ''; ?>>Italie</option>
                            <option value="Royaume-Uni" <?php echo $user['pays'] === 'Royaume-Uni' ? 'selected' : ''; ?>>Royaume-Uni</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <a href="index.php" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Enregistrer l'adresse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validation du code postal français
        const codePostalInput = document.getElementById('code_postal');
        const paysSelect = document.getElementById('pays');
        
        if (codePostalInput && paysSelect) {
            codePostalInput.addEventListener('input', function() {
                // Si on est en France, on valide le format du code postal
                if (paysSelect.value === 'France') {
                    const regex = /^[0-9]{5}$/;
                    if (!regex.test(this.value)) {
                        this.setCustomValidity('Le code postal français doit comporter 5 chiffres.');
                    } else {
                        this.setCustomValidity('');
                    }
                } else {
                    this.setCustomValidity('');
                }
            });
            
            paysSelect.addEventListener('change', function() {
                // Réinitialiser la validation si on change de pays
                codePostalInput.setCustomValidity('');
            });
        }
    });
</script>

</body>
</html>