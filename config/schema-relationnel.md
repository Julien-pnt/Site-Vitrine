# Schéma Relationnel de la Base de Données `elixir_du_temps`

## Tables Utilisateurs et Authentification

### Table `utilisateurs`
| Colonne           | Type                            | Contraintes                      |
|-------------------|--------------------------------|----------------------------------|
| id                | INT                            | AUTO_INCREMENT, PRIMARY KEY      |
| nom               | VARCHAR(100)                   | NOT NULL                         |
| prenom            | VARCHAR(100)                   |                                  |
| email             | VARCHAR(255)                   | NOT NULL, UNIQUE                 |
| mot_de_passe      | VARCHAR(255)                   | NOT NULL                         |
| telephone         | VARCHAR(20)                    |                                  |
| adresse           | TEXT                           |                                  |
| code_postal       | VARCHAR(10)                    |                                  |
| ville             | VARCHAR(100)                   |                                  |
| pays              | VARCHAR(100)                   | DEFAULT 'France'                 |
| date_creation     | DATETIME                       | DEFAULT CURRENT_TIMESTAMP        |
| date_modification | DATETIME                       | ON UPDATE CURRENT_TIMESTAMP      |
| role              | ENUM('client','admin','manager')| DEFAULT 'client'                |
| actif             | BOOLEAN                        | DEFAULT TRUE                     |

### Table `auth_tokens`
| Colonne      | Type          | Contraintes                      |
|--------------|---------------|----------------------------------|
| id           | INT           | AUTO_INCREMENT, PRIMARY KEY      |
| user_id      | INT           | NOT NULL, FOREIGN KEY            |
| token        | VARCHAR(255)  | NOT NULL                         |
| created_at   | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP        |
| expires_at   | DATETIME      | NOT NULL                         |

### Table `connexions_log`
| Colonne         | Type                                    | Contraintes                  |
|-----------------|----------------------------------------|------------------------------|
| id              | INT                                    | AUTO_INCREMENT, PRIMARY KEY  |
| user_id         | INT                                    | NOT NULL, FOREIGN KEY        |
| ip_address      | VARCHAR(45)                            | NOT NULL                     |
| user_agent      | VARCHAR(255)                           |                              |
| action          | ENUM('login','logout','failed_attempt')| DEFAULT 'login'              |
| date_connexion  | DATETIME                               | DEFAULT CURRENT_TIMESTAMP    |

## Tables Catalogue Produits

### Table `categories`
| Colonne      | Type          | Contraintes                      |
|--------------|---------------|----------------------------------|
| id           | INT           | AUTO_INCREMENT, PRIMARY KEY      |
| nom          | VARCHAR(100)  | NOT NULL                         |
| slug         | VARCHAR(100)  | NOT NULL, UNIQUE                 |
| description  | TEXT          |                                  |
| image        | VARCHAR(255)  |                                  |
| parent_id    | INT           | NULL, FOREIGN KEY (self)         |
| position     | INT           | DEFAULT 0                        |

### Table `collections`
| Colonne      | Type          | Contraintes                      |
|--------------|---------------|----------------------------------|
| id           | INT           | AUTO_INCREMENT, PRIMARY KEY      |
| nom          | VARCHAR(100)  | NOT NULL                         |
| slug         | VARCHAR(100)  | NOT NULL, UNIQUE                 |
| description  | TEXT          |                                  |
| image        | VARCHAR(255)  |                                  |
| date_debut   | DATE          |                                  |
| date_fin     | DATE          |                                  |
| active       | BOOLEAN       | DEFAULT TRUE                     |

