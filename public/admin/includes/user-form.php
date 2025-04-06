<?php
// Récupérer les valeurs pour pré-remplir le formulaire (si édition)
$nom = $user['nom'] ?? '';
$prenom = $user['prenom'] ?? '';
$email = $user['email'] ?? '';
$telephone = $user['telephone'] ?? '';
$role = $user['role'] ?? 'client';
$statut = isset($user['statut']) ? $user['statut'] : 1;
?>

<form method="post" action="" class="user-form" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <div class="form-row">
        <div class="form-group">
            <label for="prenom">Prénom <span class="required">*</span></label>
            <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($prenom) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="nom">Nom <span class="required">*</span></label>
            <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($nom) ?>" required>
        </div>
    </div>
    
    <div class="form-group">
        <label for="email">Adresse email <span class="required">*</span></label>
        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
    </div>
    
    <div class="form-group">
        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($telephone) ?>">
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="password">Mot de passe <?= isset($user) ? '' : '<span class="required">*</span>' ?></label>
            <div class="password-field">
                <input type="password" id="password" name="password" class="form-control" <?= isset($user) ? '' : 'required' ?>>
                <button type="button" class="toggle-password" title="Afficher/Masquer le mot de passe">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <?php if(isset($user)): ?>
                <small class="text-muted">Laissez vide pour ne pas changer le mot de passe</small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe <?= isset($user) ? '' : '<span class="required">*</span>' ?></label>
            <div class="password-field">
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" <?= isset($user) ? '' : 'required' ?>>
                <button type="button" class="toggle-password" title="Afficher/Masquer le mot de passe">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="role">Rôle <span class="required">*</span></label>
            <select id="role" name="role" class="form-control" required>
                <option value="client" <?= $role === 'client' ? 'selected' : '' ?>>Client</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                <option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>Gestionnaire</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="statut">Statut <span class="required">*</span></label>
            <select id="statut" name="statut" class="form-control" required>
                <option value="1" <?= $statut == 1 ? 'selected' : '' ?>>Actif</option>
                <option value="0" <?= $statut == 0 ? 'selected' : '' ?>>Inactif</option>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="photo">Photo de profil</label>
        <input type="file" id="photo" name="photo" class="form-control-file" accept="image/*">
        <?php if(isset($user['photo']) && $user['photo']): ?>
            <div class="current-photo">
                <img src="/Site-Vitrine/public/uploads/users/<?= htmlspecialchars($user['photo']) ?>" alt="Photo de profil" width="100">
                <div class="photo-actions">
                    <label>
                        <input type="checkbox" name="delete_photo" value="1"> 
                        Supprimer la photo
                    </label>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" name="save_user">
            <i class="fas fa-save"></i> Enregistrer
        </button>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // Change icon
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });
});
</script>