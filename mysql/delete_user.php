<?php
require_once 'connexion_bdd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    $userId = $data->userId;

    try {
        $dbh = connexion_bdd();

        $sql = $dbh->prepare("DELETE FROM UTILISATEUR WHERE Utilisateur_ID = :userId");

        $sql->bindParam(':userId', $userId);

        $sql->execute();

        http_response_code(200);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage()));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "La méthode de requête n'est pas autorisée"));
}
?>
