<?php
require '../mysql/connexion_bdd.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouveau_message']) && isset($_SESSION['Groupe_ID']) && isset($_SESSION['Utilisateur_ID'])) {
    $conn = connexion_bdd();
    $message = trim($_POST['nouveau_message']);
    $auteur_id = $_SESSION['Utilisateur_ID'];
    $groupe_id = $_SESSION['Groupe_ID'];

    if (!empty($message)) {
        //envoi message
        $sql = "INSERT INTO MESSAGE (Texte, Date_Envoi, Auteur_ID, Destinataire_ID) VALUES (:message, NOW(), :auteur_id, :groupe_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':auteur_id', $auteur_id, PDO::PARAM_INT);
        $stmt->bindParam(':groupe_id', $groupe_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>