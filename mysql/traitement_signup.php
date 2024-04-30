<?php

#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
#DE L'OR EN BARRE

require_once 'connexion_bdd.php';
require_once 'cookies_uid.php';

function creation_utilisateur($dbh) {
    try {
        $sql = $dbh->prepare('INSERT INTO UTILISATEUR (Pseudo, Identifiant, Mot_de_passe, Email, Droit) VALUES (:Username, :Login, :Password, :Email, :Droit)');
        
        $Username = $_POST['username'];
        $Login = $Username;
        $Password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $Email = $_POST['email'];
        $Droits = 0;

        // Validation des données, échapper les valeurs pour éviter les injections SQL
        
        $sql->bindParam(':Username', $Username);
        $sql->bindParam(':Login', $Login);
        $sql->bindParam(':Password', $Password);
        $sql->bindParam(':Email', $Email);
        $sql->bindParam(':Droit', $Droits);

        $sql->execute();
        
        $expiration = time() + (5*60); #trouver un truc pour back
        $Utilisateur_ID = $dbh->lastInsertID();

        session_start();
        $_SESSION['expiration'] = $expiration;
        $_SESSION['Utilisateur_ID'] = $Utilisateur_ID;
        $_SESSION['Droit'] = $Droits;
        $_SESSION['Pseudo'] = $Username;

        header("Location: ../pages/main.php?status=success");     
        exit();

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}


creation_utilisateur(connexion_bdd());
