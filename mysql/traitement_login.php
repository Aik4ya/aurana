<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure les fichiers nécessaires pour la connexion à la base de données et la gestion des cookies
require 'connexion_bdd.php';
require 'cookies_uid.php';

// Démarrer la session pour gérer les informations de l'utilisateur
session_start();

function connexion_utilisateur($dbh) {
    try {
        // Vérifier si les champs email et mot de passe sont définis dans la requête POST
        if (isset($_POST['email']) && isset($_POST['password'])) {
            // Sécuriser les entrées utilisateur pour éviter les attaques XSS
            $email = htmlspecialchars($_POST['email']);
            $Mot_de_passe = $_POST['password'];

            // Préparer la requête SQL pour vérifier l'email dans la base de données
            $sql = $dbh->prepare('SELECT Utilisateur_ID, Mot_de_passe, Droit, Pseudo FROM UTILISATEUR WHERE Email = :Email');
            $sql->bindParam(':Email', $email);
            $sql->execute();

            // Récupérer le résultat de la requête
            $resultat = $sql->fetch(PDO::FETCH_ASSOC);

            // Vérifier si le mot de passe fourni correspond au mot de passe hashé dans la base de données
            if ($resultat && password_verify($Mot_de_passe, $resultat['Mot_de_passe'])) {
                // Si les informations sont correctes, récupérer l'ID de l'utilisateur
                $Utilisateur_ID = $resultat['Utilisateur_ID'];

                // Mettre à jour la date de dernière connexion de l'utilisateur
                $sql_update = $dbh->prepare("UPDATE UTILISATEUR SET derniere_connexion = NOW() WHERE Utilisateur_ID = :Utilisateur_ID");
                $sql_update->bindParam(':Utilisateur_ID', $Utilisateur_ID);
                $sql_update->execute();

                // Définir la durée d'expiration de la session à 30 jours
                $expiration = time() + (30 * 24 * 60 * 60);

                // Stocker les informations de l'utilisateur dans la session
                $_SESSION['expiration'] = $expiration;
                $_SESSION['Utilisateur_ID'] = $resultat['Utilisateur_ID'];
                $_SESSION['Droit'] = $resultat['Droit'];
                $_SESSION['Pseudo'] = $resultat['Pseudo'];
                $_SESSION['Email'] = $email;

                // Réinitialiser le nombre d'essais de connexion
                $_SESSION['essais'] = 0;

                // Rediriger l'utilisateur vers la page principale
                header("Location: ../pages/main.php");
                exit();
            } else {
                // Si les informations sont incorrectes, augmenter le nombre d'essais de connexion et rediriger vers la page de connexion avec un message d'erreur
                $_SESSION['essais']++;
                header("Location: ../pages/login.php?statut=echec");
                exit();
            }
        } else {
            // Si les champs email ou mot de passe ne sont pas définis, rediriger vers la page de connexion
            header("Location: ../pages/login.php");
            exit();
        }
    } catch (PDOException $e) {
        // En cas d'erreur PDO, afficher le message d'erreur et rediriger vers une page d'erreur
        echo 'Erreur PDO : ' . $e->getMessage();
        header("Location: ../pages/error.php");
        exit();
    }
}

// Appeler la fonction de connexion avec l'objet PDO de la base de données
connexion_utilisateur(connexion_bdd());
?>
