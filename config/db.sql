-- --------------------------------------------------------
-- Base de données Elixir du Temps
-- Schéma SQL optimisé pour le site e-commerce
-- --------------------------------------------------------

-- Suppression de la base de données si elle existe déjà
DROP DATABASE IF EXISTS elixir_du_temps;

-- Création de la base de données avec encodage UTF-8
CREATE DATABASE elixir_du_temps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE elixir_du_temps;

-- --------------------------------------------------------
-- TABLES DE GESTION DES UTILISATEURS
-- --------------------------------------------------------

-- Table `utilisateurs`
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    code_postal VARCHAR(10),
    ville VARCHAR(100),
    pays VARCHAR(100) DEFAULT 'France',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME ON UPDATE CURRENT_TIMESTAMP,
    role ENUM('client', 'admin', 'manager') DEFAULT 'client',
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Table pour les jetons d'authentification "Se souvenir de moi"
CREATE TABLE auth_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expiration (expires_at)
) ENGINE=InnoDB;

-- Table pour journaliser les connexions
CREATE TABLE connexions_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255),
    action ENUM('login', 'logout', 'failed_attempt') DEFAULT 'login',
    date_connexion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_date (date_connexion)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- TABLES DE GESTION DU CATALOGUE
-- --------------------------------------------------------

-- Table des catégories de produits
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    parent_id INT NULL,
    position INT DEFAULT 0,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- Table des collections (séries spéciales, éditions limitées, etc.)
CREATE TABLE collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    date_debut DATE,
    date_fin DATE,
    active BOOLEAN DEFAULT TRUE,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- Table `produits` améliorée
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    reference VARCHAR(50) UNIQUE,
    description TEXT,
    description_courte VARCHAR(500),
    prix DECIMAL(10, 2) NOT NULL,
    prix_promo DECIMAL(10, 2) NULL,
    image VARCHAR(255),
    images_supplementaires TEXT,
    stock INT NOT NULL DEFAULT 0,
    stock_alerte INT DEFAULT 5,
    poids DECIMAL(8, 3) NULL,
    categorie_id INT,
    collection_id INT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME ON UPDATE CURRENT_TIMESTAMP,
    visible BOOLEAN DEFAULT TRUE,
    featured BOOLEAN DEFAULT FALSE,
    nouveaute BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE SET NULL,
    INDEX idx_reference (reference),
    INDEX idx_slug (slug),
    INDEX idx_prix (prix),
    INDEX idx_categorie (categorie_id),
    INDEX idx_collection (collection_id),
    INDEX idx_visible_featured (visible, featured)
) ENGINE=InnoDB;

-- Table des attributs de produits (matériaux, diamètres, etc.)
CREATE TABLE attributs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    type ENUM('texte', 'nombre', 'booleen', 'liste') NOT NULL DEFAULT 'texte'
) ENGINE=InnoDB;

-- Table de liaison produits-attributs
CREATE TABLE produit_attributs (
    produit_id INT NOT NULL,
    attribut_id INT NOT NULL,
    valeur TEXT NOT NULL,
    PRIMARY KEY (produit_id, attribut_id),
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
    FOREIGN KEY (attribut_id) REFERENCES attributs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- TABLES DE GESTION DES COMMANDES
-- --------------------------------------------------------

-- Table `commandes` améliorée
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(50) NOT NULL UNIQUE,
    utilisateur_id INT NOT NULL,
    statut ENUM('en_attente', 'payee', 'en_preparation', 'expediee', 'livree', 'annulee', 'remboursee') DEFAULT 'en_attente',
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME ON UPDATE CURRENT_TIMESTAMP,
    total_produits DECIMAL(10, 2) NOT NULL,
    frais_livraison DECIMAL(10, 2) DEFAULT 0.00,
    total_taxe DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    mode_paiement VARCHAR(50),
    adresse_livraison TEXT NOT NULL,
    adresse_facturation TEXT NOT NULL,
    notes TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON UPDATE CASCADE,
    INDEX idx_reference (reference),
    INDEX idx_statut (statut),
    INDEX idx_date (date_commande)
) ENGINE=InnoDB;

-- Table `articles_commande` améliorée
CREATE TABLE articles_commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    nom_produit VARCHAR(255) NOT NULL, -- Stocké pour historique si le produit change
    reference_produit VARCHAR(50), -- Stocké pour historique
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    prix_total DECIMAL(10, 2) NOT NULL,
    taux_taxe DECIMAL(5, 2) DEFAULT 20.00,
    montant_taxe DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE NO ACTION,
    INDEX idx_commande (commande_id)
) ENGINE=InnoDB;

-- Table `panier` améliorée
CREATE TABLE panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    session_id VARCHAR(255), -- Pour les visiteurs non-authentifiés
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
    UNIQUE KEY unique_panier_item (utilisateur_id, session_id, produit_id),
    INDEX idx_session (session_id)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- TABLES DE GESTION DU SITE
-- --------------------------------------------------------

-- Table pour les avis clients
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'approuve', 'rejete') DEFAULT 'en_attente',
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_produit (produit_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- Table pour les pages de contenu statique
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    contenu TEXT NOT NULL,
    meta_description VARCHAR(255),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME ON UPDATE CURRENT_TIMESTAMP,
    publiee BOOLEAN DEFAULT TRUE,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- DONNÉES INITIALES
-- --------------------------------------------------------

-- Création d'un compte administrateur par défaut (mot de passe: Admin123!)
INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES 
('Admin', 'admin@elixirdutemps.com', '$2y$10$lYyWKfIXIlxuSTkznIX7sOyGUDq7zQ1PMIL8zRpK2O5eijvwwZ.wm', 'admin');

-- Création de quelques catégories de base
INSERT INTO categories (nom, slug, description) VALUES 
('Montres classiques', 'montres-classiques', 'Montres élégantes et intemporelles'),
('Montres sport', 'montres-sport', 'Montres robustes pour les activités sportives'),
('Montres connectées', 'montres-connectees', 'Montres intelligentes avec fonctionnalités numériques'),
('Montres de luxe', 'montres-luxe', 'Montres haut de gamme pour les connaisseurs');

-- Création d'une collection de base
INSERT INTO collections (nom, slug, description, active) VALUES
('Édition limitée 2024', 'edition-limitee-2024', 'Notre collection exclusive en édition limitée pour l\'année 2024', TRUE),
('Collection Vintage', 'collection-vintage', 'Retrouvez le charme des montres d\'antan avec notre collection vintage', TRUE);