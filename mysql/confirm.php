<?php

require_once 'connexion_bdd.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Prepare a select statement
    $sql = $dbh->prepare("SELECT * FROM UTILISATEUR WHERE Code_email = :token");

    // Bind variables to the prepared statement as parameters
    $sql->bindParam(':token', $token);

    // Attempt to execute the prepared statement
    if ($sql->execute()) {
        // Check if token exists
        if ($sql->rowCount() == 1) {
            // Token exists, activate the account
            $sql = $dbh->prepare("UPDATE UTILISATEUR SET Droit = 1 WHERE Code_email = :token");
            $sql->bindParam(':token', $token);
            $sql->execute();

            echo "Votre compte a été activé avec succès.";
        } else {
            // Token does not exist or is invalid
            echo "Ce lien de confirmation est invalide ou a déjà été utilisé.";
        }
    } else {
        echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
    }
} else {
    // No token parameter in the URL
    echo "Paramètre de jeton invalide.";
}

?>