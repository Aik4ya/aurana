<?php
require_once '../mysql/connexion_bdd.php';

if (isset($_GET['project_id'])) {
    $projectID = $_GET['project_id'];
    $conn = connexion_bdd();

    // Fetch project members
    $stmt = $conn->prepare("SELECT Pseudo FROM UTILISATEUR JOIN est_membre_projet ON UTILISATEUR.Utilisateur_ID = est_membre_projet.Utilisateur_ID WHERE Projet_ID = :projectID");
    $stmt->bindParam(':projectID', $projectID);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch project tasks
    $stmt = $conn->prepare("SELECT Tache_ID, Texte FROM TACHE JOIN tache_assignee_projet ON TACHE.Tache_ID = tache_assignee_projet.id_tache WHERE id_projet = :projectID");
    $stmt->bindParam(':projectID', $projectID);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch users not in the project
    $stmt = $conn->prepare("SELECT Utilisateur_ID, Pseudo FROM UTILISATEUR WHERE Utilisateur_ID NOT IN (SELECT Utilisateur_ID FROM est_membre_projet WHERE Projet_ID = :projectID)");
    $stmt->bindParam(':projectID', $projectID);
    $stmt->execute();
    $nonMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['members' => $members, 'tasks' => $tasks, 'nonMembers' => $nonMembers]);
}