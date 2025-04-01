<?php
class AuthService {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function register($nom, $email, $password) {
        // Vérifier si l'email existe déjà
        $stmt = $this->pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception('Cet email est déjà utilisé');
        }
        
        // Créer le nouvel utilisateur
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)');
        
        if (!$stmt->execute([$nom, $email, $hashedPassword])) {
            throw new Exception('Erreur lors de la création du compte');
        }
        
        return $this->pdo->lastInsertId();
    }
    
    public function login($email, $password) {
        $stmt = $this->pdo->prepare('SELECT id, nom, email, mot_de_passe FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            throw new Exception('Identifiants invalides');
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['last_activity'] = time();
        
        return [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'email' => $user['email']
        ];
    }
    
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    public function isAuthenticated() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_activity'])) {
            return false;
        }
        
        // Vérifier si la session n'a pas expiré (30 minutes)
        if (time() - $_SESSION['last_activity'] > 1800) {
            $this->logout();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        $stmt = $this->pdo->prepare('SELECT id, nom, email FROM utilisateurs WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
}