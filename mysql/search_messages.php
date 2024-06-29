<?php
include_once '../mysql/connexion_bdd.php';
session_start();

$conn = connexion_bdd();

$query = isset($_GET['query']) ? $_GET['query'] : '';
$groupId = isset($_SESSION['Groupe_ID']) ? $_SESSION['Groupe_ID'] : '';

if ($query !== '' && $groupId !== '') {
    //recherche message
    $sql = "SELECT MESSAGE.Texte, MESSAGE.Date_Envoi, UTILISATEUR.Pseudo 
            FROM MESSAGE 
            JOIN UTILISATEUR ON MESSAGE.Auteur_ID = UTILISATEUR.Utilisateur_ID 
            WHERE MESSAGE.Texte LIKE :query AND MESSAGE.Destinataire_ID = :groupId AND MESSAGE.Type = 'group'";
    
    $stmt = $conn->prepare($sql);
    $searchTerm = "%{$query}%";
    $stmt->bindParam(':query', $searchTerm);
    $stmt->bindParam(':groupId', $groupId);
    $stmt->execute();

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($messages);
} else {
    echo json_encode([]);
}
?>
