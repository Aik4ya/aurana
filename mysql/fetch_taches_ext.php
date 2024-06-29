<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once "../mysql/connexion_bdd.php";

$bdd = connexion_bdd();

$sql = "SELECT Texte, Description FROM TACHE INNER JOIN es_assigner ON TACHE.Tache_ID = es_assigner.Tache_ID WHERE (Date_Tache = CURDATE() AND es_assigner.Utilisateur_ID = :id) AND TACHE.done = 0";
$stmt = $bdd->prepare($sql);
$stmt->bindParam(':id', $_COOKIE['id'], PDO::PARAM_INT);

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>
