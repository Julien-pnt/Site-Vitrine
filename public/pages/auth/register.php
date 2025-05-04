<?php
// Démarrer la session
session_start();

// Rediriger si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    // Rediriger vers le tableau de bord ou la page d'accueil selon le rôle
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: ../../admin/index.php');
    } else {
        header('Location: ../../user/index.php');
    }
    exit;
}

// Variables pour stocker les messages d'erreur et les valeurs soumises
$error_message = '';
$success_message = '';
$form_data = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'telephone' => '',
    'adresse' => '',
    'code_postal' => '',
    'ville' => '',
    'pays' => 'France'
];

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../../php/config/database.php';
    
    // Récupérer et nettoyer les entrées
    $form_data['nom'] = trim(filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS));
    $form_data['prenom'] = trim(filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS));
    $form_data['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $form_data['telephone'] = trim(filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_SPECIAL_CHARS));
    $form_data['adresse'] = trim(filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_SPECIAL_CHARS));
    $form_data['code_postal'] = trim(filter_input(INPUT_POST, 'code_postal', FILTER_SANITIZE_SPECIAL_CHARS));
    $form_data['ville'] = trim(filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_SPECIAL_CHARS));
    $form_data['pays'] = trim(filter_input(INPUT_POST, 'pays', FILTER_SANITIZE_SPECIAL_CHARS)) ?: 'France';
    
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validation basique
    if (empty($form_data['nom']) || empty($form_data['prenom']) || empty($form_data['email']) || empty($password)) {
        $error_message = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Veuillez fournir une adresse email valide.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($password !== $password_confirm) {
        $error_message = 'Les mots de passe ne correspondent pas.';
    } else {
        try {
            // Connexion à la base de données
            $db = new Database();
            $conn = $db->getConnection();
            
            // Vérifier si l'email existe déjà
            $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
            $stmt->execute([$form_data['email']]);
            
            if ($stmt->rowCount() > 0) {
                $error_message = 'Cette adresse email est déjà utilisée. Veuillez en choisir une autre.';
            } else {
                // Hasher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Préparer l'insertion
                $stmt = $conn->prepare(
                    "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, adresse, code_postal, ville, pays) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                
                // Exécuter l'insertion
                $result = $stmt->execute([
                    $form_data['nom'],
                    $form_data['prenom'],
                    $form_data['email'],
                    $hashed_password,
                    $form_data['telephone'],
                    $form_data['adresse'],
                    $form_data['code_postal'],
                    $form_data['ville'],
                    $form_data['pays']
                ]);
                
                if ($result) {
                    // Inscription réussie
                    $success_message = 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.';
                    
                    // Vider les données du formulaire
                    $form_data = [
                        'nom' => '',
                        'prenom' => '',
                        'email' => '',
                        'telephone' => '',
                        'adresse' => '',
                        'code_postal' => '',
                        'ville' => '',
                        'pays' => 'France'
                    ];
                } else {
                    $error_message = 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.';
                }
            }
        } catch (PDOException $e) {
            $error_message = 'Une erreur est survenue lors de la création du compte. Veuillez réessayer plus tard.';
            // Pour le débogage: error_log('Erreur d\'inscription: ' . $e->getMessage());
        }
    }
}

// Configuration des variables pour le header
$relativePath = "../..";
$pageTitle = "Inscription - Elixir du Temps";
$pageDescription = "Créez votre compte Elixir du Temps pour profiter d'une expérience personnalisée et suivre vos commandes.";

// CSS spécifique à la page
$additionalCss = '
<link rel="stylesheet" href="'.$relativePath.'/assets/css/auth.css">
';

// Code supplémentaire dans le head
$additionalHead = '
<!-- Meta tags SEO -->
<meta name="keywords" content="inscription, compte, elixir du temps, montres de luxe, création compte">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:title" content="Inscription - Elixir du Temps">
<meta property="og:description" content="Créez votre compte Elixir du Temps pour profiter d\'une expérience personnalisée.">
<meta property="og:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
<meta property="og:url" content="https://elixirdutemps.com/pages/auth/register">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:title" content="Inscription - Elixir du Temps">
<meta property="twitter:description" content="Créez votre compte Elixir du Temps pour profiter d\'une expérience personnalisée.">
<meta property="twitter:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
';

// Important: Définir la valeur correcte pour que le menu fonctionne
$currentPage = "auth/register.php";
$headerClass = "dark-header fixed-header"; // Ajout de classes pour styliser le header
$hideUserIcon = true; // Masquer l'icône utilisateur car nous sommes sur la page d'inscription

// Inclusion du header
require_once "../../Includes/header.php";
?>

<style>
    /* Styles spécifiques à la page d'inscription */
    .auth-section {
        padding: 60px 0;
        position: relative;
        z-index: 1;
        min-height: calc(100vh - 180px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .auth-container {
        max-width: 600px;
        width: 100%;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        padding: 40px;
        position: relative;
        overflow: hidden;
        margin: 40px auto;
    }
    
    .auth-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #d4af37, #f5e7ba, #d4af37);
        border-radius: 12px 12px 0 0;
    }
    
    .auth-title {
        color: #1a1a1a;
        font-size: 2rem;
        text-align: center;
        margin-bottom: 30px;
        font-family: 'Playfair Display', serif;
        position: relative;
        padding-bottom: 15px;
    }
    
    .auth-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 2px;
        background-color: #d4af37;
    }
    
    .auth-subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 30px;
        font-size: 1rem;
    }
    
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }
    
    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }
    
    .alert-success {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.2);
    }
    
    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }
    
    .form-col {
        flex: 1 0 100%;
        padding: 0 10px;
    }
    
    .form-col-half {
        flex: 1 0 50%;
        padding: 0 10px;
    }
    
    .form-group {
        position: relative;
        margin-bottom: 25px;
    }
    
    .form-icon {
        position: absolute;
        left: 0;
        top: 12px;
        color: #666;
    }
    
    .form-group input {
        width: 100%;
        padding: 12px 12px 12px 30px;
        border: none;
        background: transparent;
        font-size: 1rem;
        color: #333;
        border-bottom: 1px solid #ddd;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus {
        outline: none;
        border-bottom-color: #d4af37;
    }
    
    .form-group textarea {
        width: 100%;
        padding: 12px 12px 12px 30px;
        border: none;
        background: transparent;
        font-size: 1rem;
        color: #333;
        border-bottom: 1px solid #ddd;
        transition: all 0.3s ease;
        min-height: 100px;
        resize: vertical;
    }
    
    .form-group textarea:focus {
        outline: none;
        border-bottom-color: #d4af37;
    }
    
    .form-group label {
        position: absolute;
        left: 30px;
        top: 12px;
        color: #666;
        font-size: 1rem;
        transition: all 0.3s ease;
        pointer-events: none;
    }
    
    .form-group input:focus + label,
    .form-group input:not(:placeholder-shown) + label,
    .form-group textarea:focus + label,
    .form-group textarea:not(:placeholder-shown) + label,
    .form-group label.active {
        top: -10px;
        font-size: 0.85rem;
        color: #d4af37;
    }
    
    .form-border {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 0;
        background-color: #d4af37;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus ~ .form-border,
    .form-group textarea:focus ~ .form-border {
        width: 100%;
    }
    
    .required-field::after {
        content: '*';
        color: #d4af37;
        margin-left: 4px;
    }
    
    .password-requirements {
        margin-top: 5px;
        font-size: 0.8rem;
        color: #666;
        font-style: italic;
    }
    
    .btn-primary.auth-button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #d4af37 0%, #f5e7ba 50%, #d4af37 100%);
        background-size: 200% auto;
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    }
    
    .btn-primary.auth-button:hover {
        background-position: right center;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    }
    
    .auth-divider {
        display: flex;
        align-items: center;
        margin: 25px 0;
    }
    
    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background-color: #ddd;
    }
    
    .auth-divider span {
        padding: 0 15px;
        color: #666;
        font-size: 0.9rem;
    }
    
    .auth-footer {
        text-align: center;
    }
    
    .auth-footer p {
        margin-bottom: 15px;
        color: #666;
    }
    
    .btn-outline {
        display: inline-block;
        padding: 10px 25px;
        background: transparent;
        color: #333;
        border: 2px solid #d4af37;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-outline:hover {
        background-color: #d4af37;
        color: white;
    }
    
    .form-section-title {
        font-size: 1.2rem;
        color: #333;
        margin: 30px 0 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
        font-family: 'Playfair Display', serif;
    }
    
    .form-note {
        font-size: 0.9rem;
        color: #666;
        margin-top: -15px;
        margin-bottom: 20px;
        font-style: italic;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .form-col-half {
            flex: 1 0 100%;
        }
        
        .auth-container {
            padding: 30px 20px;
            max-width: 95%;
        }
        
        .auth-title {
            font-size: 1.8rem;
        }
    }
