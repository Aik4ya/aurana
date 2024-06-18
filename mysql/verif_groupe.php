<?php
// Activer l'affichage des erreurs pour le débogage
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Inclure le fichier de connexion à la base de données
require_once 'connexion_bdd.php';

// Démarrer la session
session_start();

// Fonction pour vérifier la session et le groupe de l'utilisateur
function verif_groupe() {
    $conn = connexion_bdd();

    // Vérifier l'expiration de la session
    if (!isset($_SESSION['expiration']) || time() > $_SESSION['expiration']) {
        session_destroy();
        header("Location: ../pages/login.php?statut=session_expiree");
        exit();
    }

    // Vérifier si le groupe est défini sur 'none'
    if (isset($_GET['groupe']) && $_GET['groupe'] == 'none') {
        header("Location: ../pages/choisir_groupe.php");
        exit();
    }

    // Mise à jour de la dernière connexion de l'utilisateur
    $sql_update = $conn->prepare("UPDATE UTILISATEUR SET derniere_connexion = NOW() WHERE Utilisateur_ID = :Utilisateur_ID");
    $sql_update->bindParam(':Utilisateur_ID', $_SESSION['Utilisateur_ID']);
    $sql_update->execute();
}

// Appel de la fonction de vérification de groupe
verif_groupe();
?>
