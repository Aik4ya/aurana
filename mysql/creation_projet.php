<?php
require_once '../mysql/connexion_bdd.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupeID = $_POST['groupeID'];
    $projectName = $_POST['projectName'];
    $projectStatus = $_POST['projectStatus'];
    $projectPriority = $_POST['projectPriority'];
    $projectDeadline = $_POST['projectDeadline'];

    if (empty($projectName) || empty($projectStatus) || empty($projectPriority) || empty($projectDeadline)) {
        header("Location: ../pages/main.php?error=empty_fields");
        exit();
    }

    try {
        $conn = connexion_bdd();
        
        // Commencer une transaction
        $conn->beginTransaction();

        // Insertion dans la table PROJET
        $sql = "INSERT INTO PROJET (nom, status, priorite, deadline, id_groupe) VALUES (:nom, :status, :priorite, :deadline, :id_groupe)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nom', $projectName);
        $stmt->bindParam(':status', $projectStatus);
        $stmt->bindParam(':priorite', $projectPriority);
        $stmt->bindParam(':deadline', $projectDeadline);
        $stmt->bindParam(':id_groupe', $groupeID);
        $stmt->execute();

        // Récupérer l'ID du projet nouvellement créé
        $projectID = $conn->lastInsertId();

        // Insertion dans la table est_membre_projet
        $sql = "INSERT INTO est_membre_projet (Utilisateur_ID, Projet_ID, admin) VALUES (:user_id, :project_id, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
        $stmt->bindParam(':project_id', $projectID);
        $stmt->execute();

        // Valider la transaction
        $conn->commit();

        header("Location: ../pages/main.php?groupe={$_SESSION['Groupe']}");
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollBack();
        header("Location: ../pages/main.php?error=db_error");
    }
}
?>