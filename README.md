# Site-Vitrine - Boutique de Montres 🌐⌚

[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/fr/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/fr/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/fr/docs/Web/JavaScript)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

## 📋 Table des matières
- [Description du projet](#description-du-projet-)
- [Fonctionnalités principales](#fonctionnalités-principales-)
- [Technologies utilisées](#technologies-utilisées-)
- [Structure du projet](#structure-du-projet-)
- [Instructions d'installation](#instructions-dinstallation-et-dutilisation-)
- [Personnalisation](#personnalisation-)
- [Dépannage](#dépannage-)
- [Auteurs](#auteurs-)
- [Licence](#licence-)

## Description du projet 🏢
Ce projet est un site vitrine pour une entreprise spécialisée dans la vente de montres de luxe. Il présente l'activité de l'entreprise, son histoire, son catalogue de produits et son organigramme. L'interface élégante et intuitive permet aux clients de découvrir et d'explorer les différentes collections de montres.

## Fonctionnalités principales 🚀
- **Page d'accueil** : Présentation du nom de l'entreprise, son logo et son slogan avec animations dynamiques
- **Présentation de l'activité** : Description détaillée des produits et services proposés
- **Historique** : Timeline interactive des étapes clés de l'évolution de l'entreprise
- **Catalogue des produits** : Consultation et filtrage des différentes collections de montres
- **Organigramme** : Affichage de la structure de l'entreprise avec des fiches de poste téléchargeables
- **Gestion des utilisateurs** : Système d'authentification sécurisé pour la connexion et l'inscription
- **Gestion du panier** : Fonctionnalité permettant d'ajouter, modifier et supprimer des produits du panier

## Technologies utilisées 💻
- **Front-end** : HTML5, CSS3, JavaScript (ES6+)
- **Back-end** : PHP 8.0+
- **Base de données** : MySQL 8.0
- **Serveur** : Apache 2.4+
- **Autres outils** : Git pour le versionnement, PHPMyAdmin pour la gestion de base de données

## Structure du projet 📁

```plaintext
└── julien-pnt-site-vitrine/
   ├── README.md                        # Documentation du projet
   ├── config/                          # Configuration et scripts SQL
   │   ├── db.sql                       # Script de création de la base de données
   │   └── schema-relationnel.md        # Schéma relationnel de la base de données
   ├── public/                          # Fichiers accessibles publiquement
   │   ├── .htaccess                    # Fichier de configuration Apache
   │   ├── assets/                      # Ressources statiques
   │   │   ├── Fiches-Postes/           # Fiches de poste des employés
   │   │   ├── css/                     # Fichiers de style CSS
   │   │   │   └── Styles.css           # Fichier principal de styles
   │   │   ├── img/                     # Images utilisées sur le site
   │   │   │   ├── layout/              # Images liées à la mise en page
   │   │   │   └── products/            # Images des produits
   │   │   │       ├── collection-prestige.JPG
   │   │   │       └── collection_classique.JPG
   │   │   ├── js/                      # Scripts JavaScript
   │   │   │   └── modules/             # Modules JavaScript
   │   │   │       ├── Montres.js       # Gestion des montres et affichage
   │   │   │       ├── Panier.js        # Fonctionnalités du panier d'achat
   │   │   │       ├── auth.js          # Gestion de l'authentification
   │   │   │       └── login.js         # Processus de connexion
   │   │   └── video/                   # Fichiers vidéos
   │   ├── pages/                       # Pages HTML du site
   │   │   ├── APropos.html             # Page "À propos"
   │   │   ├── Acceuil.html             # Page d'accueil (note: faute d'orthographe, devrait être "Accueil")
   │   │   ├── auth/                    # Pages d'authentification
   │   │   │   ├── login.html           # Page de connexion
   │   │   │   └── register.html        # Page d'inscription
   │   │   ├── collections/             # Pages des collections
   │   │   │   ├── Collection-Classic.html      # Collection classique
   │   │   │   ├── Collection-Limited-Edition.html  # Collection édition limitée
   │   │   │   ├── Collection-Prestige.html     # Collection prestige
   │   │   │   ├── Collection-Sport.html        # Collection sport
   │   │   │   └── Collections.html             # Vue globale des collections
   │   │   ├── legal/                   # Pages légales
   │   │   │   └── PrivacyPolicy.html   # Politique de confidentialité
   │   │   └── products/                # Pages des produits
   │   │       ├── DescriptionProduits.html  # Description détaillée des produits
   │   │       └── Montres.html         # Catalogue de montres
   │   └── php/                         # Scripts PHP
   │       └── api/                     # API PHP
   │           ├── auth/                # API pour l'authentification
   │           │   ├── AuthService.php  # Service d'authentification
   │           │   ├── check.php        # Vérification des sessions
   │           │   ├── login.php        # Traitement de la connexion
   │           │   ├── logout.php       # Déconnexion utilisateur
   │           │   └── userCreation.php # Création de comptes utilisateur
   │           └── products/            # API pour les produits
   │               ├── comparer.php     # Comparaison de produits
   │               ├── my-cart.php      # Affichage du panier personnel
   │               └── panier.php       # Gestion du panier
   └── src/                             # Code source côté serveur
       └── Services/                    # Services métier
           └── confirmation-commande.php # Traitement des confirmations de commande
```

## Instructions d'installation et d'utilisation 🛠️

### Prérequis 📝
- Serveur local (XAMPP, WAMP, MAMP, ou LAMP)
- PHP 8.0 ou supérieur
- MySQL 8.0 ou supérieur
- Navigateur web moderne (Chrome, Firefox, Edge, Safari)
- Git (optionnel pour le clonage)

### Installation 🏗️
1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-repo/julien-pnt-site-vitrine.git
   ```
   Ou téléchargez l'archive ZIP depuis GitHub et décompressez-la.

2. **Configurer le serveur local**
   - **XAMPP** : Placez le dossier `julien-pnt-site-vitrine` dans `C:\xampp\htdocs\`
   - **WAMP** : Placez-le dans `C:\wamp64\www\`
   - **MAMP** : Placez-le dans `/Applications/MAMP/htdocs/`
   - **LAMP** : Placez-le dans `/var/www/html/`

3. **Configurer la base de données**
   - Démarrez votre serveur MySQL et Apache
   - Ouvrez phpMyAdmin (généralement à l'adresse http://localhost/phpmyadmin)
   - Créez une nouvelle base de données nommée `site_vitrine`
   - Importez le fichier `config/db.sql` dans votre base de données

4. **Vérifier les configurations**
   - Assurez-vous que le module PHP `mysqli` est activé
   - Vérifiez que le fichier `.htaccess` est correctement pris en compte par Apache

5. **Lancer le site**
   - Ouvrez votre navigateur
   - Accédez à `http://localhost/julien-pnt-site-vitrine/public/pages/Accueil.html`

### Personnalisation 🎨
- **Apparence** : Modifiez `public/assets/css/Styles.css` pour personnaliser l'apparence
- **Contenu** : Éditez les fichiers HTML dans `public/pages/` pour changer le contenu textuel
- **Logique** : Travaillez sur les modules JS dans `public/assets/js/modules/` pour modifier les comportements
- **Backend** : Adaptez les scripts PHP dans `public/php/api/` selon vos besoins

## Dépannage 🔧
- **Problème d'accès** : Vérifiez les permissions des fichiers et dossiers
- **Erreurs PHP** : Activez l'affichage des erreurs dans php.ini pendant le développement
- **Problèmes de base de données** : Vérifiez les identifiants de connexion dans les scripts

## Auteurs ✍️
- **Julien Pnt** - Développeur principal - [profil GitHub](https://github.com/username)
- **Équipe projet** - La SIO1 - Promotion 2025

## Licence 📜
Ce projet est sous licence MIT. Consultez le fichier `LICENSE` pour plus d'informations.

---
© 2025 | Tous droits réservés
