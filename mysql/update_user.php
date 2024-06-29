<?php
require_once 'connexion_bdd.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    $userId = $data->userId;
    $username = $data->username;
    $email = $data->email;
    $droit = $data->droit;

    try {
        $dbh = connexion_bdd();

        //maj données utitlisateur
        $sql = $dbh->prepare("UPDATE UTILISATEUR SET Pseudo = :pseudo, Email = :email, Droit =:droit WHERE Utilisateur_ID = :userId");

        $sql->bindParam(':pseudo', $username);
        $sql->bindParam(':email', $email);
        $sql->bindParam(':userId', $userId);
        $sql->bindParam(':droit', $droit);

        $sql->execute();

        $response['success'] = true;
        $response['message'] = 'Utilisateur mis à jour avec succès';
    } catch (PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage();
    }
} else {
    $response['success'] = false;
    $response['message'] = 'La méthode de requête n\'est pas autorisée';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
