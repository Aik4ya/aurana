<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connexion_bdd.php';
require 'cookies_uid.php';

function connexion_utilisateur($dbh)
{  
    try {
        // Vérifier si les paramètres POST existent
        if(isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $Mot_de_passe = $_POST['password'];

            // Préparer la requête SQL
            $sql = $dbh->prepare('SELECT Utilisateur_ID, Mot_de_passe, Droit, Pseudo FROM UTILISATEUR WHERE Email = :Email;');
            $sql->bindParam(':Email', $email);

            // Exécuter la requête SQL
            $sql->execute();
            $resultat = $sql->fetch();

            // Vérifier si l'utilisateur existe et si le mot de passe est correct
            if ($resultat && password_verify($Mot_de_passe, $resultat['Mot_de_passe']))
            {     
                // Créer le cookie d'identification
                $Utilisateur_ID = $resultat['Utilisateur_ID'];
                $expiration = time() + (5*60); // 30 jours 30 * 24 * 60 * 60
                session_start();
                $_SESSION['expiration'] = $expiration;
                $_SESSION['Utilisateur_ID'] = $resultat['Utilisateur_ID'];
                $_SESSION['Droit'] = $resultat['Droit'];
                $_SESSION['Pseudo'] = $resultat['Pseudo'];

                // Redirection vers la page principale avec un message de succès
                header("Location: ../pages/main.php?status=success");
                exit(); 
            } 
            else 
            {   
                // Redirection vers la page de login avec un message d'échec
                header("Location: ../pages/login.php?status=failed");
                exit(); 
            }
        } else {
            // Redirection vers la page de login si les paramètres POST sont manquants
            header("Location: ../pages/login.php?status=missing_data");
            exit();
        }
    } catch (PDOException $e) {
        // Afficher les détails de l'erreur PDO
        echo 'Erreur PDO : ' . $e->getMessage();
        // Redirection vers une page d'erreur
        header("Location: ../pages/error.php");
        exit();
    }
}

connexion_utilisateur(connexion_bdd());
