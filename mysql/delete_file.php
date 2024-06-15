<?php

require_once '../mysql/connexion_bdd.php';
session_start();

if (!isset($_POST['file_id'])) {
    die("Invalid request");
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
    die("File not found or you do not have permission to delete this file");
}

$filePath = '../uploads/' . $file['Adresse'];

if (file_exists($filePath)) {
    unlink($filePath); // Delete the file from the server
}

$sql = "DELETE FROM FICHIER WHERE Fichier_ID = :file_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
$stmt->execute();

echo json_encode(['message' => 'File deleted successfully.', 'success' => true]);

?>
