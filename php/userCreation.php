<?php
try {
    $connexionPDO = new PDO('mysql:host=127.0.0.1;dbname=elixir_du_temps;charset=UTF8', "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

/**
 * Valide le nom.
 *
 * @param string $nom Le nom à valider.
 * @return bool Vrai si le nom est valide, faux sinon.
 */
function validerNom($nom) {
    return !empty(trim($nom));
}

/**
 * Valide l'email.
 *
 * @param string $email L'email à valider.
 * @return bool Vrai si l'email est valide, faux sinon.
 */
function validerEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Valide le mot de passe.
 *
 * @param string $motDePasse Le mot de passe à valider.
 * @return bool Vrai si le mot de passe est valide, faux sinon.
 */
function validerMotDePasse($motDePasse) {
    // Vérifie que le mot de passe a au moins 8 caractères
    return strlen($motDePasse) >= 8;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $motDePasse = $_POST['password']; // Corrected to match the form field name

    if (!validerNom($nom)) {
        die("Le nom ne peut pas être vide.");
    }

    if (!validerEmail($email)) {
        die("L'email n'est pas valide.");
    }

    if (!validerMotDePasse($motDePasse)) {
        die("Le mot de passe doit contenir au moins 8 caractères.");
    }

    try {
        $requetePreparee = $connexionPDO->prepare(
            'INSERT INTO `utilisateurs` (`nom`, `email`, `mot_de_passe`)
            VALUES (:nom, :email, :motDePasse);'
        );

        $requetePreparee->bindParam(':nom', $nom);
        $motDePasseHaché = password_hash($motDePasse, PASSWORD_DEFAULT);
        $requetePreparee->bindParam(':motDePasse', $motDePasseHaché);
        $requetePreparee->bindParam(':email', $email);

        if ($requetePreparee->execute()) {
            $idUtilisateur = $connexionPDO->lastInsertId();
            echo "L'utilisateur a été créé avec succès et porte le numéro $idUtilisateur";
        } else {
            echo "Erreur lors de la création de l'utilisateur.";
        }
    } catch (Exception $e) {
        echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
    }
}
?>
