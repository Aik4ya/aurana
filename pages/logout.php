<?php
require_once '../mysql/connexion_bdd.php';

session_start();

$conn = connexion_bdd();

$sql_update = $conn->prepare("UPDATE UTILISATEUR SET derniere_connexion = NOW() WHERE Utilisateur_ID = :Utilisateur_ID");
$sql_update->bindParam(':Utilisateur_ID', $_SESSION['Utilisateur_ID']);
$sql_update->execute();

session_destroy();
header("Location: ../pages/login.php");
exit();
?>
