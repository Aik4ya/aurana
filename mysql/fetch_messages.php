<?php
include_once 'connexion_bdd.php';
$conn = connexion_bdd();
session_start();

$groupe_id = $_SESSION['Groupe_ID'];

$sql = "SELECT MESSAGE.Texte, MESSAGE.Date_Envoi, UTILISATEUR.Pseudo, UTILISATEUR.avatar 
        FROM MESSAGE 
        JOIN UTILISATEUR ON MESSAGE.Auteur_ID = UTILISATEUR.Utilisateur_ID 
        WHERE MESSAGE.Destinataire_ID = :groupe_id
        ORDER BY MESSAGE.Date_Envoi DESC";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':groupe_id', $groupe_id);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>