### Table `produits`
| Colonne                | Type           | Contraintes                      |
|------------------------|----------------|----------------------------------|
| id                     | INT            | AUTO_INCREMENT, PRIMARY KEY      |
| nom                    | VARCHAR(255)   | NOT NULL                         |
| slug                   | VARCHAR(255)   | NOT NULL, UNIQUE                 |
| reference              | VARCHAR(50)    | UNIQUE                           |
| description            | TEXT           |                                  |
| description_courte     | VARCHAR(500)   |                                  |
| prix                   | DECIMAL(10, 2) | NOT NULL                         |
| prix_promo             | DECIMAL(10, 2) | NULL                             |
| image                  | VARCHAR(255)   |                                  |
| images_supplementaires | TEXT           |                                  |
| stock                  | INT            | NOT NULL DEFAULT 0               |
| stock_alerte           | INT            | DEFAULT 5                        |
| poids                  | DECIMAL(8, 3)  | NULL                             |
| categorie_id           | INT            | FOREIGN KEY                      |
| collection_id          | INT            | NULL, FOREIGN KEY                |
| date_creation          | DATETIME       | DEFAULT CURRENT_TIMESTAMP        |
| date_modification      | DATETIME       | ON UPDATE CURRENT_TIMESTAMP      |
| visible                | BOOLEAN        | DEFAULT TRUE                     |
| featured               | BOOLEAN        | DEFAULT FALSE                    |
| nouveaute              | BOOLEAN        | DEFAULT FALSE                    |

### Table `attributs`
| Colonne      | Type                                   | Contraintes                  |
|--------------|----------------------------------------|------------------------------|
| id           | INT                                    | AUTO_INCREMENT, PRIMARY KEY  |
| nom          | VARCHAR(100)                           | NOT NULL                     |
| type         | ENUM('texte','nombre','booleen','liste')| DEFAULT 'texte'             |

### Table `produit_attributs`
| Colonne      | Type          | Contraintes                      |
|--------------|---------------|----------------------------------|
| produit_id   | INT           | NOT NULL, FOREIGN KEY            |
| attribut_id  | INT           | NOT NULL, FOREIGN KEY            |
| valeur       | TEXT          | NOT NULL                         |

## Tables Commandes et Panier

### Table `commandes`
| Colonne              | Type                                                              | Contraintes                      |
|----------------------|-------------------------------------------------------------------|----------------------------------|
| id                   | INT                                                               | AUTO_INCREMENT, PRIMARY KEY      |
| reference            | VARCHAR(50)                                                       | NOT NULL, UNIQUE                 |
| utilisateur_id       | INT                                                               | NOT NULL, FOREIGN KEY            |
| statut               | ENUM('en_attente','payee','en_preparation','expediee','livree','annulee','remboursee') | DEFAULT 'en_attente' |
| date_commande        | DATETIME                                                          | DEFAULT CURRENT_TIMESTAMP        |
| date_modification    | DATETIME                                                          | ON UPDATE CURRENT_TIMESTAMP      |
| total_produits       | DECIMAL(10, 2)                                                    | NOT NULL                         |
| frais_livraison      | DECIMAL(10, 2)                                                    | DEFAULT 0.00                     |
| total_taxe           | DECIMAL(10, 2)                                                    | DEFAULT 0.00                     |
| total                | DECIMAL(10, 2)                                                    | NOT NULL                         |
| mode_paiement        | VARCHAR(50)                                                       |                                  |
| adresse_livraison    | TEXT                                                              | NOT NULL                         |
| adresse_facturation  | TEXT                                                              | NOT NULL                         |
| notes                | TEXT                                                              |                                  |

### Table `articles_commande`
| Colonne            | Type           | Contraintes                      |
|--------------------|----------------|----------------------------------|
| id                 | INT            | AUTO_INCREMENT, PRIMARY KEY      |
| commande_id        | INT            | NOT NULL, FOREIGN KEY            |
| produit_id         | INT            | NOT NULL, FOREIGN KEY            |
| nom_produit        | VARCHAR(255)   | NOT NULL                         |
| reference_produit  | VARCHAR(50)    |                                  |
| quantite           | INT            | NOT NULL                         |
| prix_unitaire      | DECIMAL(10, 2) | NOT NULL                         |
| prix_total         | DECIMAL(10, 2) | NOT NULL                         |
| taux_taxe          | DECIMAL(5, 2)  | DEFAULT 20.00                    |
| montant_taxe       | DECIMAL(10, 2) | NOT NULL                         |

