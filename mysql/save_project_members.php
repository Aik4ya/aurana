<?php
require_once '../mysql/connexion_bdd.php';
$conn = connexion_bdd();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectId = $_POST['projectId'];
    $members = $_POST['projectMembers'];

    foreach ($members as $memberId) {
        $sql = "INSERT INTO est_membre_projet (Utilisateur_ID, Projet_ID, admin) VALUES (:user_id, :project_id, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $memberId);
        $stmt->bindParam(':project_id', $projectId);
        $stmt->execute();
    }

    echo json_encode(['success' => true, 'projectId' => $projectId]);
}
?>
