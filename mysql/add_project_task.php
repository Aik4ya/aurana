<?php
require_once '../mysql/connexion_bdd.php';
$conn = connexion_bdd();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectId = $_POST['projectId'];
    $taskName = $_POST['taskName'];
    $taskDate = $_POST['taskDate'];
    $taskAssignee = $_POST['taskAssignee'];

    $sqlTask = "INSERT INTO TACHE (Texte, Date_Creation, Date_Tache, Groupe_ID) VALUES (:text, NOW(), :date, :groupe_id)";
    $stmtTask = $conn->prepare($sqlTask);
    $stmtTask->bindParam(':text', $taskName);
    $stmtTask->bindParam(':date', $taskDate);
    $stmtTask->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
    $stmtTask->execute();
    $taskId = $conn->lastInsertId();

    $sqlAssignTask = "INSERT INTO es_assigner (Utilisateur_ID, Tache_ID) VALUES (:user_id, :task_id)";
    $stmtAssignTask = $conn->prepare($sqlAssignTask);
    $stmtAssignTask->bindParam(':user_id', $taskAssignee);
    $stmtAssignTask->bindParam(':task_id', $taskId);
    $stmtAssignTask->execute();

    $sqlProjectTask = "INSERT INTO tache_assignee_projet (id_tache, id_projet) VALUES (:task_id, :project_id)";
    $stmtProjectTask = $conn->prepare($sqlProjectTask);
    $stmtProjectTask->bindParam(':task_id', $taskId);
    $stmtProjectTask->bindParam(':project_id', $projectId);
    $stmtProjectTask->execute();

    echo json_encode(['success' => true, 'projectId' => $projectId]);
}
?>
