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