<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connexion_bdd.php';
require 'cookies_uid.php';

session_start();
$_SESSION['essais']++;

function connexion_utilisateur($dbh)
{  
    try {

        if(isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $Mot_de_passe = $_POST['password'];

            $sql = $dbh->prepare('SELECT Utilisateur_ID, Mot_de_passe, Droit, Pseudo FROM UTILISATEUR WHERE Email = :Email;');
            $sql->bindParam(':Email', $email);

            $sql->execute();
            $resultat = $sql->fetch();

            if ($resultat && password_verify($Mot_de_passe, $resultat['Mot_de_passe']))
            {     
                $Utilisateur_ID = $resultat['Utilisateur_ID'];

                // Mise à jour de la date de dernière connexion
                $sql_update = $dbh->prepare("UPDATE UTILISATEUR SET derniere_connexion = NOW() WHERE Utilisateur_ID = :Utilisateur_ID");
                $sql_update->bindParam(':Utilisateur_ID', $Utilisateur_ID);
                $sql_update->execute();

                // Démarre la session et redirige l'utilisateur vers la page principale
                $expiration = time() + (30 * 24 * 60 * 60); // 30 jours 30 * 24 * 60 * 60 (trouver un truc pour paramétrer dans back)
                
                $_SESSION['expiration'] = $expiration;
                $_SESSION['Utilisateur_ID'] = $resultat['Utilisateur_ID'];
                $_SESSION['Droit'] = $resultat['Droit'];
                $_SESSION['Pseudo'] = $resultat['Pseudo'];

                header("Location: ../pages/main.php");
                $_SESSION['essais'] = 0;
                exit(); 
            } 
            else 
            {   
                header("Location: ../pages/login.php?statut=echec");
                exit(); 
            }
        } else {
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
