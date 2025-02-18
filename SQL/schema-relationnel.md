# Schéma Relationnel de la Base de Données `elixir_du_temps`

## Tables

### Table `produits`
| Colonne      | Type          | Contraintes                  |
|--------------|---------------|------------------------------|
| id           | INT           | AUTO_INCREMENT, PRIMARY KEY  |
| nom          | VARCHAR(255)  | NOT NULL                     |
| description  | TEXT          |                              |
| prix         | DECIMAL(10, 2)| NOT NULL                     |
| image        | VARCHAR(255)  |                              |
| stock        | INT           | NOT NULL                     |

### Table `utilisateurs`
| Colonne      | Type          | Contraintes                  |
|--------------|---------------|------------------------------|
| id           | INT           | AUTO_INCREMENT, PRIMARY KEY  |
| nom          | VARCHAR(255)  | NOT NULL                     |
| email        | VARCHAR(255)  | NOT NULL, UNIQUE             |
| mot_de_passe | VARCHAR(255)  | NOT NULL                     |

### Table `commandes`
| Colonne         | Type          | Contraintes                  |
|-----------------|---------------|------------------------------|
| id              | INT           | AUTO_INCREMENT, PRIMARY KEY  |
| utilisateur_id  | INT           | NOT NULL, FOREIGN KEY        |
| date_commande   | DATETIME      | DEFAULT CURRENT_TIMESTAMP    |
| total           | DECIMAL(10, 2)| NOT NULL                     |

### Table `articles_commande`
| Colonne         | Type          | Contraintes                  |
|-----------------|---------------|------------------------------|
| id              | INT           | AUTO_INCREMENT, PRIMARY KEY  |
| commande_id     | INT           | NOT NULL, FOREIGN KEY        |
| produit_id      | INT           | NOT NULL, FOREIGN KEY        |
| quantite        | INT           | NOT NULL                     |
| prix            | DECIMAL(10, 2)| NOT NULL                     |

### Table `panier`
| Colonne         | Type          | Contraintes                  |
|-----------------|---------------|------------------------------|
| id              | INT           | AUTO_INCREMENT, PRIMARY KEY  |
| utilisateur_id  | INT           | NOT NULL, FOREIGN KEY        |
| produit_id      | INT           | NOT NULL, FOREIGN KEY        |
| quantite        | INT           | NOT NULL                     |

## Relations

- Un `utilisateur` peut passer plusieurs `commandes`.
- Une `commande` contient plusieurs `articles_commande`.
- Un `produit` peut apparaître dans plusieurs `articles_commande`.
- Un `utilisateur` peut avoir plusieurs `produits` dans son `panier`.
- Un `produit` peut apparaître dans plusieurs `paniers`.

### Clés Étrangères

- `commandes.utilisateur_id` référence `utilisateurs.id`
- `articles_commande.commande_id` référence `commandes.id`
- `articles_commande.produit_id` référence `produits.id`
- `panier.utilisateur_id` référence `utilisateurs.id`
- `panier.produit_id` référence `produits.id`