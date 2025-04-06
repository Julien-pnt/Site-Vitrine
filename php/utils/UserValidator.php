<?php

class UserValidator {
    private $userModel;
    private $userData;
    private $errors = [];
    private $isEditMode;
    
    /**
     * Constructeur
     * @param User $userModel Instance du modèle User
     * @param array $userData Données de l'utilisateur à valider
     * @param bool $isEditMode Mode édition (true) ou création (false)
     */
    public function __construct(User $userModel, array $userData, $isEditMode = false) {
        $this->userModel = $userModel;
        $this->userData = $userData;
        $this->isEditMode = $isEditMode;
    }
    
    /**
     * Valide les données utilisateur
     * @return bool True si les données sont valides, false sinon
     */
    public function validate() {
        // Réinitialiser les erreurs
        $this->errors = [];
        
        // Valider le nom (obligatoire)
        if (empty($this->userData['nom'])) {
            $this->errors['nom'] = "Le nom est obligatoire.";
        } elseif (strlen($this->userData['nom']) > 100) {
            $this->errors['nom'] = "Le nom ne doit pas dépasser 100 caractères.";
        }
        
        // Valider le prénom (optionnel, mais limité en taille)
        if (!empty($this->userData['prenom']) && strlen($this->userData['prenom']) > 100) {
            $this->errors['prenom'] = "Le prénom ne doit pas dépasser 100 caractères.";
        }
        
        // Valider l'email (obligatoire et unique)
        if (empty($this->userData['email'])) {
            $this->errors['email'] = "L'email est obligatoire.";
        } elseif (!filter_var($this->userData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Format d'email invalide.";
        } elseif (strlen($this->userData['email']) > 255) {
            $this->errors['email'] = "L'email ne doit pas dépasser 255 caractères.";
        } else {
            // Vérifier si l'email existe déjà
            $excludeId = $this->isEditMode ? $this->userData['id'] : null;
            if ($this->userModel->emailExists($this->userData['email'], $excludeId)) {
                $this->errors['email'] = "Cet email est déjà utilisé.";
            }
        }
        
        // Valider le téléphone (optionnel, mais format valide)
        if (!empty($this->userData['telephone'])) {
            if (strlen($this->userData['telephone']) > 20) {
                $this->errors['telephone'] = "Le numéro de téléphone est trop long.";
            } elseif (!preg_match('/^[+\d\s.-]+$/', $this->userData['telephone'])) {
                $this->errors['telephone'] = "Format de téléphone invalide.";
            }
        }
        
        // Valider le code postal
        if (!empty($this->userData['code_postal']) && strlen($this->userData['code_postal']) > 10) {
            $this->errors['code_postal'] = "Le code postal ne doit pas dépasser 10 caractères.";
        }
        
        // Valider la ville
        if (!empty($this->userData['ville']) && strlen($this->userData['ville']) > 100) {
            $this->errors['ville'] = "Le nom de la ville ne doit pas dépasser 100 caractères.";
        }
        
        // Valider le pays
        if (!empty($this->userData['pays']) && strlen($this->userData['pays']) > 100) {
            $this->errors['pays'] = "Le nom du pays ne doit pas dépasser 100 caractères.";
        }
        
        // Valider le rôle
        $allowedRoles = ['client', 'manager', 'admin'];
        if (empty($this->userData['role']) || !in_array($this->userData['role'], $allowedRoles)) {
            $this->errors['role'] = "Le rôle sélectionné n'est pas valide.";
        }
        
        // Validation du mot de passe
        // En mode création, le mot de passe est obligatoire
        // En mode édition, le mot de passe est optionnel
        if (!$this->isEditMode || !empty($this->userData['mot_de_passe'])) {
            $this->validatePassword();
        }
        
        // Pas d'erreurs = tout est valide
        return empty($this->errors);
    }
    
    /**
     * Valide le mot de passe
     */
    private function validatePassword() {
        if (empty($this->userData['mot_de_passe'])) {
            $this->errors['mot_de_passe'] = "Le mot de passe est obligatoire.";
            return;
        }
        
        if (strlen($this->userData['mot_de_passe']) < 8) {
            $this->errors['mot_de_passe'] = "Le mot de passe doit contenir au moins 8 caractères.";
        } elseif (!preg_match('/[A-Z]/', $this->userData['mot_de_passe'])) {
            $this->errors['mot_de_passe'] = "Le mot de passe doit contenir au moins une majuscule.";
        } elseif (!preg_match('/[a-z]/', $this->userData['mot_de_passe'])) {
            $this->errors['mot_de_passe'] = "Le mot de passe doit contenir au moins une minuscule.";
        } elseif (!preg_match('/[0-9]/', $this->userData['mot_de_passe'])) {
            $this->errors['mot_de_passe'] = "Le mot de passe doit contenir au moins un chiffre.";
        }
        
        // Vérifier la confirmation
        if (empty($this->userData['confirmer_mot_de_passe'])) {
            $this->errors['confirmer_mot_de_passe'] = "La confirmation du mot de passe est obligatoire.";
        } elseif ($this->userData['mot_de_passe'] !== $this->userData['confirmer_mot_de_passe']) {
            $this->errors['confirmer_mot_de_passe'] = "Les mots de passe ne correspondent pas.";
        }
    }
    
    /**
     * Récupère les erreurs de validation
     * @return array Tableau d'erreurs
     */
    public function getErrors() {
        return $this->errors;
    }
}