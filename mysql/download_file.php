<?php
require_once '../mysql/connexion_bdd.php';
session_start();

if (!isset($_GET['file_id'])) {
    die("Invalid request");
}

$file_id = intval($_GET['file_id']);

$conn = connexion_bdd();

$sql = "SELECT * FROM FICHIER WHERE Fichier_ID = :file_id AND Groupe_ID = :group_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
$stmt->bindParam(':group_id', $_SESSION['Groupe_ID'], PDO::PARAM_INT);
$stmt->execute();

$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    die("File not found");
}

$filePath = '../uploads/' . $file['Adresse'];

if (file_exists($filePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    die("File not found on server");
}
?>
