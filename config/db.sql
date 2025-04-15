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

-- Ajouter les colonnes manquantes à la table utilisateurs
ALTER TABLE utilisateurs 
ADD COLUMN photo VARCHAR(255) NULL AFTER actif,
ADD COLUMN derniere_connexion DATETIME NULL AFTER date_modification;

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

-- Table pour les réinitialisations de mot de passe
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expiration (expires_at)
) ENGINE=InnoDB;

-- Table des promotions avec les noms de colonnes correspondant au code
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    type VARCHAR(20) NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    min_purchase DECIMAL(10,2) DEFAULT 0,
    start_date DATETIME NULL,
    end_date DATETIME NULL,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    products VARCHAR(255) NULL,
    collections VARCHAR(255) NULL,
    active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de liaison pour les promotions applicables à certains produits
CREATE TABLE IF NOT EXISTS promotion_produits (
    promotion_id INT NOT NULL,
    produit_id INT NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (promotion_id, produit_id),
    CONSTRAINT fk_promotion_produit_promotion FOREIGN KEY (promotion_id) REFERENCES promotions (id) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_promotion_produit_produit FOREIGN KEY (produit_id) REFERENCES produits (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table des logs d'administration
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    date_action DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    details TEXT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_date (date_action)
) ENGINE=InnoDB;

CREATE TABLE `system_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` enum('debug','info','notice','warning','error','critical','alert','emergency') NOT NULL DEFAULT 'info',
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('admin','customer','system','guest') NOT NULL DEFAULT 'system',
  `category` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `before_state` json DEFAULT NULL,
  `after_state` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `context` json DEFAULT NULL,
  `http_method` varchar(10) DEFAULT NULL,
  `request_url` varchar(255) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `execution_time` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_level` (`level`),
  KEY `idx_category` (`category`),
  KEY `idx_action` (`action`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_user_type` (`user_type`),
  KEY `idx_entity` (`entity_type`, `entity_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Insertion des catégories de produits (à placer AVANT les insertions de produits)
INSERT INTO categories (id, nom, slug, description, image, position) VALUES
(1, 'Montres Homme', 'montres-homme', 'Notre collection exclusive de montres pour homme combine élégance intemporelle et précision technique exceptionnelle.', '/assets/img/categories/montres-homme.jpg', 10),
(2, 'Montres Sport', 'montres-sport', 'Des montres robustes conçues pour résister aux conditions les plus extrêmes tout en conservant un style incomparable.', '/assets/img/categories/montres-sport.jpg', 20),
(3, 'Montres Connectées', 'montres-connectees', 'L\'alliance parfaite entre tradition horlogère et technologie de pointe pour les amateurs d\'horlogerie contemporaine.', '/assets/img/categories/montres-connectees.jpg', 30),
(4, 'Haute Horlogerie', 'haute-horlogerie', 'Des pièces d\'exception qui incarnent le summum de l\'art horloger avec des complications et matériaux prestigieux.', '/assets/img/categories/haute-horlogerie.jpg', 40),
(5, 'Montres Femme', 'montres-femme', 'Une sélection raffinée de montres féminines alliant élégance, précision et matériaux précieux.', '/assets/img/categories/montres-femme.jpg', 50);

-- Insertion des collections (à placer AVANT les insertions de produits)
INSERT INTO collections (id, nom, slug, description, image, date_debut, active)
VALUES 
(1, 'Collection Classic', 'collection-classic', 'L\'élégance intemporelle dans sa forme la plus pure. Notre collection Classic rend hommage aux traditions horlogères avec une touche contemporaine.', '/assets/img/collections/classic-collection.jpg', '2019-05-15', TRUE),
(2, 'Collection Sport', 'collection-sport', 'Des montres conçues pour l\'action et l\'aventure. La Collection Sport allie robustesse, précision et élégance dynamique.', '/assets/img/collections/sport-collection.jpg', '2020-03-10', TRUE),
(3, 'Collection Prestige', 'collection-prestige', 'L\'apogée de notre savoir-faire horloger. La Collection Prestige met en valeur des matériaux d\'exception et des finitions exquises.', '/assets/img/collections/prestige-collection.jpg', '2021-06-28', TRUE),
(4, 'Collection Limited Edition', 'collection-limited-edition', 'Des créations exclusives produites en série limitée et numérotée. Chaque montre de cette collection est un chef-d\'œuvre d\'horlogerie.', '/assets/img/collections/limited-edition-collection.jpg', '2022-11-30', TRUE);


-- Insertion des produits de la Collection Classic - Montres Homme
INSERT INTO produits (id, nom, slug, reference, description_courte, description, prix, image, images_supplementaires, stock, stock_alerte, poids, categorie_id, collection_id, visible, featured, nouveaute) VALUES
(101, 'Élégance Éternelle', 'elegance-eternelle', 'ELX-CL-001', 'Montre automatique en or rose 18k avec cadran guilloché et mouvement manufacture', 'L\'Élégance Éternelle incarne l\'apogée de l\'horlogerie classique avec son boîtier en or rose 18k et son cadran guilloché à la main. Son mouvement manufacture offre une réserve de marche exceptionnelle de 72 heures et une précision chronométrique certifiée. La finition méticuleuse de chaque composant, des ponts décorés à la main aux vis polies en bleu, témoigne du savoir-faire inégalé de nos artisans horlogers.', 8950.00, '/assets/img/products/elegance-eternal.jpg', '/assets/img/products/elegance-eternal-detail1.jpg,/assets/img/products/elegance-eternal-detail2.jpg', 15, 4, 0.125, 1, 1, TRUE, TRUE, FALSE),

(102, 'Tradition Classique', 'tradition-classique', 'ELX-CL-002', 'Montre à remontage manuel en acier inoxydable avec finitions polies et satinées', 'La Tradition Classique allie élégance et savoir-faire horloger avec son boîtier en acier inoxydable 316L aux finitions alternées polies et satinées. Son cadran argenté soleil et ses index appliqués créent un effet de profondeur saisissant. Le mouvement à remontage manuel est visible à travers le fond saphir et offre une autonomie de 56 heures. Un hommage aux grands classiques de l\'horlogerie avec une touche contemporaine.', 4750.00, '/assets/img/products/tradition-classique.jpg', NULL, 24, 5, 0.110, 1, 1, TRUE, FALSE, FALSE),

(103, 'Raffinement Or', 'raffinement-or', 'ELX-CL-003', 'Montre squelette en or jaune 18k avec mouvement apparent et réserve de marche', 'Chef-d\'œuvre de transparence, cette montre squelette en or jaune 18k dévoile les rouages de son mouvement manufacture apparent. Ses 274 composants sont assemblés à la main par nos maîtres horlogers. La cage squelettée permet d\'admirer les engrenages en action, tandis que l\'indicateur de réserve de marche de 72 heures rappelle sa complexité technique remarquable. Un véritable témoignage d\'excellence horlogère.', 12800.00, '/assets/img/products/raffinement-or.jpg', '/assets/img/products/raffinement-or-detail1.jpg', 8, 3, 0.135, 4, 1, TRUE, TRUE, FALSE),

(104, 'Heritage Prestige', 'heritage-prestige', 'ELX-CL-004', 'Montre chronographe en acier avec lunette en céramique et bracelet en alligator', 'L\'Heritage Prestige revisite les codes classiques de l\'horlogerie avec une sensibilité contemporaine. Son chronographe à roue à colonnes, considéré comme l\'une des complications les plus nobles, offre une précision irréprochable. La lunette en céramique high-tech contraste élégamment avec le cadran laqué blanc cassé et le bracelet en alligator cousu main. Un garde-temps qui transcende les époques.', 7500.00, '/assets/img/products/heritage-prestige.jpg', NULL, 18, 5, 0.142, 1, 1, TRUE, FALSE, FALSE);

-- Insertion des produits de la Collection Classic - Montres Femme
INSERT INTO produits (id, nom, slug, reference, description_courte, description, prix, image, images_supplementaires, stock, stock_alerte, poids, categorie_id, collection_id, visible, featured, nouveaute) VALUES
(201, 'Élégance Rose', 'elegance-rose', 'ELX-CL-F01', 'Montre en or rose 18k sertie de 60 diamants et bracelet en cuir d\'alligator', 'Cette montre féminine allie délicatesse et précision avec son boîtier en or rose 18k serti de 60 diamants sur la lunette (0.75 carats au total). Son cadran en nacre rosée est sublimé par des index appliqués en or rose et son bracelet en cuir d\'alligator complète son esthétique raffinée. Le mouvement automatique suisse offre une fiabilité sans compromis et une réserve de marche de 50 heures.', 13500.00, '/assets/img/products/elegance-rose.jpg', '/assets/img/products/elegance-rose-detail1.jpg', 12, 3, 0.095, 5, 1, TRUE, TRUE, FALSE),

(202, 'Pureté Divine', 'purete-divine', 'ELX-CL-F02', 'Montre automatique avec boîtier en acier et nacre, sertie de 8 diamants', 'Avec son design minimaliste et élégant, cette montre automatique pour femme présente un boîtier en acier poli de 29mm. Son cadran en nacre blanche est orné de 8 diamants en guise d\'index et ses aiguilles bleuies apportent une touche de sophistication. Le bracelet en alligator blanc complète parfaitement ce garde-temps d\'exception, aussi précis qu\'élégant.', 3950.00, '/assets/img/products/purete-divine.jpg', NULL, 20, 5, 0.082, 5, 1, TRUE, FALSE, TRUE),

(203, 'Clarté Étoilée', 'clarte-etoilee', 'ELX-CL-F03', 'Montre en or blanc 18k avec cadran serti de diamants et phases de lune', 'La Clarté Étoilée capture l\'essence du firmament avec son cadran en aventurine serti de 87 diamants taille brillant. Son boîtier en or blanc 18k (36mm) abrite un mouvement automatique avec indication des phases de lune d\'une précision astronomique. Un chef-d\'œuvre de joaillerie horlogère qui ne nécessitera qu\'un ajustement de la lune tous les 122 ans.', 16400.00, '/assets/img/products/clarte-etoilee.jpg', '/assets/img/products/clarte-etoilee-detail1.jpg,/assets/img/products/clarte-etoilee-detail2.jpg', 5, 2, 0.118, 5, 1, TRUE, FALSE, FALSE),

(204, 'Élégance Saphir', 'elegance-saphir', 'ELX-CL-F04', 'Montre en acier avec lunette sertie de saphirs et cadran bleu profond', 'L\'Élégance Saphir se distingue par sa lunette sertie de 36 saphirs bleus taillés à la main, encerclant un cadran bleu profond aux reflets soleil. Son boîtier en acier de haute qualité et son bracelet intégré offrent une ergonomie parfaite. Le mouvement automatique visible par le fond transparent révèle une mécanique aussi belle que précise.', 5650.00, '/assets/img/products/elegance-saphir.jpg', NULL, 15, 4, 0.105, 5, 1, TRUE, FALSE, TRUE);

-- Montre Chronos Édition Limitée
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(301, 'Chronos Édition Limitée', 'chronos-edition-limitee', 'ELX-LE-001', 'Le Chronos Édition Limitée représente l\'excellence horlogère dans sa forme la plus pure. Produit en seulement 100 exemplaires numérotés, ce garde-temps exceptionnel allie tradition et innovation avec un boîtier en platine 950 et un cadran en émail grand feu réalisé à la main. Son mouvement manufacture à remontage manuel offre une réserve de marche de 80 heures et présente des finitions exceptionnelles visibles à travers le fond saphir. Chaque pièce est signée et numérotée par notre maître horloger.', 'Montre en platine 950 avec cadran en émail grand feu et mouvement manufacture exclusif. Édition limitée à 100 exemplaires numérotés.', 22000.00, NULL, '/assets/img/products/chronos-edition-limitee.jpg', '/assets/img/products/chronos-edition-limitee-detail1.jpg,/assets/img/products/chronos-edition-limitee-detail2.jpg', 35, 5, 0.145, 1, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Prestige Unique
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(302, 'Prestige Unique', 'prestige-unique', 'ELX-LE-002', 'Le Prestige Unique incarne l\'exclusivité avec son boîtier en or blanc 18k et son cadran en météorite de Muonionalusta, d\'origine extraterrestre. Chaque pièce présente des motifs cristallins uniques, rendant chaque montre véritablement unique. Limitée à 50 exemplaires, cette création exceptionnelle abrite notre calibre squelette à complication phase de lune astronomique. Le bracelet en alligator bleu nuit est cousu main par nos artisans maroquiniers.', 'Montre en or blanc 18k avec cadran en météorite et phase de lune astronomique. Édition limitée à 50 exemplaires.', 24500.00, NULL, '/assets/img/products/prestige-unique.jpg', '/assets/img/products/prestige-unique-detail1.jpg', 20, 3, 0.155, 1, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Héritage Exclusif
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(303, 'Héritage Exclusif', 'heritage-exclusif', 'ELX-LE-003', 'L\'Héritage Exclusif rend hommage aux techniques horlogères ancestrales avec un mouvement tourbillon entièrement fabriqué et décoré à la main. Son boîtier en or rose 18k abrite un cadran en aventurine qui capture la magie d\'un ciel étoilé. Limitée à 25 exemplaires, chaque pièce nécessite plus de 500 heures de travail minutieux. Le tourbillon volant visible à 6 heures témoigne de la maîtrise technique incomparable de nos horlogers.', 'Montre tourbillon en or rose 18k avec cadran en aventurine. Édition limitée à 25 exemplaires fabriqués entièrement à la main.', 23500.00, NULL, '/assets/img/products/heritage-exclusif.jpg', '/assets/img/products/heritage-exclusif-detail1.jpg,/assets/img/products/heritage-exclusif-detail2.jpg', 15, 3, 0.160, 1, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Souveraineté Singulière
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(304, 'Souveraineté Singulière', 'souverainete-singuliere', 'ELX-LE-004', 'La Souveraineté Singulière représente l\'apogée de l\'art horloger contemporain avec son boîtier en tantale, métal rare et difficile à travailler. Édition strictement limitée à 15 exemplaires, cette montre à grande complication intègre un quantième perpétuel, une équation du temps et une indication de réserve de marche de 7 jours. Le cadran en nacre noire est décoré de motifs guillochés réalisés à la main par notre maître guillocheur.', 'Montre grande complication en tantale avec quantième perpétuel et équation du temps. Édition ultra-limitée à 15 exemplaires.', 21000.00, NULL, '/assets/img/products/souverainete-singuliere.jpg', '/assets/img/products/souverainete-singuliere-detail1.jpg', 8, 2, 0.170, 1, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Épopée Rare
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(305, 'Épopée Rare', 'epopee-rare', 'ELX-LE-005', 'L\'Épopée Rare associe innovation et tradition avec son boîtier en or palladié et titane grade 5. Ce chronographe rattrapante monopoussoir est doté d\'un mécanisme révolutionnaire breveté par notre manufacture. Produite en série limitée de 20 exemplaires, chaque pièce est accompagnée d\'un certificat d\'authenticité signé et d\'un livre retraçant l\'histoire de sa conception. Le cadran en carbone forgé offre une légèreté et une résistance exceptionnelles.', 'Chronographe rattrapante monopoussoir en or palladié et titane avec mécanisme breveté. Série limitée à 20 exemplaires.', 25000.00, NULL, '/assets/img/products/epopee-rare.jpg', '/assets/img/products/epopee-rare-detail1.jpg,/assets/img/products/epopee-rare-detail2.jpg', 12, 3, 0.138, 1, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Grâce Limitée
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(401, 'Grâce Limitée', 'grace-limitee', 'ELX-LE-F01', 'La Grâce Limitée incarne l\'élégance féminine dans sa forme la plus pure avec son boîtier en or blanc 18k entièrement pavé de diamants taille brillant (2.8 carats). Son cadran en nacre blanche est orné de 12 rubis en guise d\'index horaires. Limitée à 30 exemplaires, cette création d\'exception abrite un mouvement automatique ultra-plat de haute précision. Le bracelet en satin est complété par une boucle déployante sertie de diamants.', 'Montre en or blanc 18k entièrement pavée de diamants avec cadran en nacre et index en rubis. Édition limitée à 30 exemplaires.', 20500.00, NULL, '/assets/img/products/grace-limitee.jpg', '/assets/img/products/grace-limitee-detail1.jpg', 18, 4, 0.110, 5, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Splendeur Exclusif
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(402, 'Splendeur Exclusif', 'splendeur-exclusif', 'ELX-LE-F02', 'La Splendeur Exclusif allie horlogerie et joaillerie dans un chef-d\'œuvre technique et esthétique. Son boîtier en or rose 18k adopte une forme florale sertie de saphirs roses taillés sur mesure. Le cadran en opale australienne présente des teintes irisées uniques pour chaque pièce de cette série limitée à 25 exemplaires. Le mouvement à remontage manuel est visible par le fond saphir et présente des ponts gravés à la main.', 'Montre joaillière en or rose 18k sertie de saphirs roses avec cadran en opale australienne. Série limitée à 25 exemplaires.', 22000.00, NULL, '/assets/img/products/splendeur-exclusif.jpg', '/assets/img/products/splendeur-exclusif-detail1.jpg,/assets/img/products/splendeur-exclusif-detail2.jpg', 15, 3, 0.115, 5, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Éclat Unique
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(403, 'Éclat Unique', 'eclat-unique', 'ELX-LE-F03', 'L\'Éclat Unique se distingue par son cadran en malachite véritable dont les veines naturelles créent des motifs uniques sur chaque exemplaire. Son boîtier ovale en or jaune 18k est délicatement serti de tsavorites formant un dégradé subtil. Cette pièce d\'exception, limitée à 20 exemplaires, présente une petite seconde excentrée à 7 heures et une indication de phases de lune poétique à 2 heures, dans une composition asymétrique captivante.', 'Montre en or jaune 18k avec cadran en malachite véritable et sertissage de tsavorites. Édition limitée à 20 exemplaires.', 23500.00, NULL, '/assets/img/products/eclat-unique.jpg', '/assets/img/products/eclat-unique-detail1.jpg', 12, 3, 0.120, 5, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Noblesse Singulière
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(404, 'Noblesse Singulière', 'noblesse-singuliere', 'ELX-LE-F04', 'La Noblesse Singulière allie innovation et raffinement avec son boîtier en céramique blanche high-tech sublimé par des inserts en or rose 18k. Son cadran laqué blanc neige présente une indication rétrograde des jours et une grande date à 12 heures. Limitée à 40 exemplaires, cette création unique abrite un mouvement automatique exclusif développé par notre manufacture. Le bracelet intégré en céramique et or rose offre un confort incomparable.', 'Montre en céramique blanche et or rose 18k avec fonctions rétrogrades et grande date. Édition limitée à 40 exemplaires.', 21000.00, NULL, '/assets/img/products/noblesse-singuliere.jpg', '/assets/img/products/noblesse-singuliere-detail1.jpg,/assets/img/products/noblesse-singuliere-detail2.jpg', 22, 4, 0.125, 5, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Élégance Rare
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(405, 'Élégance Rare', 'elegance-rare', 'ELX-LE-F05', 'L\'Élégance Rare redéfinit les codes du luxe horloger féminin avec son boîtier tonneau en titane et sa lunette sertie de diamants baguette (1.8 carats). Son cadran en lapis-lazuli véritable est parsemé de particules de pyrite créant un effet ciel étoilé saisissant. Cette édition limitée à 15 exemplaires intègre un mouvement squelette exclusif visible à travers le cadran ajouré, alliant prouesse technique et beauté hypnotique.', 'Montre en titane et diamants avec cadran en lapis-lazuli et mouvement squelette visible. Série ultra-limitée à 15 exemplaires.', 25000.00, NULL, '/assets/img/products/elegance-rare.jpg', '/assets/img/products/elegance-rare-detail1.jpg', 8, 2, 0.105, 5, 4, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montres Homme Collection Prestige

-- Montre Excellence Royale
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(501, 'Excellence Royale', 'excellence-royale', 'ELX-PR-001', 'L\'Excellence Royale incarne le summum du raffinement horloger avec son boîtier en or blanc 18k finement ciselé à la main. Son cadran bleu profond aux reflets soleil est réalisé selon une technique ancestrale et nécessite plus de 20 étapes de fabrication. Le mouvement manufacture à remontage automatique offre une réserve de marche exceptionnelle de 72 heures avec un tourbillon volant visible à travers le cadran. Le bracelet en alligator bleu roi est cousu main par nos artisans maroquiniers et complété par une boucle déployante en or blanc.', 'Montre en or blanc 18k avec tourbillon volant et cadran bleu soleil réalisé selon une technique ancestrale. Chef-d\'œuvre de la haute horlogerie.', 32500.00, NULL, '/assets/img/products/excellence-royale.jpg', '/assets/img/products/excellence-royale-detail1.jpg,/assets/img/products/excellence-royale-detail2.jpg', 12, 3, 0.165, 4, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Majesté Éternelle
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(502, 'Majesté Éternelle', 'majeste-eternelle', 'ELX-PR-002', 'La Majesté Éternelle est une démonstration parfaite de la maîtrise horlogère contemporaine, associant tradition et innovation. Son boîtier en platine 950 entièrement poli à la main abrite un mouvement manufacture avec remontage manuel et quantième perpétuel. Cette complication prestigieuse indique le jour, la date, le mois et les années bissextiles, ne nécessitant aucun ajustement jusqu\'en 2100. Le cadran en émail grand feu noir est réalisé par notre maître émailleur, avec des index en appliques d\'or blanc. Le fond transparent révèle un mouvement finement décoré avec des ponts anglés et polis, ainsi que des vis bleues et des rubis flamboyants.', 'Montre en platine 950 avec quantième perpétuel et cadran en émail grand feu noir. Une référence en matière de haute horlogerie technique.', 45000.00, NULL, '/assets/img/products/majeste-eternelle.jpg', '/assets/img/products/majeste-eternelle-detail1.jpg', 8, 2, 0.175, 4, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Grandeur Suprême
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(503, 'Grandeur Suprême', 'grandeur-supreme', 'ELX-PR-003', 'La Grandeur Suprême est l\'expression ultime de notre savoir-faire horloger, combinant technicité et élégance. Son boîtier en or rose 18k de 42mm renferme un calibre manufacture chronographe à rattrapante avec roue à colonnes, l\'une des complications les plus nobles et complexes de l\'horlogerie. Le cadran est composé de jade impérial, pierre rare d\'un vert profond uniforme, qui est travaillée avec une extrême délicatesse et finesse. Les sous-cadrans contrastants et l\'échelle tachymétrique gravée sur la lunette confèrent à cette pièce d\'exception un caractère sportif et élégant à la fois. Le fond transparent permet d\'admirer le mouvement avec ses finitions côtes de Genève, anglage à la main et perlage.', 'Chronographe rattrapante en or rose 18k avec cadran en jade impérial. Alliance parfaite entre performance technique et rareté des matériaux.', 38500.00, NULL, '/assets/img/products/grandeur-supreme.jpg', '/assets/img/products/grandeur-supreme-detail1.jpg,/assets/img/products/grandeur-supreme-detail2.jpg', 10, 3, 0.158, 1, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Accomplissement Royal
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(504, 'Accomplissement Royal', 'accomplissement-royal', 'ELX-PR-004', 'L\'Accomplissement Royal est une œuvre horlogère qui défie les conventions avec son boîtier en tantalum, métal rare aux reflets bleu-gris et d\'une résistance exceptionnelle. À l\'intérieur, notre calibre manufacture à remontage manuel intègre une répétition minutes, complication rarissime qui sonne les heures, quarts d\'heures et minutes sur demande grâce à deux timbres cathédrale. Le cadran en onyx noir profond est incrusté d\'indices en diamants baguette totalisant 1.2 carats. Au verso, le fond saphir dévoile le mécanisme complexe de sonnerie avec ses marteaux et timbres, ainsi que les finitions exceptionnelles réalisées entièrement à la main.', 'Montre à répétition minutes en tantalum avec cadran en onyx et indices en diamants. Une merveille technique et acoustique.', 52000.00, NULL, '/assets/img/products/accomplissement-royal.jpg', '/assets/img/products/accomplissement-royal-detail1.jpg', 5, 2, 0.162, 4, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Perfection Ultime
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(505, 'Perfection Ultime', 'perfection-ultime', 'ELX-PR-005', 'La Perfection Ultime représente l\'apogée de notre quête d\'excellence avec son boîtier en or gris 18k aux finitions alternées polies et brossées. Le cadran en météorite de Gibeon, vieille de plus de 4 milliards d\'années, présente des motifs cristallins naturels qui rendent chaque pièce véritablement unique. Cette météorite est soigneusement découpée puis polie pour révéler ses figures de Widmanstätten caractéristiques. Notre mouvement manufacture à double barillet offre une réserve de marche exceptionnelle de 8 jours, avec indicateur de réserve de marche intégré au cadran. Les index appliqués en or gris et les aiguilles dauphine polies à la main complètent cette création d\'exception.', 'Montre en or gris 18k avec cadran en météorite de Gibeon et réserve de marche de 8 jours. Un témoignage cosmique au poignet.', 41500.00, NULL, '/assets/img/products/perfection-ultime.jpg', '/assets/img/products/perfection-ultime-detail1.jpg,/assets/img/products/perfection-ultime-detail2.jpg', 7, 2, 0.155, 1, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montres Femme Collection Prestige

-- Montre Splendeur Céleste
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(601, 'Splendeur Céleste', 'splendeur-celeste', 'ELX-PR-F01', 'La Splendeur Céleste capture l\'essence même de la voûte étoilée dans une création horlogère d\'exception. Son boîtier en or blanc 18k de 36mm est entièrement serti de diamants taille brillant sur la lunette et les cornes (2.3 carats au total). Le cadran en aventurine véritable offre un spectacle fascinant, semblable à un ciel nocturne constellé d\'étoiles scintillantes. Les 12 index sont des saphirs bleus taillés spécialement pour cette pièce, tandis que les aiguilles en or blanc sont finement ajourées. Le mouvement automatique ultra-plat intègre une complication phase de lune astronomique, visible à travers une ouverture à 6 heures, où le disque de lune est réalisé en nacre blanche sur fond d\'aventurine.', 'Montre en or blanc 18k sertie de diamants avec cadran en aventurine et phase de lune astronomique. Un véritable ciel étoilé au poignet.', 36500.00, NULL, '/assets/img/products/splendeur-celeste.jpg', '/assets/img/products/splendeur-celeste-detail1.jpg,/assets/img/products/splendeur-celeste-detail2.jpg', 8, 2, 0.120, 5, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Délicatesse Majestueuse
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(602, 'Délicatesse Majestueuse', 'delicatesse-majestueuse', 'ELX-PR-F02', 'La Délicatesse Majestueuse redéfinit l\'élégance féminine avec son boîtier tonneau en or rose 18k, dont la forme ergonomique épouse parfaitement le poignet. Le cadran en opale noble australienne présente des jeux de couleurs irisées uniques, oscillant entre bleus, verts et violets selon l\'angle de vue. Chaque opale est sélectionnée pour ses qualités exceptionnelles et polie avec un soin extrême par nos lapidaires. Le mouvement manufacture à remontage manuel est visible à travers le fond saphir, révélant des ponts en forme de fleurs délicatement gravés et incrustés de diamants. Le bracelet en cuir d\'alligator rose poudré est fabriqué sur mesure et assemblé à la main.', 'Montre tonneau en or rose 18k avec cadran en opale noble australienne aux reflets multicolores. Une création joaillière d\'exception.', 34000.00, NULL, '/assets/img/products/delicatesse-majestueuse.jpg', '/assets/img/products/delicatesse-majestueuse-detail1.jpg', 6, 2, 0.115, 5, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Élégance Souveraine
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(603, 'Élégance Souveraine', 'elegance-souveraine', 'ELX-PR-F03', 'L\'Élégance Souveraine incarne la quintessence du luxe discret avec son boîtier ovale en titane grade 5 et sa lunette sertie d\'une double rangée de diamants taille brillant (1.85 carats). Le cadran en nacre blanche est travaillé selon la technique du guillochage à la main, créant un motif ondulé d\'une finesse extrême. Les chiffres romains sont peints à la main avec une encre composite mêlant or 24k et résine spéciale, procurant une texture en relief unique. Le mouvement automatique ultra-plat, visible par le fond transparent, est décoré de Côtes de Genève et de ponts anglés à la main. Le bracelet intégré en titane et or rose 18k offre une flexibilité et un confort exceptionnels.', 'Montre ovale en titane et diamants avec cadran en nacre guillochée à la main et bracelet intégré. Alliance parfaite de légèreté et d\'élégance.', 31500.00, NULL, '/assets/img/products/elegance-souveraine.jpg', '/assets/img/products/elegance-souveraine-detail1.jpg,/assets/img/products/elegance-souveraine-detail2.jpg', 10, 3, 0.095, 5, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Éclat Impérial
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(604, 'Éclat Impérial', 'eclat-imperial', 'ELX-PR-F04', 'L\'Éclat Impérial est un chef-d\'œuvre de joaillerie horlogère qui marie l\'art de la haute horlogerie et de la haute joaillerie. Son boîtier en or blanc 18k adopte une forme inspirée des arts déco et est entièrement pavé de diamants taille brillant et baguette (3.4 carats). Le cadran en onyx noir profond est orné d\'un motif floral serti de rubis, saphirs et émeraudes formant un dégradé de couleurs. Le mouvement manufacture à remontage manuel est squeletté et ajouré à la main, permettant d\'admirer ses rouages à travers le cadran partiellement transparent. Le bracelet en satin noir est complété par une boucle déployante en or blanc également sertie de diamants.', 'Montre joaillière en or blanc 18k entièrement pavée de diamants avec motif floral en pierres précieuses sur cadran en onyx. Une pièce d\'art au poignet.', 45000.00, NULL, '/assets/img/products/eclat-imperial.jpg', '/assets/img/products/eclat-imperial-detail1.jpg', 4, 1, 0.125, 5, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Grâce Absolue
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(605, 'Grâce Absolue', 'grace-absolue', 'ELX-PR-F05', 'La Grâce Absolue est l\'incarnation de la délicatesse et de la précision technique dans une montre féminine. Son boîtier rectangulaire aux angles arrondis en or jaune 18k est inspiré des années 1920 et abrite notre calibre miniature le plus fin (seulement 2.1mm d\'épaisseur). Le cadran en laque urushi traditionnelle japonaise représente des fleurs de cerisier sur fond noir profond, réalisé par un maître laqueur selon une technique millénaire nécessitant plus de 40 couches de laque. Chaque fleur est rehaussée de poudre d\'or et de fragments de nacre selon la technique du maki-e. Les aiguilles dauphine en or jaune complètent harmonieusement ce garde-temps d\'exception.', 'Montre rectangulaire Art Déco en or jaune 18k avec cadran en laque urushi représentant des fleurs de cerisier. Un hommage à l\'artisanat traditionnel japonais.', 29800.00, NULL, '/assets/img/products/grace-absolue.jpg', '/assets/img/products/grace-absolue-detail1.jpg,/assets/img/products/grace-absolue-detail2.jpg', 7, 2, 0.085, 5, 3, CURRENT_TIMESTAMP, 1, 1, 1);

-- --------------------------------------------------------
-- Montres Homme Collection Sport
-- --------------------------------------------------------

-- Montre Force Titan
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(701, 'Force Titan', 'force-titan', 'ELX-SP-001', 'La Force Titan incarne la robustesse et la fiabilité à l\'état pur. Son boîtier en titane grade 5 traité DLC noir de 45mm offre une résistance exceptionnelle aux chocs et aux rayures, tout en restant étonnamment léger au poignet. Le cadran structuré noir mat avec motif guilloché concentrique présente des index et aiguilles surdimensionnés revêtus de Super-LumiNova® pour une parfaite lisibilité dans toutes les conditions. Notre calibre automatique sport ELX-89 protégé par un système anti-magnétique offre une précision chronométrique même dans les environnements les plus extrêmes. Étanche jusqu\'à 300 mètres, cette montre intègre une valve à hélium automatique pour les plongées professionnelles. Le bracelet en caoutchouc technique intègre une extension rapide pour un ajustement parfait sur combinaison de plongée.', 'Montre de plongée professionnelle en titane grade 5 avec traitement DLC noir. Étanche à 300 mètres avec protection anti-magnétique et système de valve à hélium automatique.', 12500.00, NULL, '/assets/img/products/force-titan.jpg', '/assets/img/products/force-titan-detail1.jpg,/assets/img/products/force-titan-detail2.jpg', 15, 3, 0.120, 1, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Dynamique Élite
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(702, 'Dynamique Élite', 'dynamique-elite', 'ELX-SP-002', 'La Dynamique Élite est l\'alliance parfaite entre performance technique et design contemporain. Son boîtier en céramique high-tech de 44mm offre une résistance aux rayures incomparable et une légèreté idéale pour les activités sportives intenses. Le cadran texturé bleu profond avec dégradé subtil est protégé par un verre saphir bombé traité antireflet sur les deux faces. Notre chronographe flyback manufacture permet des mesures successives instantanées grâce à une simple pression. L\'échelle tachymétrique sur la lunette permet de calculer vitesses moyennes et distances. Le fond transparent révèle notre calibre ELX-725 et sa masse oscillante en or rose 18k gravée. Le bracelet en caoutchouc intègre des inserts en titane pour une durabilité exceptionnelle tout en offrant un confort optimal.', 'Chronographe flyback en céramique high-tech avec échelle tachymétrique. Alliance de légèreté et résistance aux rayures avec mouvement manufacture sophistiqué visible par le fond transparent.', 14000.00, NULL, '/assets/img/products/dynamique-elite.jpg', '/assets/img/products/dynamique-elite-detail1.jpg,/assets/img/products/dynamique-elite-detail2.jpg', 18, 4, 0.115, 1, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Vitesse Suprême
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(703, 'Vitesse Suprême', 'vitesse-supreme', 'ELX-SP-003', 'La Vitesse Suprême est née de notre passion pour les sports mécaniques et s\'inspire directement du monde des compétitions automobiles. Son boîtier en titane et carbone forgé de 43mm présente un design aérodynamique avec poussoirs ergonomiques inspirés des pistons de moteur. Le cadran squelette multicouche révèle partiellement le mouvement chronographe et intègre des compteurs directement inspirés des tableaux de bord de voitures de course. La lunette en céramique comporte une échelle tachymétrique pour mesurer les vitesses jusqu\'à 400 km/h. Notre calibre chronographe automatique haute fréquence bat à 36,000 alternances par heure, permettant des mesures au 1/10e de seconde d\'une précision absolue. Le bracelet en cuir de veau perforé avec coutures contrastantes évoque les gants de pilote de course.', 'Chronographe haute fréquence en titane et carbone forgé inspiré des sports mécaniques. Mesure du temps au 1/10e de seconde avec design évoquant les voitures de course.', 13000.00, NULL, '/assets/img/products/vitesse-supreme.jpg', '/assets/img/products/vitesse-supreme-detail1.jpg', 12, 3, 0.118, 1, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Puissance Infinie
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(704, 'Puissance Infinie', 'puissance-infinie', 'ELX-SP-004', 'La Puissance Infinie repousse les limites de l\'innovation horlogère sportive avec son boîtier en composite de carbone et titane de 45mm. Sa structure interne en nid d\'abeille lui confère une résistance exceptionnelle pour un poids plume de seulement 72 grammes. Le cadran openworked à géométrie complexe permet d\'admirer notre calibre manufacture ELX-P120 à remontage automatique doté d\'une réserve de marche de 120 heures - une prouesse technique pour une montre sportive. L\'indicateur de réserve de marche à 9 heures est complété par une petite seconde à 6 heures et une date à guichet à 3 heures. La couronne vissée à double joint garantit une étanchéité à 200 mètres. Le système breveté d\'absorption des chocs protège le mouvement dans les conditions les plus extrêmes. Le bracelet intégré en fibre technique et caoutchouc naturel offre flexibilité et durabilité.', 'Montre ultralégère en composite carbone-titane avec structure en nid d\'abeille et réserve de marche de 120 heures. Un concentré de technologie offrant résistance exceptionnelle et légèreté extrême.', 15500.00, NULL, '/assets/img/products/puissance-infinie.jpg', '/assets/img/products/puissance-infinie-detail1.jpg,/assets/img/products/puissance-infinie-detail2.jpg', 10, 2, 0.072, 1, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Sprint Ultime
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(705, 'Sprint Ultime', 'sprint-ultime', 'ELX-SP-005', 'Le Sprint Ultime représente une fusion parfaite entre innovation technologique et design inspiré de l\'athlétisme de haut niveau. Son boîtier en alliage d\'aluminium aéronautique de 43mm traité par anodisation offre une palette de couleurs vives et une légèreté exceptionnelle. Le cadran multicouche avec compteur 60 minutes surdimensionné à 6 heures est optimisé pour les entraînements fractionnés. Notre calibre chronographe à rattrapante permet des chronométrages intermédiaires précis, idéal pour les coachs sportifs et les athlètes. Le système exclusif de bracelet interchangeable permet de passer d\'un bracelet technique à un bracelet en cuir en quelques secondes sans outil. La glace saphir avec traitement antireflet 7 couches garantit une lisibilité parfaite même en plein soleil.', 'Chronographe rattrapante en aluminium anodisé conçu pour les athlètes et entraîneurs. Système exclusif de bracelet interchangeable et cadran optimisé pour l\'entraînement fractionné.', 14800.00, NULL, '/assets/img/products/sprint-ultime.jpg', '/assets/img/products/sprint-ultime-detail1.jpg,/assets/img/products/sprint-ultime-detail2.jpg', 14, 3, 0.085, 1, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Rivalité Acérée
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(706, 'Rivalité Acérée', 'rivalite-aceree', 'ELX-SP-006', 'La Rivalité Acérée incarne l\'esprit de compétition dans sa forme la plus pure. Son boîtier en titane grade 5 et céramique noire de 46mm arbore une architecture complexe inspirée des moteurs de Formule 1. Le cadran multicouche comporte une échelle tachymétrique, un totalisateur 12 heures et un indicateur de réserve de marche de 65 heures. Notre calibre chronographe automatique intégré ELX-500 à roue à colonnes et embrayage vertical garantit une précision absolue et un fonctionnement parfait des poussoirs. La lunette bidirectionnelle en céramique permet le calcul de temps intermédiaires. Le fond saphir révèle la masse oscillante en forme de volant de course finement ajourée et le pont de chronographe en forme d\'aileron. Le bracelet en caoutchouc texturé évoque les pneus de course avec son motif directionnel.', 'Chronographe de compétition en titane et céramique avec calibre intégré à roue à colonnes. Design inspiré des Formule 1 avec finitions évoquant la mécanique de haute performance.', 16200.00, NULL, '/assets/img/products/rivalite-aceree.jpg', '/assets/img/products/rivalite-aceree-detail1.jpg,/assets/img/products/rivalite-aceree-detail2.jpg', 9, 2, 0.130, 1, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- --------------------------------------------------------
-- Montres Femme Collection Sport
-- --------------------------------------------------------

-- Montre Élan Glamour
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(751, 'Élan Glamour', 'elan-glamour', 'ELX-SP-F01', 'L\'Élan Glamour redéfinit le concept de montre sport féminine avec son boîtier en céramique blanche high-tech de 38mm, alliant légèreté et résistance aux rayures. La lunette sertie de 60 diamants taille brillant (0.75 carat) encadre un cadran en nacre blanche aux reflets iridescents avec motif ondulé évoquant les vagues. Les index appliqués en or rose 18k complètent harmonieusement les aiguilles squelettées luminescentes. Notre calibre automatique ELX-222 visible par le fond saphir offre une réserve de marche de 48 heures. Étanche à 100 mètres, cette montre allie performance sportive et élégance raffinée. Le bracelet intégré en céramique blanche comporte un système de micro-ajustement pour un confort optimal en toutes circonstances.', 'Montre sport-chic en céramique blanche sertie de diamants avec cadran en nacre. Alliance d\'élégance féminine et de technologie sportive, étanche à 100 mètres.', 11500.00, NULL, '/assets/img/products/elan-glamour.jpg', '/assets/img/products/elan-glamour-detail1.jpg,/assets/img/products/elan-glamour-detail2.jpg', 12, 3, 0.085, 5, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Énergie Radieuse
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(752, 'Énergie Radieuse', 'energie-radieuse', 'ELX-SP-F02', 'L\'Énergie Radieuse capture l\'essence du dynamisme féminin dans une création à la fois sportive et raffinée. Son boîtier en titane grade 5 de 36mm traité par PVD or rose crée un contraste saisissant avec le cadran en aventurine bleue mouchetée d\'éclats métalliques, évoquant un ciel étoilé. Ce garde-temps intègre notre chronographe monopoussoir exclusif, permettant le contrôle des fonctions start/stop/reset par une seule couronne, sublimant ainsi la pureté des lignes. Étanche à 50 mètres, cette montre associe fonctionnalité sportive et élégance sophistiquée. Le bracelet en caoutchouc technique bleu nuit est orné de surpiqûres or rose et doté d\'un système de changement rapide pour s\'adapter à toutes les occasions.', 'Chronographe monopoussoir en titane or rose avec cadran en aventurine bleue. Design épuré alliant élégance et fonctionnalité pour une femme active et raffinée.', 12800.00, NULL, '/assets/img/products/energie-radieuse.jpg', '/assets/img/products/energie-radieuse-detail1.jpg', 15, 3, 0.078, 5, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Vibrance Pure
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(753, 'Vibrance Pure', 'vibrance-pure', 'ELX-SP-F03', 'La Vibrance Pure incarne la fusion parfaite entre haute performance sportive et esthétique contemporaine. Son boîtier en composite de fibre de carbone de 37mm présente un motif marbré unique sur chaque pièce. Le cadran en saphir fumé laisse entrevoir le mouvement squelette manufacture, créant un effet de profondeur saisissant. Notre calibre automatique ELX-VS2 visible des deux côtés combine légèreté et robustesse grâce à ses ponts en titane anodisé bleu. La lunette en céramique blanche graduée sur 60 minutes permet le chronométrage d\'activités sportives. Étanche à 100 mètres, cette montre résiste aux conditions les plus exigeantes tout en conservant une allure sophistiquée. Le bracelet interchangeable en caoutchouc blanc texturé est complété par une boucle déployante en titane.', 'Montre squelette en fibre de carbone avec cadran en saphir fumé et mouvement visible. Une pièce technique ultramoderne combinant légèreté, transparence et résistance sportive.', 13000.00, NULL, '/assets/img/products/vibrance-pure.jpg', '/assets/img/products/vibrance-pure-detail1.jpg,/assets/img/products/vibrance-pure-detail2.jpg', 10, 2, 0.065, 5, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Aura Athlétique
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(754, 'Aura Athlétique', 'aura-athletique', 'ELX-SP-F04', 'L\'Aura Athlétique est conçue pour la femme d\'action qui ne sacrifie pas l\'élégance à la performance. Son boîtier tonneau en titane et céramique noire de 35mm offre une robustesse exceptionnelle dans un format parfaitement adapté au poignet féminin. Le cadran guilloché soleil rouge grenat s\'anime de reflets changeants selon l\'incidence de la lumière, tandis que les index diamantés apportent une touche de brillance subtile. Notre calibre chronographe flyback automatique ultraplat permet de chronométrer des événements successifs avec une seule pression. Le système breveté de correction rapide de la date et du fuseau horaire facilite les voyages internationaux. Étanche à 200 mètres, cette montre accompagne sa propriétaire dans toutes ses aventures. Le bracelet intégré en titane et céramique noire est ajustable au demi-maillon pour un confort optimal.', 'Chronographe flyback en titane et céramique avec cadran guilloché grenat et index diamantés. Une montre sport-chic pour femmes voyageuses combinant technique horlogère et design audacieux.', 14500.00, NULL, '/assets/img/products/aura-athletique.jpg', '/assets/img/products/aura-athletique-detail1.jpg', 8, 2, 0.095, 5, 2, CURRENT_TIMESTAMP, 1, 1, 1);

-- Montre Allure Détente
INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `visible`, `featured`, `nouveaute`) VALUES
(755, 'Allure Détente', 'allure-detente', 'ELX-SP-F05', 'L\'Allure Détente réinvente le concept de la montre de sport féminine avec son boîtier en aluminium aéronautique anodisé turquoise de 39mm. Ultraléger (seulement 55 grammes avec le bracelet), ce garde-temps se fait oublier au poignet tout en affirmant un style audacieux. Le cadran en nacre noire avec motif vagues en trois dimensions capture et réfléchit la lumière de façon spectaculaire. Notre calibre automatique avec indicateur jour/nuit à 6 heures est visible par le fond transparent. L\'étanchéité à 100 mètres permet de pratiquer la natation ou le snorkeling en toute sérénité. Les index et aiguilles luminescents garantissent une parfaite lisibilité dans toutes les conditions. Le bracelet en caoutchouc technique turquoise avec système de fixation rapide permet de changer facilement de style.', 'Montre sport-loisir ultraléger en aluminium turquoise avec cadran en nacre noire texturée. Design contemporain et audacieux pour une femme active appréciant confort et style distinctif.', 13200.00, NULL, '/assets/img/products/allure-detente.jpg', '/assets/img/products/allure-detente-detail1.jpg,/assets/img/products/allure-detente-detail2.jpg', 14, 3, 0.055, 5, 2, CURRENT_TIMESTAMP, 1, 1, 1);