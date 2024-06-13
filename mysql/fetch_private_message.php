<?php
require '../mysql/connexion_bdd.php';
session_start();

if (isset($_SESSION['Utilisateur_ID']) && isset($_GET['recipient_id'])) {
    $conn = connexion_bdd();
    $user_id = $_SESSION['Utilisateur_ID'];
    $recipient_id = $_GET['recipient_id'];

    $sql = "SELECT MESSAGE.Texte, MESSAGE.Date_Envoi, UTILISATEUR.Pseudo 
            FROM MESSAGE 
            JOIN UTILISATEUR ON MESSAGE.Auteur_ID = UTILISATEUR.Utilisateur_ID 
            WHERE (MESSAGE.Auteur_ID = :user_id AND MESSAGE.Destinataire_Utilisateur_ID = :recipient_id)
               OR (MESSAGE.Auteur_ID = :recipient_id AND MESSAGE.Destinataire_Utilisateur_ID = :user_id)
            ORDER BY MESSAGE.Date_Envoi DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':recipient_id', $recipient_id, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages);
}
?>
