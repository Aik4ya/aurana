<?php
require_once '../mysql/connexion_bdd.php';
$conn = connexion_bdd();

if (isset($_GET['project_id'])) {
    $projectId = $_GET['project_id'];
    $groupeId = $_SESSION['Groupe_ID'];
    $userId = $_SESSION['Utilisateur_ID'];
    
    //récupérer les tâches du projet
    $sqlTasks = "SELECT TACHE.Tache_ID, TACHE.Texte, TACHE.Date_Tache
                 FROM TACHE
                 JOIN tache_assignee_projet ON TACHE.Tache_ID = tache_assignee_projet.id_tache
                 WHERE tache_assignee_projet.id_projet = :project_id";
    $stmtTasks = $conn->prepare($sqlTasks);
    $stmtTasks->bindParam(':project_id', $projectId);
    $stmtTasks->execute();
    $tasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

    //récupérer les membres du projet
    $sqlMembers = "SELECT UTILISATEUR.Utilisateur_ID, UTILISATEUR.Pseudo
                   FROM UTILISATEUR
                   JOIN est_membre_projet ON UTILISATEUR.Utilisateur_ID = est_membre_projet.Utilisateur_ID
                   WHERE est_membre_projet.Projet_ID = :project_id";
    $stmtMembers = $conn->prepare($sqlMembers);
    $stmtMembers->bindParam(':project_id', $projectId);
    $stmtMembers->execute();
    $members = $stmtMembers->fetchAll(PDO::FETCH_ASSOC);

    //vérifier si admin de projet    
    $sqlIsAdmin = "SELECT admin FROM est_membre_projet WHERE Utilisateur_ID = :user_id AND Projet_ID = :project_id";
    $stmtIsAdmin = $conn->prepare($sqlIsAdmin);
    $stmtIsAdmin->bindParam(':user_id', $userId);
    $stmtIsAdmin->bindParam(':project_id', $projectId);
    $stmtIsAdmin->execute();
    if ($stmtIsAdmin->fetchColumn() == 1) {
        $isAdmin = true;
    } else {
        $isAdmin = false;
    }

    echo json_encode([
        'tasks' => $tasks,
        'members' => $members,
        'isAdmin' => $isAdmin,
        'month' => date('n'),
        'year' => date('Y')
    ]);
}
?>
