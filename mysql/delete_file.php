<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../mysql/connexion_bdd.php';
session_start();

header('Content-Type: application/json');

if (!isset($_POST['file_id'])) {
    echo json_encode(['error' => 'Invalid request: file_id is missing']);
    exit;
}

$file_id = intval($_POST['file_id']);
$user_id = $_SESSION['Utilisateur_ID'];

$conn = connexion_bdd();

// Check if the file belongs to the logged-in user
$sql = "SELECT * FROM FICHIER WHERE Fichier_ID = :file_id AND Utilisateur_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    echo json_encode(['error' => 'File not found or you do not have permission to delete this file']);
    exit;
}

$filePath = '../uploads/' . $file['Adresse'];

if (file_exists($filePath) && is_writable($filePath)) {
    unlink($filePath); // Delete the file from the server
} else {
    echo json_encode(['error' => 'Cannot delete the file. File does not exist or is not writable.']);
    exit;
}

$sql = "DELETE FROM FICHIER WHERE Fichier_ID = :file_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
$stmt->execute();

echo json_encode(['message' => 'File deleted successfully.', 'success' => true]);
?>
