CREATE DATABASE MontresDeLuxe;
USE MontresDeLuxe;

-- Table Utilisateurs
CREATE TABLE Utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table Collections
CREATE TABLE Collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

-- Table Montres
CREATE TABLE Montres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    collection_id INT,
    prix DECIMAL(10,2) NOT NULL,
    description TEXT,
    FOREIGN KEY (collection_id) REFERENCES Collections(id)
);

-- Table DescriptionProduits
CREATE TABLE DescriptionProduits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montre_id INT,
    details TEXT,
    FOREIGN KEY (montre_id) REFERENCES Montres(id)
);

-- Table APropos
CREATE TABLE APropos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT
);

-- Table Organigramme
CREATE TABLE Organigramme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    poste VARCHAR(100) NOT NULL,
    description TEXT
);

-- Table Panier
CREATE TABLE Panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    montre_id INT,
    quantite INT NOT NULL,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id),
    FOREIGN KEY (montre_id) REFERENCES Montres(id)
);