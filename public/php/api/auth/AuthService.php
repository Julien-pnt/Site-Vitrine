<?php
class AuthService {
    private $pdo;
    private $sessionTimeout = 1800; // 30 minutes en secondes
    
    /**
     * Constructeur avec initialisation de session
     */
    public function __construct(PDO $pdo) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->pdo = $pdo;
    }
    
    /**
     * Inscrit un nouvel utilisateur
     */
    public function register($nom, $email, $password) {
        // Validation des entrées
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new Exception('Format d\'email invalide');
        }
        
        if (strlen($password) < 8) {
            throw new Exception('Le mot de passe doit contenir au moins 8 caractères');
        }
        
        if (empty($nom) || strlen($nom) < 2) {
            throw new Exception('Nom d\'utilisateur invalide');
        }
        
        // Vérifier si l'email existe déjà
        $stmt = $this->pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception('Cet email est déjà utilisé');
        }
        
        // Créer le nouvel utilisateur
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, date_creation) 
                                    VALUES (?, ?, ?, NOW())');
        
        if (!$stmt->execute([$nom, $email, $hashedPassword])) {
            throw new Exception('Erreur lors de la création du compte');
        }
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Authentifie un utilisateur
     */
    public function login($email, $password) {
        // Validation d'email
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new Exception('Format d\'email invalide');
        }
        
        $stmt = $this->pdo->prepare('SELECT id, nom, email, mot_de_passe FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            // Message générique pour éviter la divulgation d'informations
            throw new Exception('Identifiants invalides');
        }
        
        // Régénérer l'ID de session pour prévenir la fixation de session
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['last_activity'] = time();
        
        // Enregistrer l'adresse IP et la date de connexion
        $this->logLogin($user['id']);
        
        return [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'email' => $user['email']
        ];
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public function logout() {
        $_SESSION = array();
        
        // Détruire le cookie de session si utilisé
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        return true;
    }
    
    /**
     * Vérifie si l'utilisateur est authentifié
     */
    public function isAuthenticated() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_activity'])) {
            return false;
        }
        
        // Vérifier si la session n'a pas expiré
        if (time() - $_SESSION['last_activity'] > $this->sessionTimeout) {
            $this->logout();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Obtient les informations de l'utilisateur courant
     */
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        $stmt = $this->pdo->prepare('SELECT id, nom, email FROM utilisateurs WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Enregistre une connexion dans un journal
     */
    private function logLogin($userId) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'inconnu';
        
        $stmt = $this->pdo->prepare('INSERT INTO connexions_log (user_id, ip_address, user_agent, date_connexion) 
                                    VALUES (?, ?, ?, NOW())');
        $stmt->execute([$userId, $ip, $userAgent]);
    }
    
    /**
     * Configure le délai d'expiration de session
     */
    public function setSessionTimeout($seconds) {
        $this->sessionTimeout = $seconds;
    }
}