<?php
require_once '../mysql/connexion_bdd.php';
$conn = connexion_bdd();

if (isset($_GET['project_id'])) {
    $projectId = $_GET['project_id'];
    $groupeId = $_SESSION['Groupe_ID'];

    $sql = "SELECT UTILISATEUR.Utilisateur_ID, UTILISATEUR.Pseudo 
            FROM UTILISATEUR 
            JOIN est_membre ON UTILISATEUR.Utilisateur_ID = est_membre.Utilisateur_ID 
            WHERE est_membre.GROUPE = :groupe_id 
            AND UTILISATEUR.Utilisateur_ID NOT IN (
                SELECT Utilisateur_ID FROM est_membre_projet WHERE Projet_ID = :project_id
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':groupe_id', $groupeId);
    $stmt->bindParam(':project_id', $projectId);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
}
?>
