# Elixir du Temps - Boutique de Montres de Luxe 🌐⌚

[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/fr/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/fr/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/fr/docs/Web/JavaScript)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![jQuery](https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white)](https://jquery.com/)
[![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://httpd.apache.org/)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

![Site Preview](./public/assets/img/layout/readme-preview.jpg)

## 📋 Table des matières
- [Description du projet](#description-du-projet-)
- [Fonctionnalités principales](#fonctionnalités-principales-)
- [Aperçu](#aperçu-)
- [Technologies utilisées](#technologies-utilisées-)
- [Structure du projet](#structure-du-projet-)
- [Instructions d'installation](#instructions-dinstallation-et-dutilisation-)
- [Configuration](#configuration-)
- [Personnalisation](#personnalisation-)
- [Sécurité](#sécurité-)
- [Documentation API](#documentation-api-)
- [Performance](#performance-)
- [Tests](#tests-)
- [Déploiement](#déploiement-)
- [Contribution](#contribution-)
- [Dépannage](#dépannage-)
- [Feuille de route](#feuille-de-route-)
- [Auteurs](#auteurs-)
- [Licence](#licence-)
- [Remerciements](#remerciements-)

## Description du projet 🏢
"Elixir du Temps" est une boutique en ligne spécialisée dans la vente de montres de luxe et de haute horlogerie. Ce site vitrine présente l'entreprise, son savoir-faire, son histoire et son catalogue de produits exceptionnels. L'interface élégante et intuitive permet aux clients de découvrir et d'explorer les différentes collections de montres, alliant tradition horlogère et technologies modernes.

## Fonctionnalités principales 🚀
- **Page d'accueil immersive** : Présentation visuelle avec animations dynamiques et vidéo d'arrière-plan
- **Catalogue interactif** : Consultation et filtrage des collections avec zoom sur les produits
- **Historique de la marque** : Timeline interactive des étapes clés de l'évolution de l'entreprise
- **Organigramme de l'entreprise** : Structure organisationnelle avec fiches de poste téléchargeables
- **Système d'authentification** : Inscription et connexion sécurisées avec validation multi-facteurs
- **Panier d'achat** : Gestion complète du processus d'achat avec sauvegarde automatique
- **Tableau de bord administratif** : Interface de gestion des produits, commandes et utilisateurs
- **Moteur de recherche avancé** : Recherche par nom, référence, collection ou caractéristiques
- **Galeries de produits** : Visualisation haute définition des montres sous différents angles
- **Système de promotions** : Gestion des codes promo, réductions et offres spéciales
- **Multilinguisme** : Support de plusieurs langues pour une audience internationale
- **Espace client personnalisé** : Suivi des commandes et historique des achats

## Aperçu 📸
![Collection Classic](./public/assets/img/products/collection_classique.JPG)
![Collection Prestige](./public/assets/img/products/collection-prestige.JPG)

## Technologies utilisées 💻
- **Front-end** :
  - HTML5, CSS3 avec Flexbox et Grid
  - JavaScript ES6+ et jQuery
  - Bootstrap 5 pour l'interface responsive
  - AJAX pour les interactions asynchrones
  - GSAP pour les animations avancées

- **Back-end** :
  - PHP 8.2+
  - Architecture MVC
  - API RESTful
  - Système de cache optimisé
  - JWT pour l'authentification API

- **Base de données** :
  - MySQL 8.0
  - Indexation avancée
  - Procédures stockées
  - Transactions sécurisées

- **Sécurité** :
  - Protection CSRF
  - Sanitisation des entrées
  - Cryptage des données sensibles
  - Prévention des injections SQL/XSS
  - Contrôle d'accès basé sur les rôles

- **Outils de développement** :
  - Git pour le versionnement
  - Composer pour la gestion des dépendances
  - PHPUnit pour les tests
  - Docker pour l'environnement de développement
  - CI/CD pour le déploiement automatisé

## Structure du projet 📁

```plaintext
└── julien-pnt-site-vitrine/
   ├── README.md                        # Documentation du projet
   ├── config/                          # Configuration et scripts SQL
   │   ├── admin-login.php              # Page de connexion administrateur initiale
   │   ├── config.php                   # Configuration principale
   │   ├── create-admin.php             # Script pour créer un administrateur
   │   ├── db.sql                       # Script de création de la base de données
   │   └── schema-relationnel.md        # Schéma relationnel de la base de données
   ├── php/                             # Code PHP principal
   │   ├── config/                      # Fichiers de configuration PHP
   │   │   └── database.php             # Configuration de la base de données
   │   ├── models/                      # Modèles de données
   │   │   └── User.php                 # Modèle utilisateur
   │   └── utils/                       # Utilitaires
   │       ├── auth.php                 # Fonctions d'authentification
   │       ├── Logger.php               # Système de logs
   │       ├── UserManager.php          # Gestion des utilisateurs
   │       └── UserValidator.php        # Validation des données utilisateur
   ├── public/                          # Fichiers accessibles publiquement
   │   ├── .htaccess                    # Configuration Apache
   │   ├── admin/                       # Administration
   │   │   ├── activity-logs.php        # Journal d'activité
   │   │   ├── dashboard-widgets.php    # Widgets du tableau de bord
   │   │   ├── export.php               # Export de données
   │   │   ├── index.php                # Accueil admin
   │   │   ├── login.php                # Connexion admin
   │   │   ├── products.php             # Gestion des produits
   │   │   ├── promotions.php           # Gestion des promotions
   │   │   ├── system-logs.php          # Logs système
   │   │   ├── ajax/                    # Requêtes AJAX
   │   │   ├── api/                     # API d'administration
   │   │   ├── css/                     # Styles d'administration
   │   │   ├── includes/                # Inclusions partielles
   │   │   ├── js/                      # Scripts d'administration
   │   │   └── users/                   # Gestion utilisateurs
   │   ├── assets/                      # Ressources statiques
   │   │   ├── css/                     # Styles CSS
   │   │   │   ├── auth.css             # Styles d'authentification
   │   │   │   ├── collections-list.css # Styles des listes de collections
   │   │   │   ├── collections.css      # Styles des collections
   │   │   │   ├── main.css             # Styles principaux
   │   │   │   ├── base/                # Styles de base
   │   │   │   ├── components/          # Composants CSS
   │   │   │   ├── layout/              # Dispositions CSS
   │   │   │   └── utilities/           # Utilitaires CSS
   │   │   ├── Fiches-Postes/           # Fiches de poste
   │   │   ├── img/                     # Images
   │   │   │   ├── avatars/             # Photos de profil
   │   │   │   ├── layout/              # Images de mise en page
   │   │   │   └── products/            # Images de produits
   │   │   ├── js/                      # Scripts JavaScript
   │   │   └── video/                   # Vidéos
   │   ├── pages/                       # Pages HTML
   │   │   ├── Accueil.html             # Page d'accueil
   │   │   ├── APropos.html             # Page À propos
   │   │   ├── Organigramme.html        # Organigramme
   │   │   ├── auth/                    # Pages d'authentification
   │   │   ├── collections/             # Pages des collections
   │   │   ├── legal/                   # Pages légales
   │   │   └── products/                # Pages des produits
   │   ├── php/                         # Scripts PHP publics
   │   │   └── api/                     # API publique
   │   └── uploads/                     # Fichiers téléchargés
   │       └── users/                   # Photos utilisateurs
   └── src/                             # Code source
       └── Services/                    # Services métier
           └── confirmation-commande.php # Service de confirmation
```

## Instructions d'installation et d'utilisation 🛠️

### Prérequis 📝
- Serveur local (XAMPP, WAMP, MAMP, ou LAMP)
- PHP 8.0 ou supérieur
- MySQL 8.0 ou supérieur
- Navigateur web moderne (Chrome, Firefox, Edge, Safari)
- Git (optionnel pour le clonage)
- Composer (pour les dépendances PHP)

### Installation 🏗️
1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-repo/julien-pnt-site-vitrine.git
   cd julien-pnt-site-vitrine
   ```

2. **Installer les dépendances avec Composer**
   ```bash
   composer install
   ```

3. **Configurer le serveur local**
   - Pour **XAMPP** : Placez le dossier dans `C:\xampp\htdocs\`
   - Pour **WAMP** : Placez-le dans `C:\wamp64\www\`
   - Pour **MAMP** : Placez-le dans `/Applications/MAMP/htdocs/`
   - Pour **LAMP** : Placez-le dans `/var/www/html/`

4. **Configurer la base de données**
   - Démarrez votre serveur MySQL et Apache
   - Créez une nouvelle base de données nommée `elixir_du_temps`
   - Importez le fichier `config/db.sql` dans votre base de données
   ```bash
   mysql -u username -p elixir_du_temps < config/db.sql
   ```

5. **Configurer l'environnement**
   - Copiez `.env.example` vers `.env` et configurez vos paramètres
   ```bash
   cp .env.example .env
   # Éditez .env avec vos paramètres de connexion à la base de données
   ```

6. **Créer un compte administrateur**
   - Accédez à `http://localhost/julien-pnt-site-vitrine/config/create-admin.php`
   - Suivez les instructions pour créer votre premier compte administrateur
   - **Important** : Supprimez ou protégez ce fichier après utilisation

7. **Lancer le site**
   - Ouvrez votre navigateur
   - Accédez à `http://localhost/julien-pnt-site-vitrine/public/pages/Accueil.html`

## Configuration 🔧

### Configuration de base de données
Modifiez le fichier `php/config/database.php` pour adapter les paramètres de connexion à votre environnement :

```php
// Exemple de configuration
$config = [
    'host' => 'localhost',
    'dbname' => 'elixir_du_temps',
    'username' => 'votre_utilisateur',
    'password' => 'votre_mot_de_passe',
    'charset' => 'utf8mb4'
];
```

### Variables d'environnement
Créez un fichier `.env` à la racine du projet avec les variables suivantes :

```env
# Configuration de la base de données
DB_HOST=localhost
DB_NAME=elixir_du_temps
DB_USER=root
DB_PASS=password
DB_PORT=3306

# Configuration du site
SITE_URL=http://localhost/julien-pnt-site-vitrine
ADMIN_EMAIL=admin@example.com

# Clés de sécurité
SECRET_KEY=votre_cle_secrete_pour_jwt
ENCRYPTION_KEY=votre_cle_de_cryptage
```

## Sécurité 🔒

Le site intègre plusieurs couches de sécurité :

### Protection des dossiers sensibles
Des fichiers `.htaccess` sont placés dans les répertoires critiques pour restreindre l'accès :

- **Racine** : Redirige toutes les requêtes vers le dossier public
- **Config** : Bloque tout accès direct aux fichiers de configuration
- **PHP** : Empêche l'exécution directe des scripts métier
- **Uploads** : Désactive l'exécution des scripts dans ce répertoire
- **Admin** : Requiert une authentification pour l'accès

### Prévention des attaques courantes
- **XSS** : Échappement automatique des sorties
- **CSRF** : Jetons de validation sur tous les formulaires
- **SQLi** : Requêtes préparées avec paramètres liés
- **Brute Force** : Limitation du nombre de tentatives de connexion
- **Session Hijacking** : Régénération des identifiants de session

### Audit de sécurité
Un système de logs enregistre toutes les actions sensibles :
- Connexions/déconnexions
- Modifications de données critiques
- Tentatives d'accès non autorisées
- Exports de données

## Documentation API 📚

### Authentification
```
POST /public/php/api/auth/login.php
```
Corps de la requête :
```json
{
  "email": "utilisateur@exemple.com",
  "password": "mot_de_passe_sécurisé"
}
```

Réponse :
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "role": "client"
  }
}
```

### Catalogue de produits
```
GET /public/php/api/products/catalogue.php?collection=classic&page=1&limit=10
```

Réponse :
```json
{
  "products": [
    {
      "id": 101,
      "name": "Élégance Éternelle",
      "price": 8950.00,
      "image": "/assets/img/products/elegance-eternal.jpg",
      "collection": "Classic"
    },
    // autres produits
  ],
  "pagination": {
    "current": 1,
    "total_pages": 4,
    "total_items": 36
  }
}
```

## Performance ⚡

Le site est optimisé pour des performances maximales :

- **Cache navigateur** : Configuration d'en-têtes expires pour les ressources statiques
- **Compression GZIP** : Réduction de la taille des fichiers transmis
- **Minification** : CSS et JavaScript optimisés pour la production
- **Images optimisées** : Format WebP avec fallback JPG/PNG
- **Lazy loading** : Chargement différé des images hors écran
- **Requêtes SQL optimisées** : Indexation et requêtes efficaces

## Tests 🧪

Le projet inclut des tests automatisés :

- **Tests unitaires** avec PHPUnit pour les classes métier
- **Tests d'intégration** pour les API et services
- **Tests fonctionnels** pour les flux utilisateurs

Pour exécuter les tests :
```bash
./vendor/bin/phpunit
```

## Déploiement 🚀

### Environnement de production
1. Configurez un serveur web avec PHP 8.0+ et MySQL 8.0+
2. Activez les modules Apache nécessaires : rewrite, headers, ssl
3. Configurez un certificat SSL (Let's Encrypt recommandé)
4. Clonez le dépôt et suivez les étapes d'installation
5. Ajustez le fichier `.env` pour l'environnement de production
6. Exécutez l'optimisation pour la production :
   ```bash
   php build/optimize.php
   ```

### Automatisation CI/CD
Le projet peut être intégré avec GitHub Actions ou GitLab CI pour :
- Tests automatiques à chaque commit
- Déploiement automatique en staging/production
- Analyse de qualité du code

## Contribution 🤝

Les contributions sont les bienvenues ! Pour contribuer :

1. **Forkez** le projet
2. **Créez** une branche pour votre fonctionnalité (`git checkout -b feature/nouvelle-fonctionnalite`)
3. **Committez** vos changements (`git commit -m 'Ajout: nouvelle fonctionnalité'`)
4. **Poussez** vers votre branche (`git push origin feature/nouvelle-fonctionnalite`)
5. **Ouvrez** une Pull Request

**Standards de codage :**
- PSR-12 pour PHP
- BEM pour CSS
- ESLint avec la config Airbnb pour JavaScript

## Feuille de route 🗺️

- [ ] Intégration de paiements en ligne (Stripe, PayPal)
- [ ] Application mobile (React Native)
- [ ] Système de recommandations personnalisées
- [ ] Support client via chat en direct
- [ ] Intégration avec des ERP/CRM
- [ ] Système de fidélité avec points et récompenses
- [ ] Galeries produits en réalité augmentée

## Auteurs ✍️
- **Julien Pnt** - Développeur principal - [GitHub](https://github.com/username)
- **Équipe projet** - La SIO1 - Promotion 2025

## Licence 📜
Ce projet est sous licence MIT. Consultez le fichier `LICENSE` pour plus d'informations.

## Remerciements 🙏
- [Bootstrap](https://getbootstrap.com/) - Framework CSS
- [Font Awesome](https://fontawesome.com/) - Icônes
- [Unsplash](https://unsplash.com/) - Images libres de droits
- Tous les contributeurs qui ont participé à ce projet

---
© 2025 Elixir du Temps | Tous droits réservés