</style>

<!-- Authentication Section -->
<section class="auth-section">
    <div class="auth-container">
        <h1 class="auth-title">Créer un compte</h1>
        <p class="auth-subtitle">Rejoignez l'univers Elixir du Temps</p>
        
        <?php if (!empty($error_message)): ?>
        <div id="alert" class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
        <div id="success" class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <form id="registerForm" class="auth-form" method="post" action="register.php" novalidate>
            <div class="form-section-title">Informations personnelles</div>
            
            <div class="form-row">
                <div class="form-col-half">
                    <div class="form-group">
                        <!-- Icône nom -->
                        <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($form_data['nom']); ?>" placeholder=" " required>
                        <label for="nom" class="required-field <?php echo !empty($form_data['nom']) ? 'active' : ''; ?>">Nom</label>
                        <div class="form-border"></div>
                    </div>
                </div>
                
                <div class="form-col-half">
                    <div class="form-group">
                        <!-- Icône prénom -->
                        <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($form_data['prenom']); ?>" placeholder=" " required>
                        <label for="prenom" class="required-field <?php echo !empty($form_data['prenom']) ? 'active' : ''; ?>">Prénom</label>
                        <div class="form-border"></div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <!-- Icône email -->
                <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" placeholder=" " required>
                <label for="email" class="required-field <?php echo !empty($form_data['email']) ? 'active' : ''; ?>">Adresse email</label>
                <div class="form-border"></div>
            </div>
            
            <div class="form-group">
                <!-- Icône téléphone -->
                <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($form_data['telephone']); ?>" placeholder=" ">
                <label for="telephone" class="<?php echo !empty($form_data['telephone']) ? 'active' : ''; ?>">Téléphone</label>
                <div class="form-border"></div>
            </div>
            
            <div class="form-section-title">Mot de passe</div>
            
            <div class="form-group">
                <!-- Icône mot de passe -->
                <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input type="password" id="password" name="password" placeholder=" " required>
                <label for="password" class="required-field">Mot de passe</label>
                <div class="form-border"></div>
            </div>
            <p class="password-requirements">Le mot de passe doit contenir au moins 8 caractères.</p>
            
            <div class="form-group">
                <!-- Icône confirmation mot de passe -->
                <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input type="password" id="password_confirm" name="password_confirm" placeholder=" " required>
                <label for="password_confirm" class="required-field">Confirmer le mot de passe</label>
                <div class="form-border"></div>
            </div>
            
            <div class="form-section-title">Adresse (facultatif)</div>
            <p class="form-note">Ces informations pourront être complétées ultérieurement.</p>
            
            <div class="form-group">
                <!-- Icône adresse -->
                <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <textarea id="adresse" name="adresse" placeholder=" "><?php echo htmlspecialchars($form_data['adresse']); ?></textarea>
                <label for="adresse" class="<?php echo !empty($form_data['adresse']) ? 'active' : ''; ?>">Adresse</label>
                <div class="form-border"></div>
            </div>
            
            <div class="form-row">
                <div class="form-col-half">
                    <div class="form-group">
                        <!-- Icône code postal -->
                        <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                            <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                        </svg>
                        <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($form_data['code_postal']); ?>" placeholder=" ">
                        <label for="code_postal" class="<?php echo !empty($form_data['code_postal']) ? 'active' : ''; ?>">Code postal</label>
                        <div class="form-border"></div>
                    </div>
                </div>
                
                <div class="form-col-half">
                    <div class="form-group">
                        <!-- Icône ville -->
                        <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($form_data['ville']); ?>" placeholder=" ">
                        <label for="ville" class="<?php echo !empty($form_data['ville']) ? 'active' : ''; ?>">Ville</label>
                        <div class="form-border"></div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <!-- Icône pays -->
                <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    <path d="M2 12h20"></path>
                </svg>
                <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($form_data['pays']); ?>" placeholder=" ">
                <label for="pays" class="<?php echo !empty($form_data['pays']) ? 'active' : ''; ?>">Pays</label>
                <div class="form-border"></div>
            </div>
            
            <button type="submit" id="registerButton" class="btn-primary auth-button">Créer mon compte</button>
            
            <div class="auth-divider">
                <span>ou</span>
            </div>
            
            <div class="auth-footer">
                <p>Vous avez déjà un compte ?</p>
                <a href="login.php" class="btn-outline">Se connecter</a>
            </div>
        </form>
    </div>
