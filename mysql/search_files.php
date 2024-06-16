<?php
require_once '../mysql/connexion_bdd.php';
session_start();

$conn = connexion_bdd();
$query = isset($_GET['query']) ? $_GET['query'] : '';
$groupId = isset($_SESSION['Groupe_ID']) ? $_SESSION['Groupe_ID'] : '';

if ($query !== '' && $groupId !== '') {
    $sql = "SELECT FICHIER.Fichier_ID, FICHIER.Adresse, FICHIER.Date_Stock, FICHIER.Utilisateur_id
            FROM FICHIER
            WHERE FICHIER.Adresse LIKE :query AND FICHIER.Groupe_ID = :groupId";
    
    $stmt = $conn->prepare($sql);
    $searchTerm = "%{$query}%";
    $stmt->bindParam(':query', $searchTerm);
    $stmt->bindParam(':groupId', $groupId);
    $stmt->execute();

    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($files);
} else {
    echo json_encode([]);
}
?>
