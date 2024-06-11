<?php
require_once '../mysql/connexion_bdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $taskId = intval($data['taskId']);
    $field = isset($data['done'])? 'done' : 'star';
    $value = intval($data[$field]);

    $sql = "UPDATE TACHE SET $field = :value WHERE Tache_ID = :taskId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
    $stmt->bindParam(':value', $value, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} else {
    http_response_code(405); // Méthode HTTP non autorisée
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed.']);
}
?>