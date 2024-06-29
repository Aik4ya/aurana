<?php
// Activer l'affichage des erreurs pour le débogage
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once 'connexion_bdd.php';

session_start();

function verif_groupe() {
    $conn = connexion_bdd();

    if (!isset($_SESSION['expiration']) || time() > $_SESSION['expiration']) {
        session_destroy();
        header("Location: ../pages/login.php?statut=session_expiree");
        exit();
    }

    if (isset($_GET['groupe']) && $_GET['groupe'] == 'none') {
        header("Location: ../pages/choisir_groupe.php");
        exit();
    }

    $sql_update = $conn->prepare("UPDATE UTILISATEUR SET derniere_connexion = NOW() WHERE Utilisateur_ID = :Utilisateur_ID");
    $sql_update->bindParam(':Utilisateur_ID', $_SESSION['Utilisateur_ID']);
    $sql_update->execute();
}

verif_groupe();
?>
