<?php
require_once '../mysql/connexion_bdd.php';

if (isset($_POST['ip'])) {
    $ip = $_POST['ip'];

    try {
        $dbh = connexion_bdd();

        $stmt = $dbh->prepare("INSERT INTO est_banip (adresse_ip) VALUES (:ip)");

        $stmt->bindParam(':ip', $ip);

        $stmt->execute();

        http_response_code(200);
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Adresse IP non fournie.";
}
?>
