<?php
require_once '../mysql/connexion_bdd.php';

$taskId = $_POST['taskId'];
$task_status = $_POST['task_status'];

$conn = connexion_bdd();

$sql = "UPDATE TACHE SET stars = :task_status WHERE Tache_ID = :taskId";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':taskId', $taskId);
$stmt->bindParam(':task_status', $task_status);

$response = ['success' => false];

if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['message'] = 'Failed to update task status.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
