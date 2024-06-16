<?php
require_once '../mysql/cookies_uid.php';
require_once '../mysql/connexion_bdd.php';

session_start();

if (isset($_GET['project_id']) && isset($_GET['user_id'])) {
    $projectID = $_GET['project_id'];
    $userID = $_GET['user_id'];
    $conn = connexion_bdd();

    $stmt = $conn->prepare("UPDATE est_membre_projet SET admin = 1 WHERE Projet_ID = :projectID AND Utilisateur_ID = :userID");
    $stmt->bindParam(':projectID', $projectID);
    $stmt->bindParam(':userID', $userID);
    $success = $stmt->execute();

    echo json_encode(['success' => $success]);
}
?>
