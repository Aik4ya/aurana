<?php
require_once '../mysql/connexion_bdd.php';

if (isset($_POST['project_id']) && isset($_POST['projectMembers'])) {
    $projectID = $_POST['project_id'];
    $members = $_POST['projectMembers'];
    $conn = connexion_bdd();
    $success = true;

    //parcourir les membres pour ajout
    foreach ($members as $memberID) {
        $stmt = $conn->prepare("INSERT INTO est_membre_projet (Utilisateur_ID, Projet_ID) VALUES (:memberID, :projectID)");
        $stmt->bindParam(':memberID', $memberID);
        $stmt->bindParam(':projectID', $projectID);
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }

    echo json_encode(['success' => $success]);
}
?>
