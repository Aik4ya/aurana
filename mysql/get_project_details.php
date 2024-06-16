<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'connexion_bdd.php';

session_start();

if (!isset($_GET['project_id'])) {
    echo json_encode(['success' => false, 'error' => 'project_id missing']);
    exit;
}

$projectID = $_GET['project_id'];

$conn = connexion_bdd();

// Fetch project members
$sql_members = "SELECT UTILISATEUR.Utilisateur_ID, UTILISATEUR.Pseudo, est_membre_projet.admin 
                FROM est_membre_projet 
                JOIN UTILISATEUR ON est_membre_projet.Utilisateur_ID = UTILISATEUR.Utilisateur_ID 
                WHERE est_membre_projet.Projet_ID = :projectID";
$stmt_members = $conn->prepare($sql_members);
$stmt_members->bindParam(':projectID', $projectID, PDO::PARAM_INT);
$stmt_members->execute();
$members = $stmt_members->fetchAll(PDO::FETCH_ASSOC);

// Fetch non-members
$sql_non_members = "SELECT UTILISATEUR.Utilisateur_ID, UTILISATEUR.Pseudo 
                    FROM UTILISATEUR 
                    WHERE UTILISATEUR.Utilisateur_ID NOT IN 
                    (SELECT est_membre_projet.Utilisateur_ID FROM est_membre_projet WHERE est_membre_projet.Projet_ID = :projectID)";
$stmt_non_members = $conn->prepare($sql_non_members);
$stmt_non_members->bindParam(':projectID', $projectID, PDO::PARAM_INT);
$stmt_non_members->execute();
$nonMembers = $stmt_non_members->fetchAll(PDO::FETCH_ASSOC);

// Fetch project tasks
$sql_tasks = "SELECT TACHE.Tache_ID, TACHE.Texte, UTILISATEUR.Pseudo, TACHE.Date_Tache 
              FROM TACHE 
              JOIN es_assigner ON TACHE.Tache_ID = es_assigner.Tache_ID 
              JOIN UTILISATEUR ON es_assigner.Utilisateur_ID = UTILISATEUR.Utilisateur_ID 
              JOIN tache_assignee_projet ON TACHE.Tache_ID = tache_assignee_projet.id_tache
              WHERE tache_assignee_projet.id_projet = :projectID";
$stmt_tasks = $conn->prepare($sql_tasks);
$stmt_tasks->bindParam(':projectID', $projectID, PDO::PARAM_INT);
$stmt_tasks->execute();
$tasks = $stmt_tasks->fetchAll(PDO::FETCH_ASSOC);

$isAdmin = ($_SESSION['Droit_groupe'] == 1) ? true : false;

echo json_encode([
    'success' => true,
    'members' => $members,
    'nonMembers' => $nonMembers,
    'tasks' => $tasks,
    'isAdmin' => $isAdmin
]);
?>
