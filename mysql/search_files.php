<?php

require_once '../mysql/connexion_bdd.php';
session_start();

// Connexion à la base de données
$conn = connexion_bdd();
$group_id = $_SESSION['Groupe_ID'];
$query = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_STRING);

// Préparation et exécution de la requête pour rechercher les fichiers
$sql = "SELECT * FROM FICHIER WHERE Groupe_ID = :group_id AND Adresse LIKE :query";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
$stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Envoi des résultats de la recherche en réponse au format JSON
echo json_encode($files);
?>
