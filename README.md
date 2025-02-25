# Site-Vitrine 🌐

## Description du projet 🏢
Ce projet est un site vitrine pour une entreprise spécialisée dans la vente de montres. Il vise à présenter l'activité de l'entreprise, son histoire, son catalogue de produits et son organigramme.

## Fonctionnalités principales 🚀
- **Page d'accueil** : Présente le nom de l'entreprise, son logo et son slogan.
- **Présentation de l'activité** : Fournit une description détaillée des produits et services proposés.
- **Historique** : Met en avant les étapes clés de l'évolution de l'entreprise.
- **Catalogue des produits** : Permet aux visiteurs de consulter différentes collections de montres.
- **Organigramme** : Affiche la structure de l'entreprise et propose des fiches de poste téléchargeables.
- **Gestion des utilisateurs** : Système d'authentification permettant aux utilisateurs de se connecter et de s'inscrire.
- **Gestion du panier** : Fonctionnalité permettant aux utilisateurs d'ajouter des produits à un panier virtuel.

---

## Structure du projet 📁

```plaintext
└── julien-pnt-site-vitrine/
    ├── README.md
    ├── Fiche Poste/                     # Contient les fiches de poste des employés
    ├── SQL/                              # Scripts SQL pour la gestion de la base de données
    │   ├── db.sql                        # Script de création de la base de données
    │   ├── panier.sql                     # Script SQL pour la gestion du panier
    │   └── schema-relationnel.md         # Schéma relationnel de la base de données
    ├── css/                              # Contient les fichiers de style CSS
    │   └── Styles.css                     # Fichier principal de styles
    ├── html/                             # Contient toutes les pages HTML du site
    │   ├── APropos.html                   # Page "À propos"
    │   ├── Acceuil.html                   # Page d'accueil
    │   ├── Collection-Classic.html        # Page Collection Classique
    │   ├── Collection-Limited-Edition.html # Page Collection Édition Limitée
    │   ├── Collection-Prestige.html       # Page Collection Prestige
    │   ├── Collection-Sport.html          # Page Collection Sport
    │   ├── Collections.html               # Page listant toutes les collections
    │   ├── DescriptionProduits.html       # Détails des produits
    │   ├── Montres.html                   # Vue d'ensemble des montres
    │   ├── Organigramme.html              # Page de l'organigramme de l'entreprise
    │   ├── PrivacyPolicy.html             # Politique de confidentialité
    │   ├── login.html                     # Page de connexion
    │   └── register.html                  # Page d'inscription
    ├── img/                              # Contient les images utilisées sur le site
    │   ├── collection-prestige.JPG
    │   └── collection_classique.JPG
    ├── js/                               # Contient les scripts JavaScript
    │   ├── Montres.js                     # Gestion de l'affichage des montres
    │   ├── Panier.js                      # Gestion du panier
    │   ├── auth.js                        # Gestion de l'authentification
    │   └── login.js                       # Script de gestion de la connexion
    ├── php/                              # Scripts PHP pour la gestion des fonctionnalités dynamiques
    │   ├── AuthService.php                # Service d'authentification
    │   ├── check.php                      # Vérification des informations utilisateur
    │   ├── db.php                         # Connexion à la base de données
    │   ├── login.php                      # Script de connexion utilisateur
    │   ├── logout.php                     # Script de déconnexion
    │   ├── panier.php                     # Gestion du panier côté serveur
    │   └── userCreation.php               # Gestion de l'inscription des utilisateurs
    └── video/                            # Dossier destiné aux fichiers vidéos
```

---

## Instructions d'installation et d'utilisation 🛠️

### Prérequis 📝
- Un serveur local (XAMPP, WAMP, MAMP, ou LAMP)
- Un navigateur web
- MySQL pour la gestion de la base de données

### Installation 🏗️
1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-repo/julien-pnt-site-vitrine.git
   ```
2. **Placer les fichiers sur un serveur local**
   - Si vous utilisez XAMPP, placez le dossier `julien-pnt-site-vitrine` dans `htdocs`.
   - Si vous utilisez WAMP, placez-le dans `www`.
3. **Importer la base de données**
   - Ouvrez phpMyAdmin.
   - Créez une nouvelle base de données (ex: `site_vitrine`).
   - Importez le fichier `db.sql` et `panier.sql` depuis le dossier `SQL/`.
4. **Lancer le site**
   - Ouvrez `http://localhost/julien-pnt-site-vitrine/html/Acceuil.html` dans votre navigateur.

### Personnalisation 🎨
- **Modifier le style** : Éditez le fichier `css/Styles.css`.
- **Ajouter ou modifier du contenu HTML** : Modifiez les fichiers dans `html/`.
- **Ajouter des fonctionnalités dynamiques** : Éditez les scripts dans `js/` et `php/`.

---

## Améliorations futures 📌
- Ajouter un système de gestion des commandes.
- Intégrer un espace administrateur pour gérer le catalogue.
- Améliorer l'expérience utilisateur avec des animations et interactions JavaScript.
- Optimiser le SEO pour un meilleur référencement.

---

## Auteurs ✍️
- **Julien Pnt** - Développeur principal
- **Équipe projet** - La SIO1

---

## Licence 📜
Ce projet est sous licence MIT. Vous êtes libre de le modifier et de le distribuer sous les conditions de cette licence.
