<?php
require_once __DIR__ . '/Logger.php';

class UserManager {
    private $pdo;
    private $userModel;
    private $logger;
    
    /**
     * Constructeur
     * @param PDO $pdo Instance de PDO pour la connexion à la base de données
     * @param User $userModel Instance du modèle User
     */
    public function __construct(PDO $pdo, User $userModel) {
        $this->pdo = $pdo;
        $this->userModel = $userModel;
        $this->logger = new Logger($pdo);
    }
    
    /**
     * Crée un nouvel utilisateur
     * @param array $userData Données de l'utilisateur
     * @return int|false ID du nouvel utilisateur ou false en cas d'erreur
     */
    public function createUser(array $userData) {
        try {
            $this->pdo->beginTransaction();
            
            // Hash du mot de passe
            $hashedPassword = password_hash($userData['mot_de_passe'], PASSWORD_DEFAULT);
            
            // Préparation de la requête
            $stmt = $this->pdo->prepare("
                INSERT INTO utilisateurs (
                    nom, prenom, email, mot_de_passe, telephone, adresse,
                    code_postal, ville, pays, role, actif
                ) VALUES (
                    :nom, :prenom, :email, :mot_de_passe, :telephone, :adresse,
                    :code_postal, :ville, :pays, :role, :actif
                )
            ");
            
            // Exécution de la requête
            $result = $stmt->execute([
                ':nom' => $userData['nom'],
                ':prenom' => $userData['prenom'],
                ':email' => $userData['email'],
                ':mot_de_passe' => $hashedPassword,
                ':telephone' => $userData['telephone'] ?? null,
                ':adresse' => $userData['adresse'] ?? null,
                ':code_postal' => $userData['code_postal'] ?? null,
                ':ville' => $userData['ville'] ?? null,
                ':pays' => $userData['pays'] ?? 'France',
                ':role' => $userData['role'],
                ':actif' => $userData['actif'] ?? 1
            ]);
            
            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }
            
            // Récupérer l'ID du nouvel utilisateur
            $userId = $this->pdo->lastInsertId();
            
            // Journaliser l'action
            $this->logger->info('utilisateur', 'create', [
                'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
                'entity_type' => 'utilisateur',
                'entity_id' => $userId,
                'details' => "Création de l'utilisateur {$userData['prenom']} {$userData['nom']} ({$userData['email']})",
                'after_state' => $this->sanitizeUserData($userData)
            ]);
            
            $this->pdo->commit();
            return $userId;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            // Log de l'erreur
            $this->logger->error('utilisateur', 'create_error', [
                'details' => "Erreur lors de la création d'un utilisateur: " . $e->getMessage(),
                'context' => ['userData' => $this->sanitizeUserData($userData)]
            ]);
            return false;
        }
    }
    
