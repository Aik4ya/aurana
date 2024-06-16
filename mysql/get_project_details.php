<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../mysql/connexion_bdd.php';

header('Content-Type: application/json'); // Assurez-vous que la réponse est JSON

if (!isset($_GET['project_id'])) {
    echo json_encode(['error' => 'project_id missing']);
    exit;
}

$project_id = intval($_GET['project_id']);
$conn = connexion_bdd();

// Fetch project members
$sql = "SELECT u.Pseudo FROM est_membre_projet emp
        JOIN UTILISATEUR u ON emp.Utilisateur_ID = u.Utilisateur_ID
        WHERE emp.Projet_ID = :project_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch project tasks
$sql = "SELECT t.Tache_ID, t.Texte, u.Pseudo, t.Date_Tache FROM tache_assignee_projet tap
        JOIN TACHE t ON tap.id_tache = t.Tache_ID
        LEFT JOIN UTILISATEUR u ON t.Utilisateur_ID = u.Utilisateur_ID
        WHERE tap.id_projet = :project_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch non-members
$sql = "SELECT u.Utilisateur_ID, u.Pseudo FROM UTILISATEUR u
        WHERE u.Utilisateur_ID NOT IN (
            SELECT emp.Utilisateur_ID FROM est_membre_projet emp WHERE emp.Projet_ID = :project_id
        )";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$nonMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'members' => $members,
    'tasks' => $tasks,
    'nonMembers' => $nonMembers
]);
?>
