<?php
require_once 'db.php';

// Exception personnalisée pour la création d'utilisateur
class UserCreationException extends Exception {}

// Configuration des contraintes de validation
const PASSWORD_MIN_LENGTH = 8;
const PASSWORD_MAX_LENGTH = 72; // Limite de bcrypt
const NAME_MIN_LENGTH = 2;
const NAME_MAX_LENGTH = 50;

// Classe de validation des entrées utilisateur
class UserValidator {
    // Valide le nom
    public static function validateName($nom) {
        $nom = trim($nom);
        if (empty($nom)) {
            throw new UserCreationException('Le nom est requis');
        }
        if (strlen($nom) < NAME_MIN_LENGTH || strlen($nom) > NAME_MAX_LENGTH) {
            throw new UserCreationException("Le nom doit contenir entre " . NAME_MIN_LENGTH . " et " . NAME_MAX_LENGTH . " caractères");
        }
        if (!preg_match('/^[\p{L}\s-]+$/u', $nom)) {
            throw new UserCreationException('Le nom contient des caractères non autorisés');
        }
        return $nom;
    }

    // Valide l'email
    public static function validateEmail($email) {
        $email = trim(strtolower($email));
        if (empty($email)) {
            throw new UserCreationException('L\'email est requis');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UserCreationException('Format d\'email invalide');
        }
        return $email;
    }

    // Valide le mot de passe
    public static function validatePassword($password) {
        if (empty($password)) {
            throw new UserCreationException('Le mot de passe est requis');
        }
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            throw new UserCreationException('Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères');
        }
        if (strlen($password) > PASSWORD_MAX_LENGTH) {
            throw new UserCreationException('Le mot de passe est trop long');
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
            throw new UserCreationException('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');
        }
        return $password;
    }
}

// Classe de création d'utilisateur
class UserCreator {
    private $pdo;

    // Constructeur avec injection de dépendance PDO
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Vérifie si l'email existe déjà dans la base de données
    public function emailExists($email) {
        $stmt = $this->pdo->prepare('SELECT 1 FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetchColumn() !== false;
    }

    // Crée un nouvel utilisateur
    public function createUser($nom, $email, $password) {
        try {
            // Validation des données
            $nom = UserValidator::validateName($nom);
            $email = UserValidator::validateEmail($email);
            $password = UserValidator::validatePassword($password);

            // Vérifier si l'email existe déjà
            if ($this->emailExists($email)) {
                throw new UserCreationException('Cet email est déjà utilisé');
            }

            // Démarrer une transaction
            $this->pdo->beginTransaction();

            // Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

            // Insérer l'utilisateur dans la base de données
            $stmt = $this->pdo->prepare('
                INSERT INTO utilisateurs (nom, email, mot_de_passe, date_creation)
                VALUES (?, ?, ?, NOW())
            ');
            $stmt->execute([$nom, $email, $hashedPassword]);
            $userId = $this->pdo->lastInsertId();

            // Créer les entrées associées (préférences, profil, etc.)
            $this->createUserProfile($userId);

            // Valider la transaction
            $this->pdo->commit();
            return $userId;

        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $this->pdo->rollBack();
            error_log("Erreur création utilisateur : " . $e->getMessage());
            throw new UserCreationException('Erreur lors de la création du compte');
        }
    }

    // Crée le profil utilisateur associé
    private function createUserProfile($userId) {
        $stmt = $this->pdo->prepare('
            INSERT INTO profils_utilisateur (utilisateur_id, est_actif)
            VALUES (?, true)
        ');
        $stmt->execute([$userId]);
    }
}

// Gestionnaire de la création d'utilisateur
function handleUserCreation($data) {
    global $pdo;

    try {
        $userCreator = new UserCreator($pdo);
        $userId = $userCreator->createUser(
            $data['nom'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? ''
        );

        sendJsonResponse(true, 'Compte créé avec succès', ['user_id' => $userId]);
    } catch (UserCreationException $e) {
        sendJsonResponse(false, $e->getMessage());
    } catch (Exception $e) {
        error_log("Erreur inattendue : " . $e->getMessage());
        sendJsonResponse(false, 'Une erreur est survenue lors de la création du compte');
    }
}

// Fonction utilitaire pour les réponses JSON
function sendJsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(
        ['success' => $success, 'message' => $message],
        $data
    ));
    exit;
}

// Point d'entrée de l'API
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(false, 'Données JSON invalides');
    }

    handleUserCreation($data);
} else {
    http_response_code(405);
    sendJsonResponse(false, 'Méthode non autorisée');
}