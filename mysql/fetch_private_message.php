<?php
require '../mysql/connexion_bdd.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouveau_message']) && isset($_SESSION['Utilisateur_ID'])) {
    $conn = connexion_bdd();
    $message = trim($_POST['nouveau_message']);
    $auteur_id = $_SESSION['Utilisateur_ID'];
    $type = $_POST['type'];
    $destinataire_id = ($type === 'private') ? $_POST['recipient_id'] : $_SESSION['Groupe_ID'];

    if (!empty($message)) {
        $sql = "INSERT INTO MESSAGE (Texte, Date_Envoi, Auteur_ID, Destinataire_ID, Type, Destinataire_Utilisateur_ID) 
                VALUES (:message, NOW(), :auteur_id, :destinataire_id, :type, :destinataire_utilisateur_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':auteur_id', $auteur_id, PDO::PARAM_INT);
        $stmt->bindParam(':destinataire_id', ($type === 'group' ? $destinataire_id : null), PDO::PARAM_INT);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':destinataire_utilisateur_id', ($type === 'private' ? $destinataire_id : null), PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
