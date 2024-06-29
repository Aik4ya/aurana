<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connexion_bdd.php';
require 'cookies_uid.php';

session_start();

function connexion_utilisateur($dbh) {
    try {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = htmlspecialchars($_POST['email']);
            $Mot_de_passe = $_POST['password'];

            //récup combo email/mdp
            $sql = $dbh->prepare('SELECT Utilisateur_ID, Mot_de_passe, Droit, Pseudo FROM UTILISATEUR WHERE Email = :Email');
            $sql->bindParam(':Email', $email);
            $sql->execute();

            $resultat = $sql->fetch(PDO::FETCH_ASSOC);

            //vérif si hash correspond
            if ($resultat && password_verify($Mot_de_passe, $resultat['Mot_de_passe'])) {
                $Utilisateur_ID = $resultat['Utilisateur_ID'];

                $sql_update = $dbh->prepare("UPDATE UTILISATEUR SET derniere_connexion = NOW() WHERE Utilisateur_ID = :Utilisateur_ID");
                $sql_update->bindParam(':Utilisateur_ID', $Utilisateur_ID);
                $sql_update->execute();

                $expiration = time() + (30 * 24 * 60 * 60); // back ??? 

                $_SESSION['expiration'] = $expiration;
                $_SESSION['Utilisateur_ID'] = $resultat['Utilisateur_ID'];

                setcookie('id', $resultat['Utilisateur_ID'], ($expiration * 10), '/', null, false, true);

                $_SESSION['Droit'] = $resultat['Droit'];
                $_SESSION['Pseudo'] = $resultat['Pseudo'];
                $_SESSION['Email'] = $email;

                $_SESSION['essais'] = 0;

                //connexion ok
                header("Location: ../pages/main.php");
                exit();
            } else {

                //echec connexion, augmente le nb d'essais pour anti brute force
                $_SESSION['essais']++;
                header("Location: ../pages/login.php?statut=echec");
                exit();
            }
        } else {
            //champs vides
            header("Location: ../pages/login.php");
            exit();
        }
    } catch (PDOException $e) {
        echo 'Erreur PDO : ' . $e->getMessage();
        header("Location: ../pages/error.php");
        exit();
    }
}

connexion_utilisateur(connexion_bdd());
?>
