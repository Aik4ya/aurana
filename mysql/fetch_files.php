<?php

require_once '../mysql/connexion_bdd.php';
session_start();

$conn = connexion_bdd();
$group_id = $_SESSION['Groupe_ID'];

$sql = "SELECT * FROM FICHIER WHERE Groupe_ID = :group_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($files);
?>
