<?php
require_once '../mysql/connexion_bdd.php';
$conn = connexion_bdd();

if (isset($_GET['project_id'])) {
    $projectId = $_GET['project_id'];
    $sql = "SELECT UTILISATEUR.Pseudo
            FROM UTILISATEUR
            JOIN est_membre_projet ON UTILISATEUR.Utilisateur_ID = est_membre_projet.Utilisateur_ID
            WHERE est_membre_projet.Projet_ID = :project_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':project_id', $projectId);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($members);
}
?>
