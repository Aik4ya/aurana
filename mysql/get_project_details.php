<?php
require_once 'connexion_bdd.php';

session_start();

if (isset($_GET['project_id'])) {
    $projectID = $_GET['project_id'];
    $userID = $_SESSION['Utilisateur_ID'];
    $conn = connexion_bdd();

    // Vérification si l'utilisateur est admin
    $sql = "SELECT admin FROM est_membre_projet WHERE Projet_ID = :projectID AND Utilisateur_ID = :userID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();
    $isAdmin = $stmt->fetch(PDO::FETCH_ASSOC)['admin'];

    // Récupération des détails du projet
    $sql = "SELECT * FROM PROJET WHERE ID = :projectID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupération des membres du projet
    $sql = "SELECT UTILISATEUR.Pseudo, est_membre_projet.admin, UTILISATEUR.Utilisateur_ID 
            FROM est_membre_projet 
            JOIN UTILISATEUR ON est_membre_projet.Utilisateur_ID = UTILISATEUR.Utilisateur_ID 
            WHERE est_membre_projet.Projet_ID = :projectID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupération des non-membres du projet qui sont membres du groupe
        $sql = "SELECT UTILISATEUR.Utilisateur_ID, UTILISATEUR.Pseudo 
        FROM UTILISATEUR 
        WHERE UTILISATEUR.Utilisateur_ID NOT IN 
            (SELECT Utilisateur_ID FROM est_membre_projet WHERE Projet_ID = :projectID)
        AND UTILISATEUR.Utilisateur_ID IN 
            (SELECT Utilisateur_ID FROM est_membre WHERE GROUPE = :groupID)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT);
        $stmt->bindParam(':groupID', $_SESSION['Groupe_ID'], PDO::PARAM_INT);
        $stmt->execute();
        $nonMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Récupération des tâches du projet
    $sql = "SELECT TACHE.Tache_ID, TACHE.Texte, UTILISATEUR.Pseudo, TACHE.Date_Tache 
            FROM tache_assignee_projet 
            JOIN TACHE ON tache_assignee_projet.id_tache = TACHE.Tache_ID 
            LEFT JOIN es_assigner ON TACHE.Tache_ID = es_assigner.Tache_ID 
            LEFT JOIN UTILISATEUR ON es_assigner.Utilisateur_ID = UTILISATEUR.Utilisateur_ID 
            WHERE tache_assignee_projet.id_projet = :projectID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'isAdmin' => (bool) $isAdmin,
        'project' => $project,
        'members' => $members,
        'nonMembers' => $nonMembers,
        'tasks' => $tasks
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'project_id missing']);
}