### Table `panier`
| Colonne         | Type          | Contraintes                      |
|-----------------|---------------|----------------------------------|
| id              | INT           | AUTO_INCREMENT, PRIMARY KEY      |
| utilisateur_id  | INT           | FOREIGN KEY, NULL                |
| session_id      | VARCHAR(255)  |                                  |
| produit_id      | INT           | NOT NULL, FOREIGN KEY            |
| quantite        | INT           | NOT NULL DEFAULT 1               |
| date_ajout      | DATETIME      | DEFAULT CURRENT_TIMESTAMP        |

## Tables de Contenu

### Table `avis`
| Colonne         | Type                              | Contraintes                      |
|-----------------|-----------------------------------|----------------------------------|
| id              | INT                               | AUTO_INCREMENT, PRIMARY KEY      |
| produit_id      | INT                               | NOT NULL, FOREIGN KEY            |
| utilisateur_id  | INT                               | NOT NULL, FOREIGN KEY            |
| note            | INT                               | NOT NULL, CHECK (note BETWEEN 1 AND 5) |
| commentaire     | TEXT                              |                                  |
| date_creation   | DATETIME                          | DEFAULT CURRENT_TIMESTAMP        |
| statut          | ENUM('en_attente','approuve','rejete') | DEFAULT 'en_attente'       |

### Table `pages`
| Colonne           | Type          | Contraintes                      |
|-------------------|---------------|----------------------------------|
| id                | INT           | AUTO_INCREMENT, PRIMARY KEY      |
| titre             | VARCHAR(255)  | NOT NULL                         |
| slug              | VARCHAR(255)  | NOT NULL, UNIQUE                 |
| contenu           | TEXT          | NOT NULL                         |
| meta_description  | VARCHAR(255)  |                                  |
| date_creation     | DATETIME      | DEFAULT CURRENT_TIMESTAMP        |
| date_modification | DATETIME      | ON UPDATE CURRENT_TIMESTAMP      |
| publiee           | BOOLEAN       | DEFAULT TRUE                     |

## Relations

### Relations Utilisateurs
- Un `utilisateur` peut avoir plusieurs `auth_tokens` (relation 1:N)
- Un `utilisateur` peut avoir plusieurs entrées dans `connexions_log` (relation 1:N)
- Un `utilisateur` peut passer plusieurs `commandes` (relation 1:N)
- Un `utilisateur` peut avoir plusieurs produits dans son `panier` (relation 1:N)
- Un `utilisateur` peut laisser plusieurs `avis` (relation 1:N)

### Relations Catalogue
- Une `categorie` peut avoir plusieurs sous-catégories (relation self-référentielle)
- Une `categorie` peut contenir plusieurs `produits` (relation 1:N)
- Une `collection` peut contenir plusieurs `produits` (relation 1:N)
- Un `produit` peut avoir plusieurs `attributs` via `produit_attributs` (relation N:M)
- Un `produit` peut être dans plusieurs `articles_commande` (relation 1:N)
- Un `produit` peut être dans plusieurs `paniers` (relation 1:N)
- Un `produit` peut avoir plusieurs `avis` (relation 1:N)

### Relations Commandes
- Une `commande` contient plusieurs `articles_commande` (relation 1:N)

## Clés Étrangères

- `auth_tokens.user_id` référence `utilisateurs.id` (CASCADE)
- `connexions_log.user_id` référence `utilisateurs.id` (CASCADE)
- `categories.parent_id` référence `categories.id` (SET NULL)
- `produits.categorie_id` référence `categories.id` (SET NULL)
- `produits.collection_id` référence `collections.id` (SET NULL)
- `produit_attributs.produit_id` référence `produits.id` (CASCADE)
- `produit_attributs.attribut_id` référence `attributs.id` (CASCADE)
- `commandes.utilisateur_id` référence `utilisateurs.id` (CASCADE)
- `articles_commande.commande_id` référence `commandes.id` (CASCADE)
- `articles_commande.produit_id` référence `produits.id` (NO ACTION)
- `panier.utilisateur_id` référence `utilisateurs.id` (CASCADE)
- `panier.produit_id` référence `produits.id` (CASCADE)
- `avis.produit_id` référence `produits.id` (CASCADE)
- `avis.utilisateur_id` référence `utilisateurs.id` (CASCADE)