# Site-Vitrine 🌐

## Description du projet 🏢
Ce projet est un site vitrine pour une entreprise spécialisée dans la vente de montres. Il a pour objectif de présenter l'activité de l'entreprise, son histoire, son catalogue de produits et son organigramme.

## Fonctionnalités principales 🚀
- **Page d'accueil** : Présentation du nom de l'entreprise, son logo et son slogan.
- **Présentation de l'activité** : Description détaillée des produits et services proposés.
- **Historique** : Mise en avant des étapes clés de l'évolution de l'entreprise.
- **Catalogue des produits** : Consultation des différentes collections de montres.
- **Organigramme** : Affichage de la structure de l'entreprise avec des fiches de poste téléchargeables.
- **Gestion des utilisateurs** : Système d'authentification pour la connexion et l'inscription des utilisateurs.
- **Gestion du panier** : Fonctionnalité permettant d'ajouter des produits à un panier virtuel.

---

## Structure du projet 📁

```plaintext
└── julien-pnt-site-vitrine/
   ├── README.md
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
   │   │   │       ├── Montres.js       # Gestion de l'affichage des montres
   │   │   │       ├── Panier.js        # Gestion du panier
   │   │   │       ├── auth.js          # Gestion de l'authentification
   │   │   │       └── login.js         # Script de gestion de la connexion
   │   │   └── video/                   # Fichiers vidéos
   │   ├── pages/                       # Pages HTML du site
   │   │   ├── APropos.html             # Page "À propos"
   │   │   ├── Acceuil.html             # Page d'accueil
   │   │   ├── auth/                    # Pages liées à l'authentification
   │   │   │   ├── login.html           # Page de connexion
   │   │   │   └── register.html        # Page d'inscription
   │   │   ├── collections/             # Pages des collections
   │   │   │   ├── Collection-Classic.html
   │   │   │   ├── Collection-Limited-Edition.html
   │   │   │   ├── Collection-Prestige.html
   │   │   │   ├── Collection-Sport.html
   │   │   │   └── Collections.html     # Page listant toutes les collections
   │   │   ├── legal/                   # Pages légales
   │   │   │   └── PrivacyPolicy.html   # Politique de confidentialité
   │   │   └── products/                # Pages des produits
   │   │       ├── DescriptionProduits.html # Détails des produits
   │   │       └── Montres.html         # Vue d'ensemble des montres
   │   └── php/                         # Scripts PHP pour les fonctionnalités dynamiques
   │       └── api/                     # API PHP
   │           ├── auth/                # API pour l'authentification
   │           │   ├── AuthService.php  # Service d'authentification
   │           │   ├── check.php        # Vérification des informations utilisateur
   │           │   ├── login.php        # Script de connexion utilisateur
   │           │   ├── logout.php       # Script de déconnexion
   │           │   └── userCreation.php # Gestion de l'inscription des utilisateurs
   │           └── products/            # API pour les produits
   │               ├── comparer.php     # Comparaison de produits
   │               ├── my-cart.php      # Gestion du panier utilisateur
   │               └── panier.php       # Gestion du panier côté serveur
   └── src/                             # Code source côté serveur
       └── Services/
           └── confirmation-commande.php # Service de confirmation de commande
```

---

## Instructions d'installation et d'utilisation 🛠️

### Prérequis 📝
- Serveur local (XAMPP, WAMP, MAMP, ou LAMP)
- Navigateur web
- MySQL pour la gestion de la base de données

### Installation 🏗️
1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-repo/julien-pnt-site-vitrine.git
   ```
2. **Placer les fichiers sur un serveur local**
   - Avec XAMPP : placez le dossier `julien-pnt-site-vitrine` dans `htdocs`.
   - Avec WAMP : placez-le dans `www`.
3. **Importer la base de données**
   - Ouvrez phpMyAdmin.
   - Créez une nouvelle base de données (ex: `site_vitrine`).
   - Importez les fichiers `db.sql` depuis le dossier `config/`.
4. **Lancer le site**
   - Accédez à `http://localhost/julien-pnt-site-vitrine/public/pages/Acceuil.html` dans votre navigateur.

### Personnalisation 🎨
- **Modifier le style** : Éditez `public/assets/css/Styles.css`.
- **Modifier le contenu HTML** : Modifiez les fichiers dans `public/pages/`.
- **Ajouter des fonctionnalités dynamiques** : Travaillez sur les scripts dans `public/assets/js/modules/` et `public/php/api/`.

---

## Améliorations futures 📌
- Intégrer un système de gestion des commandes.
- Ajouter un espace administrateur pour gérer le catalogue.
- Améliorer l'expérience utilisateur avec des animations et interactions JavaScript.
- Optimiser le SEO pour un meilleur référencement.

---

## Auteurs ✍️
- **Julien Pnt** - Développeur principal
- **Équipe projet** - La SIO1

---

## Licence 📜
Ce projet est sous licence MIT. Vous êtes libre de le modifier et de le distribuer sous les conditions de cette licence.
