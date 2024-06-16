<?php
require_once 'connexion_bdd.php';
session_start();

if (!isset($_GET['task_id'])) {
    echo json_encode(['success' => false, 'error' => 'task_id missing']);
    exit;
}

$taskID = $_GET['task_id'];
$conn = connexion_bdd();

$sql_task = "SELECT TACHE.Tache_ID, TACHE.Texte, TACHE.Categorie, TACHE.Date_Tache, UTILISATEUR.Pseudo 
             FROM TACHE 
             JOIN es_assigner ON TACHE.Tache_ID = es_assigner.Tache_ID 
             JOIN UTILISATEUR ON es_assigner.Utilisateur_ID = UTILISATEUR.Utilisateur_ID 
             WHERE TACHE.Tache_ID = :taskID";
$stmt_task = $conn->prepare($sql_task);
$stmt_task->bindParam(':taskID', $taskID, PDO::PARAM_INT);
$stmt_task->execute();
$task = $stmt_task->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo json_encode(['success' => false, 'error' => 'Task not found']);
    exit;
}

echo json_encode(['success' => true, 'task' => $task]);
?>
