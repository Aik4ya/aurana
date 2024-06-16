<?php
require_once '../mysql/connexion_bdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['taskId'];
    $task_status = $_POST['task_status'];


    // $sql = "UPDATE TACHE SET done = :task_status WHERE Tache_ID = :taskId";
    // $stmt = $conn->prepare($sql);
    // $stmt->bindParam(':taskId', $taskId);
    // $stmt->bindParam(':task_status', $task_status);
    // $stmt->execute();
}
?>