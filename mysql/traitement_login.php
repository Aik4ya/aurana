<?php
require 'connexion_bdd.php';
require 'cookies_uid.php';

function connexion_utilisateur($dbh)
{  
    try {
        $sql = $dbh->prepare('SELECT Utilisateur_ID FROM UTILISATEUR WHERE Email = :Email AND Mot_de_passe = :Mot_de_passe;');
        $sql->bindParam(':Email', $email);
        $sql->bindParam(':Mot_de_passe', $Mot_de_passe);

        $email = $_POST['email'];

        $Mot_de_passe = $_POST['password'];


        $sql->execute();
        $resultat = $sql->fetch(PDO::FETCH_ASSOC);



        if (!$resultat) 
        {     
            // Login échoué, redirigez vers une autre page avec un message d'échec     
            header("Location: ../pages/login.php?status=failed");     
            exit(); 
        } 

        else 
        {   
            $Utilisateur_ID = $resultat['Utilisateur_ID'];
            $expiration = time() + (30 * 24 * 60 * 60); #trouver un truc pour back
            creation_cookie_uid($Utilisateur_ID,$expiration);

            header("Location: ../pages/main.php?status=success");
            exit(); 
        }
    } catch (PDOException $e) {
        var_dump($e); // Afficher les détails de l'erreur
        // Gérer l'erreur : rediriger l'utilisateur vers une page d'erreur
        header("Location: ../pages/error.php");
        exit();
    }
}

connexion_utilisateur(connexion_bdd());