</section>

<!-- Scripts chargés à la fin pour optimiser le chargement -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/collection-sorting.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-detail.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-filters.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/quick-view.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/wishlist-manager.js"></script>

<?php
// Inclusion du footer
require_once "../../Includes/footer.php";
?>

<!-- Scripts spécifiques à la page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des champs de formulaire
    const inputs = document.querySelectorAll('.form-group input, .form-group textarea');
    
    inputs.forEach(input => {
        // Ajouter la classe active si le champ a une valeur
        if (input.value) {
            input.parentNode.querySelector('label').classList.add('active');
        }
        
        input.addEventListener('focus', function() {
            this.parentNode.querySelector('label').classList.add('active');
        });
        
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentNode.querySelector('label').classList.remove('active');
            }
        });
    });
    
    // Si un message de succès est affiché, rediriger vers la page de connexion après 3 secondes
    const successMessage = document.getElementById('success');
    if (successMessage) {
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 3000);
    }
    
    // Validation du formulaire côté client
    const registerForm = document.getElementById('registerForm');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let hasError = false;
            const errorMessages = [];
            
            // Récupération des valeurs
            const nom = document.getElementById('nom').value.trim();
            const prenom = document.getElementById('prenom').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            // Validation des champs obligatoires
            if (!nom || !prenom || !email || !password || !passwordConfirm) {
                errorMessages.push('Veuillez remplir tous les champs obligatoires.');
                hasError = true;
            }
            
            // Validation de l'email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailPattern.test(email)) {
                errorMessages.push('Veuillez fournir une adresse email valide.');
                hasError = true;
            }
            
            // Validation du mot de passe
            if (password && password.length < 8) {
                errorMessages.push('Le mot de passe doit contenir au moins 8 caractères.');
                hasError = true;
            }
            
            // Validation de la confirmation du mot de passe
            if (password && passwordConfirm && password !== passwordConfirm) {
                errorMessages.push('Les mots de passe ne correspondent pas.');
                hasError = true;
            }
            
            // Afficher les erreurs ou soumettre le formulaire
            if (hasError) {
                e.preventDefault();
                
                // Créer ou mettre à jour l'alerte d'erreur
                let alertBox = document.getElementById('alert');
                if (!alertBox) {
                    alertBox = document.createElement('div');
                    alertBox.id = 'alert';
                    alertBox.className = 'alert alert-danger';
                    registerForm.insertBefore(alertBox, registerForm.firstChild);
                }
                
                alertBox.textContent = errorMessages.join(' ');
                
                // Faire défiler jusqu'à l'alerte
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }
});
</script>

<script>
    // Script de diagnostic pour le panier
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Diagnostic du panier : chargement de la page");
        
        const cartIcon = document.querySelector('.cart-icon');
        const cartDropdown = document.querySelector('.cart-dropdown');
        
        console.log("Éléments trouvés:", {
            cartIcon: !!cartIcon,
            cartDropdown: !!cartDropdown
        });
        
        if (cartIcon && cartDropdown) {
            // Ajouter un attribut data pour indiquer que le diagnostic est actif
            cartIcon.setAttribute('data-diagnostic', 'active');
            
            // Gestionnaire d'événement direct
            cartIcon.addEventListener('click', function(e) {
                console.log("CLIC SUR LE PANIER DÉTECTÉ!");
                e.preventDefault();
                e.stopPropagation();
                
                // Force l'affichage en ajoutant un style inline
                if (cartDropdown.classList.contains('show')) {
                    console.log("Masquage du dropdown");
                    cartDropdown.classList.remove('show');
                    cartDropdown.style.display = 'none';
                } else {
                    console.log("Affichage du dropdown");
                    cartDropdown.classList.add('show');
                    cartDropdown.style.display = 'block';
                }
            });
            
            console.log("Gestionnaire d'événement ajouté au panier");
        }
    });
</script>