    /**
     * Met à jour un utilisateur existant
     * @param array $userData Données de l'utilisateur à mettre à jour
     * @return bool True en cas de succès, false sinon
     */
    public function updateUser(array $userData) {
        try {
            $this->pdo->beginTransaction();
            
            // Construire la requête SQL en fonction des champs à mettre à jour
            $fields = [
                'nom = :nom',
                'prenom = :prenom',
                'email = :email',
                'telephone = :telephone',
                'adresse = :adresse',
                'code_postal = :code_postal',
                'ville = :ville',
                'pays = :pays',
                'role = :role',
                'actif = :actif'
            ];
            
            // Paramètres pour l'exécution
            $params = [
                ':id' => $userData['id'],
                ':nom' => $userData['nom'],
                ':prenom' => $userData['prenom'],
                ':email' => $userData['email'],
                ':telephone' => $userData['telephone'] ?? null,
                ':adresse' => $userData['adresse'] ?? null,
                ':code_postal' => $userData['code_postal'] ?? null,
                ':ville' => $userData['ville'] ?? null,
                ':pays' => $userData['pays'] ?? 'France',
                ':role' => $userData['role'],
                ':actif' => $userData['actif'] ?? 0
            ];
            
            // Si un nouveau mot de passe est fourni, l'ajouter à la requête
            if (!empty($userData['mot_de_passe'])) {
                $fields[] = 'mot_de_passe = :mot_de_passe';
                $params[':mot_de_passe'] = password_hash($userData['mot_de_passe'], PASSWORD_DEFAULT);
            }
            
            // Construction de la requête complète
            $sql = "UPDATE utilisateurs SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            
            // Exécution
            $result = $stmt->execute($params);
            
            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }
            
            // Récupérer les données mises à jour
            $newUserData = $this->userModel->getById($userData['id']);
            
            // Journaliser l'action
            $this->logger->logChanges(
                'utilisateur',
                $userData['id'],
                $this->sanitizeUserData($userData),
                $this->sanitizeUserData($newUserData),
                'update',
                "Mise à jour de l'utilisateur {$newUserData['prenom']} {$newUserData['nom']} (ID: {$userData['id']})"
            );
            
            $this->pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            // Log de l'erreur
            $this->logger->error('utilisateur', 'update_error', [
                'details' => "Erreur lors de la mise à jour de l'utilisateur (ID: {$userData['id']}): " . $e->getMessage(),
                'entity_type' => 'utilisateur',
                'entity_id' => $userData['id'],
                'context' => ['userData' => $this->sanitizeUserData($userData)]
            ]);
            return false;
        }
    }
    
    /**
     * Supprime un utilisateur
     * @param int $userId ID de l'utilisateur à supprimer
     * @return bool True si la suppression a réussi, false sinon
     */
    public function deleteUser($userId) {
        try {
            // Récupérer les données de l'utilisateur avant suppression
            $userData = $this->userModel->getById($userId);
            
            if (!$userData) {
                return false;
            }
            
            $this->pdo->beginTransaction();
            
            // Suppression de l'utilisateur
            $stmt = $this->pdo->prepare("DELETE FROM utilisateurs WHERE id = :id");
            $result = $stmt->execute([':id' => $userId]);
            
            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }
            
            // Journaliser l'action
            $this->logger->info('utilisateur', 'delete', [
                'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
                'entity_type' => 'utilisateur',
                'entity_id' => $userId,
                'details' => "Suppression de l'utilisateur {$userData['prenom']} {$userData['nom']} (ID: {$userId})",
                'before_state' => $this->sanitizeUserData($userData)
            ]);
            
            $this->pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            // Log de l'erreur
            $this->logger->error('utilisateur', 'delete_error', [
                'details' => "Erreur lors de la suppression de l'utilisateur (ID: {$userId}): " . $e->getMessage(),
                'entity_type' => 'utilisateur',
                'entity_id' => $userId
            ]);
            return false;
        }
    }
    
    /**
     * Active ou désactive un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param bool $active Nouvel état (actif=true, inactif=false)
     * @return bool True en cas de succès, false sinon
     */
    public function toggleUserStatus($userId, $active) {
        try {
            $this->pdo->beginTransaction();
            
            // Récupérer les données actuelles avant la mise à jour
            $oldUserData = $this->userModel->getById($userId);
            
            if (!$oldUserData) {
                return false;
            }
            
            // Mise à jour du statut
            $stmt = $this->pdo->prepare("UPDATE utilisateurs SET actif = :actif WHERE id = :id");
            $result = $stmt->execute([
                ':id' => $userId,
                ':actif' => $active ? 1 : 0
            ]);
            
            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }
            
            // Récupérer les données mises à jour
            $newUserData = $this->userModel->getById($userId);
            
            // Journaliser l'action
            $this->logger->logChanges(
                'utilisateur',
                $userId,
                $this->sanitizeUserData($oldUserData),
                $this->sanitizeUserData($newUserData),
                'status_update',
                "Modification du statut de l'utilisateur {$oldUserData['prenom']} {$oldUserData['nom']} (ID: {$userId}) à " . 
                ($active ? "actif" : "inactif")
            );
            
            $this->pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            // Log de l'erreur
            $this->logger->error('utilisateur', 'status_update_error', [
                'details' => "Erreur lors de la mise à jour du statut de l'utilisateur (ID: {$userId}): " . $e->getMessage(),
                'entity_type' => 'utilisateur',
                'entity_id' => $userId
            ]);
            return false;
        }
    }
    
    /**
     * Nettoyer les données utilisateur pour le logging (retirer le mot de passe)
     */
    private function sanitizeUserData($userData) {
        if (is_array($userData)) {
            unset($userData['mot_de_passe']);
        }
        return $userData;
    }
}
?>