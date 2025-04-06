<?php
class User {
    private $pdo;
    
    /**
     * Constructeur
     * @param PDO $pdo Instance de PDO pour la connexion à la base de données
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Récupère un utilisateur par son ID
     * @param int $id ID de l'utilisateur
     * @return array|false Données de l'utilisateur ou false si non trouvé
     */
    public function getUserById($id) {
        // Modifié pour n'inclure que les colonnes existantes
        $query = "SELECT id, nom, prenom, email, telephone, role, actif AS statut, 
                     date_creation
              FROM utilisateurs WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Récupère un utilisateur par son ID
     * Alias de la méthode getUserById pour compatibilité
     * 
     * @param int $id ID de l'utilisateur
     * @return array|false Données de l'utilisateur ou false si non trouvé
     */
    public function getById($id) {
        return $this->getUserById($id);
    }
    
    /**
     * Récupère un utilisateur par email
     */
    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Vérifie si un email existe déjà (hors utilisateur actuel en cas de modification)
     * @param string $email Email à vérifier
     * @param int|null $excludeId ID de l'utilisateur à exclure de la vérification
     * @return bool True si l'email existe déjà, false sinon
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Récupère tous les utilisateurs avec pagination et filtres
     * @param array $filters Critères de filtrage
     * @param int $limit Nombre max d'utilisateurs à récupérer
     * @param int $offset Position de départ
     * @param string $sortField Champ pour le tri
     * @param string $sortOrder Ordre de tri (ASC ou DESC)
     * @return array Liste des utilisateurs
     */
    public function getUsers($filters = [], $limit = 20, $offset = 0, $sortField = 'date_creation', $sortOrder = 'DESC') {
        $whereConditions = [];
        $params = [];
        
        // Construction des conditions WHERE basées sur les filtres
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($key === 'search') {
                    $whereConditions[] = "(nom LIKE :search OR prenom LIKE :search OR email LIKE :search)";
                    $params[':search'] = "%$value%";
                } else {
                    $whereConditions[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
        }
        
        // Construction de la clause WHERE complète
        $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : '';
        
        // Sécuriser le tri
        $allowedSortFields = ['id', 'nom', 'prenom', 'email', 'role', 'actif', 'date_creation', 'derniere_connexion'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'date_creation';
        }
        
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        // Exécution de la requête
        $sql = "SELECT * FROM utilisateurs $whereClause ORDER BY $sortField $sortOrder LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        
        // Liaison des paramètres pour LIMIT et OFFSET
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        // Liaison des autres paramètres
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Compte le nombre total d'utilisateurs selon les filtres
     * @param array $filters Critères de filtrage
     * @return int Nombre d'utilisateurs
     */
    public function countUsers($filters = []) {
        $whereConditions = [];
        $params = [];
        
        // Construction des conditions WHERE basées sur les filtres
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($key === 'search') {
                    $whereConditions[] = "(nom LIKE :search OR prenom LIKE :search OR email LIKE :search)";
                    $params[':search'] = "%$value%";
                } else {
                    $whereConditions[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
        }
        
        // Construction de la clause WHERE complète
        $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : '';
        
        // Exécution de la requête
        $sql = "SELECT COUNT(*) FROM utilisateurs $whereClause";
        $stmt = $this->pdo->prepare($sql);
        
        // Liaison des paramètres
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Crée un nouvel utilisateur
     */
    public function create($userData) {
        $query = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, adresse, code_postal, ville, pays, role, actif) 
                 VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :adresse, :code_postal, :ville, :pays, :role, :actif)";
        
        $stmt = $this->pdo->prepare($query);
        
        // Hash du mot de passe
        $userData['mot_de_passe'] = password_hash($userData['mot_de_passe'], PASSWORD_DEFAULT);
        
        $result = $stmt->execute([
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'email' => $userData['email'],
            'mot_de_passe' => $userData['mot_de_passe'],
            'telephone' => $userData['telephone'] ?? null,
            'adresse' => $userData['adresse'] ?? null,
            'code_postal' => $userData['code_postal'] ?? null,
            'ville' => $userData['ville'] ?? null,
            'pays' => $userData['pays'] ?? 'France',
            'role' => $userData['role'] ?? 'client',
            'actif' => isset($userData['actif']) ? $userData['actif'] : 1
        ]);
        
        if ($result) {
            return $this->pdo->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update($id, $userData) {
        $setFields = [];
        $params = [];
        
        // Création dynamique des champs à mettre à jour
        foreach ($userData as $field => $value) {
            if ($field !== 'id' && $field !== 'mot_de_passe') {
                $setFields[] = "$field = :$field";
                $params[$field] = $value;
            }
        }
        
        // Gestion spéciale du mot de passe (uniquement s'il est fourni)
        if (!empty($userData['mot_de_passe'])) {
            $setFields[] = "mot_de_passe = :mot_de_passe";
            $params['mot_de_passe'] = password_hash($userData['mot_de_passe'], PASSWORD_DEFAULT);
        }
        
        // Ajout de l'ID pour la condition WHERE
        $params['id'] = $id;
        
        $query = "UPDATE utilisateurs SET " . implode(", ", $setFields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute($params);
    }
    
    /**
     * Supprime un utilisateur
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Met à jour le statut d'un utilisateur (actif/inactif)
     */
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE utilisateurs SET actif = ? WHERE id = ?");
        return $stmt->execute([$status ? 1 : 0, $id]);
    }
    
    /**
     * Met à jour la date de dernière connexion
     */
    public function updateLastLogin($id) {
        $stmt = $this->pdo->prepare("UPDATE utilisateurs SET derniere_connexion = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Récupère tous les rôles disponibles
     */
    public function getRoles() {
        $stmt = $this->pdo->query("SELECT DISTINCT role FROM utilisateurs ORDER BY role");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Récupère tous les utilisateurs avec pagination et filtres
     * Alias de la méthode getUsers pour compatibilité
     * 
     * @param int $offset Position de départ
     * @param int $limit Nombre max d'utilisateurs à récupérer
     * @param array $filters Critères de filtrage
     * @param string $sortField Champ pour le tri
     * @param string $sortOrder Ordre de tri (ASC ou DESC)
     * @return array Liste des utilisateurs
     */
    public function getAll($offset, $limit, $filters = [], $sortField = 'date_creation', $sortOrder = 'DESC') {
        return $this->getUsers($filters, $limit, $offset, $sortField, $sortOrder);
    }

    /**
     * Compte le nombre total d'utilisateurs selon les filtres
     * Alias de la méthode countUsers pour compatibilité
     * 
     * @param array $filters Critères de filtrage
     * @return int Nombre d'utilisateurs
     */
    public function countAll($filters = []) {
        return $this->countUsers($filters);
    }
}
?>