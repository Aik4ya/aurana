<?php
require '../mysql/connexion_bdd.php';
session_start();

if (isset($_SESSION['Utilisateur_ID'])) {
    $conn = connexion_bdd();
    $sql = "UPDATE UTILISATEUR SET En_Ligne = 0, derniere_connexion = NOW() WHERE Utilisateur_ID = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
    $stmt->execute();
}
?>
