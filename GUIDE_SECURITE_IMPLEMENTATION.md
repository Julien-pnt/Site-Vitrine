# Guide d'Implémentation des Corrections de Sécurité

## 🚀 Mise en Place Immédiate

### 1. Remplacement de la Configuration

**Remplacer** le fichier `config/config.php` par `config/config_secure.php` :

```bash
# Sauvegarder l'ancien fichier
mv config/config.php config/config_old.php

# Utiliser la nouvelle configuration
mv config/config_secure.php config/config.php
```

### 2. Intégration du Middleware de Sécurité

**Dans chaque fichier PHP public**, ajouter au début :

```php
<?php
require_once '../php/utils/SecurityMiddleware.php';
// SecurityMiddleware::init() est appelé automatiquement
```

**Pour les API**, ajouter la validation CSRF :

```php
// Dans vos formulaires HTML
<input type="hidden" name="csrf_token" value="<?= SecurityMiddleware::generateCSRFToken() ?>">

// Dans vos requêtes AJAX
$.ajaxSetup({
    beforeSend: function(xhr) {
        xhr.setRequestHeader('X-CSRF-TOKEN', '<?= SecurityMiddleware::generateCSRFToken() ?>');
    }
});
```

### 3. Sécurisation des Uploads

**Remplacer les validations d'upload existantes** :

```php
// Ancien code
if (!in_array($extension, $allowed_extensions)) {
    $errors[] = "Format non autorisé";
}

// Nouveau code sécurisé
$validation = SecurityMiddleware::validateUpload($_FILES['image']);
if (!$validation['valid']) {
    $errors[] = $validation['error'];
}
```

### 4. Mise en Place de l'Audit

**Ajouter l'audit dans les actions critiques** :

```php
// Initialisation
$audit = new SecurityAudit($pdo);

// Dans les actions sensibles
$audit->logEvent('USER_LOGIN', 'MEDIUM', ['user_id' => $userId]);
$audit->logEvent('ADMIN_ACCESS', 'HIGH', ['action' => 'user_delete']);
$audit->logEvent('FAILED_LOGIN', 'MEDIUM', ['email' => $email]);
```

## 🔧 Corrections Spécifiques par Fichier

### php/utils/auth.php

**Ajouter la régénération de session** :

```php
function createUserSession($user) {
    ensureSessionStarted();
    
    // Régénérer l'ID de session pour éviter la fixation
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in_time'] = time();
    $_SESSION['last_activity'] = time();
}
```

### Tous les fichiers de login

**Ajouter la protection contre le brute force** :

```php
// Avant la vérification du mot de passe
$maxAttempts = 5;
$timeWindow = 300; // 5 minutes
$ip = $_SERVER['REMOTE_ADDR'];

// Vérifier les tentatives précédentes
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM security_audit 
    WHERE event_type = 'LOGIN_FAILED' 
    AND ip_address = ? 
    AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
");
$stmt->execute([$ip, $timeWindow]);
$attempts = $stmt->fetchColumn();

if ($attempts >= $maxAttempts) {
    $audit->logEvent('BRUTEFORCE_BLOCKED', 'HIGH', ['ip' => $ip]);
    http_response_code(429);
    die('Trop de tentatives. Réessayez dans 5 minutes.');
}
```

### Tous les formulaires

**Ajouter la protection CSRF** :

```html
<!-- Dans tous les formulaires -->
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= SecurityMiddleware::generateCSRFToken() ?>">
    <!-- autres champs -->
</form>
```

## 🛠️ Variables d'Environnement

**Créer un fichier `.env`** à la racine :

```env
# Base de données
DB_HOST=localhost
DB_NAME=elixir_du_temps
DB_USER=your_db_user
DB_PASSWORD=your_secure_password

# Sécurité
ENCRYPTION_KEY=your-very-secure-encryption-key-32-chars
JWT_SECRET=your-jwt-secret-key-change-this-value

# Email (pour les alertes)
ADMIN_EMAIL=admin@yoursite.com
SMTP_HOST=smtp.yourprovider.com
SMTP_USER=your_smtp_user
SMTP_PASS=your_smtp_password
```

## 📊 Dashboard de Sécurité

**Créer une page de monitoring** `public/admin/security-dashboard.php` :

```php
<?php
require_once '../../php/utils/SecurityAudit.php';

$audit = new SecurityAudit($pdo);

// Analyser les tentatives suspectes
$suspiciousIPs = $audit->analyzeLoginAttempts();

// Générer un rapport
$report = $audit->generateSecurityReport(7);

// Nettoyer les anciens logs (à faire périodiquement)
// $audit->cleanupOldLogs(90);
?>

<div class="security-dashboard">
    <h1>Dashboard Sécurité</h1>
    
    <div class="alert-panel">
        <h2>Alertes Récentes</h2>
        <?php foreach ($suspiciousIPs as $ip): ?>
            <div class="alert alert-warning">
                IP suspecte: <?= $ip['ip_address'] ?> 
                (<?= $ip['attempts'] ?> tentatives)
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="stats-panel">
        <h2>Statistiques (7 derniers jours)</h2>
        <?php foreach ($report['summary']['by_severity'] as $severity => $count): ?>
            <div class="stat-item">
                <span class="severity-<?= strtolower($severity) ?>"><?= $severity ?></span>: 
                <?= $count ?> événements
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

## 🔄 Tâches de Maintenance

**Script de maintenance quotidienne** `scripts/daily_security.php` :

```php
#!/usr/bin/env php
<?php
require_once '../config/config.php';
require_once '../php/config/database.php';
require_once '../php/utils/SecurityAudit.php';

$audit = new SecurityAudit($pdo);

// Analyser les tentatives de connexion
$suspiciousIPs = $audit->analyzeLoginAttempts();

// Nettoyer les anciens logs (garde 90 jours)
$cleaned = $audit->cleanupOldLogs(90);

// Générer un rapport quotidien
$report = $audit->generateSecurityReport(1);

// Envoyer par email si nécessaire
if (!empty($suspiciousIPs) || $report['summary']['by_severity']['CRITICAL'] > 0) {
    // TODO: Envoyer alerte email
}

echo "Maintenance sécurité terminée.\n";
echo "IPs suspectes détectées: " . count($suspiciousIPs) . "\n";
echo "Logs nettoyés: $cleaned entrées\n";
```

## ⚠️ Points d'Attention

1. **Tester** toutes les fonctionnalités après l'implémentation
2. **Sauvegarder** la base de données avant les modifications
3. **Configurer** les variables d'environnement selon votre setup
4. **Monitorer** les logs de sécurité régulièrement
5. **Mettre à jour** les mots de passe par défaut

## 🚨 Actions Urgentes (À faire MAINTENANT)

1. ✅ Créer les fichiers `.htaccess` (déjà fait)
2. ✅ Remplacer `config.php` par la version sécurisée
3. ⚠️ Changer tous les mots de passe par défaut
4. ⚠️ Activer HTTPS en production
5. ⚠️ Configurer la sauvegarde automatique
6. ⚠️ Tester la récupération après incident

Ce guide vous permettra de sécuriser rapidement votre site. Suivez l'ordre des étapes pour une implémentation sans problème.
