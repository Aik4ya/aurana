<?php

require_once '../mysql/connexion_bdd.php';
session_start();

$conn = connexion_bdd();
$group_id = $_SESSION['Groupe_ID'];
$query = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_STRING);

//recherche fichier
$sql = "SELECT * FROM FICHIER WHERE Groupe_ID = :group_id AND Adresse LIKE :query";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
$stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($files);
?>
