<?php
include_once 'connexion_bdd.php';
$conn = connexion_bdd();
session_start();

$nouveau_message = isset($_POST['nouveau_message']) ? $_POST['nouveau_message'] : '';
$groupe_id = isset($_POST['groupe_id']) ? $_POST['groupe_id'] : 0;
$utilisateur_id = $_SESSION['Utilisateur_ID'];

if ($nouveau_message && $groupe_id) {
    $sql = "INSERT INTO MESSAGE (Texte, Date_Envoi, Auteur_ID, Destinataire_ID) VALUES (:texte, NOW(), :auteur_id, :destinataire_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':texte', $nouveau_message);
    $stmt->bindValue(':auteur_id', $utilisateur_id);
    $stmt->bindValue(':destinataire_id', $groupe_id);
    $stmt->execute();

    $message_id = $conn->lastInsertId();

    $sql = "SELECT MESSAGE.Texte, MESSAGE.Date_Envoi, UTILISATEUR.Pseudo, UTILISATEUR.avatar 
            FROM MESSAGE 
            JOIN UTILISATEUR ON MESSAGE.Auteur_ID = UTILISATEUR.Utilisateur_ID 
            WHERE MESSAGE.Message_ID = :message_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':message_id', $message_id);
    $stmt->execute();
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'message' => $message]);
} else {
    echo json_encode(['success' => false, 'message' => 'Message or group ID missing']);
}
?>
