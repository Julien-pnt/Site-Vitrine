<?php

// Database connection parameters
$host = 'localhost';
$dbname = 'elixir_du_temps';
$username = 'root';
$password = '';

try {
    // Establish a connection to the MySQL database using PDO
    $connexionPDO = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $connexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Function to sanitize user input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Prompt the user to enter a name
echo "\nNom :";
$nom = sanitizeInput(readline());

// Prompt the user to enter an email
echo "\nEmail :";
$email = sanitizeInput(readline());

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Format d'email invalide.");
}

// Prompt the user to enter a password
echo "\nMot de passe :";
$motDePasse = sanitizeInput(readline());

try {
    // Prepare an SQL statement for inserting a new user into the 'utilisateurs' table
    $requetePreparee = $connexionPDO->prepare(
        'INSERT INTO `utilisateurs` (`nom`, `email`, `mot_de_passe`)
            VALUES (:paramNom, :paramEmail, :paramMotDePasse);'
    );

    // Bind the parameters to the user input
    $requetePreparee->bindParam('paramNom', $nom);
    $requetePreparee->bindParam('paramEmail', $email);

    // Hash the password using the default algorithm and bind the hashed password parameter
    $motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);
    $requetePreparee->bindParam('paramMotDePasse', $motDePasseHache);

    // Execute the prepared statement and store the result (boolean indicating the success of the query)
    $reponse = $requetePreparee->execute();

    // Retrieve the ID of the last inserted user
    $idUtilisateur = $connexionPDO->lastInsertId();

    // Output the ID of the newly created user
    echo "\nL'utilisateur porte le numéro $idUtilisateur";
} catch (PDOException $e) {
    die("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
}