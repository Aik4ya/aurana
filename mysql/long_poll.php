<?php
require '../mysql/connexion_bdd.php';
session_start();

if (isset($_SESSION['Groupe_ID'])) {
    $conn = connexion_bdd();
    $groupe_id = $_SESSION['Groupe_ID'];

    $lastMessageId = isset($_GET['last_message_id']) ? (int)$_GET['last_message_id'] : 0;

    $sql = "SELECT MESSAGE.Texte, MESSAGE.Date_Envoi, UTILISATEUR.Pseudo as Auteur_Nom, MESSAGE.Message_ID 
            FROM MESSAGE 
            JOIN UTILISATEUR ON MESSAGE.Auteur_ID = UTILISATEUR.Utilisateur_ID 
            WHERE MESSAGE.Destinataire_ID = :groupe_id AND MESSAGE.Message_ID > :last_message_id
            ORDER BY MESSAGE.Date_Envoi ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':groupe_id', $groupe_id, PDO::PARAM_INT);
    $stmt->bindParam(':last_message_id', $lastMessageId, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($messages) > 0) {
        echo json_encode($messages);
    } else {
        echo json_encode([]);
    }
}
?>
