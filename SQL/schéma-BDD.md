# Schéma Relationnel de la Base de Données pour le Site de Vente de Montres de Luxe

## Tables

### Utilisateurs
| Colonne       | Type         | Description                |
|---------------|--------------|----------------------------|
| id            | INT          | Identifiant unique         |
| nom           | VARCHAR(100) | Nom de l'utilisateur       |
| email         | VARCHAR(100) | Email de l'utilisateur     |
| mot_de_passe  | VARCHAR(255) | Mot de passe de l'utilisateur |
| date_creation | DATETIME     | Date de création du compte |

### Collections
| Colonne       | Type         | Description                |
|---------------|--------------|----------------------------|
| id            | INT          | Identifiant unique         |
| nom           | VARCHAR(100) | Nom de la collection       |
| description   | TEXT         | Description de la collection |

### Montres
| Colonne       | Type         | Description                |
|---------------|--------------|----------------------------|
| id            | INT          | Identifiant unique         |
| nom           | VARCHAR(100) | Nom de la montre           |
| collection_id | INT          | Référence à la collection  |
| prix          | DECIMAL(10,2)| Prix de la montre          |
| description   | TEXT         | Description de la montre   |

### DescriptionProduits
| Colonne       | Type         | Description                |
|---------------|--------------|----------------------------|
| id            | INT          | Identifiant unique         |
| montre_id     | INT          | Référence à la montre      |
| details       | TEXT         | Détails du produit         |

### APropos
| Colonne       | Type         | Description                |
|---------------|--------------|----------------------------|
| id            | INT          | Identifiant unique         |
| contenu       | TEXT         | Contenu de la page À propos |

### Organigramme
| Colonne       | Type         | Description                |
|---------------|--------------|----------------------------|
| id            | INT          | Identifiant unique         |
| nom           | VARCHAR(100) | Nom de la personne         |
| poste         | VARCHAR(100) | Poste de la personne       |
| description   | TEXT         | Description du poste       |

### Panier
| Colonne       | Type         | Description                |
|---------------|--------------|----------------------------|
| id            | INT          | Identifiant unique         |
| utilisateur_id| INT          | Référence à l'utilisateur  |
| montre_id     | INT          | Référence à la montre      |
| quantite      | INT          | Quantité de la montre      |
| date_ajout    | DATETIME     | Date d'ajout au panier     |

## Relations
- Une `Collection` peut avoir plusieurs `Montres`.
- Une `Montre` peut avoir plusieurs `DescriptionProduits`.
- Un `Utilisateur` peut avoir plusieurs `Paniers`.
- Un `Panier` peut contenir plusieurs `Montres`.
