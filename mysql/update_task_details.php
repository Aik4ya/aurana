<?php
require_once '../mysql/cookies_uid.php';
require_once '../mysql/connexion_bdd.php';
session_start();

error_log(print_r($_SESSION, true));

if (!isset($_SESSION['Groupe']) || empty($_SESSION['Groupe'])) {
    error_log('Group ID missing in session');
    exit('Group ID missing in session');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('Invalid request method');
    exit('Invalid request method');
}

// Validation des données
$taskID = isset($_POST['taskID']) ? htmlspecialchars($_POST['taskID'], ENT_QUOTES, 'UTF-8') : null;
$taskText = isset($_POST['taskName']) ? htmlspecialchars($_POST['taskName'], ENT_QUOTES, 'UTF-8') : null;
$taskCategory = isset($_POST['taskCategory']) ? htmlspecialchars($_POST['taskCategory'], ENT_QUOTES, 'UTF-8') : null;
$taskDate = isset($_POST['taskDate']) ? htmlspecialchars($_POST['taskDate'], ENT_QUOTES, 'UTF-8') : null;
$taskDescription = isset($_POST['taskDescription']) ? htmlspecialchars($_POST['taskDescription'], ENT_QUOTES, 'UTF-8') : null;

if (!$taskID || !$taskText || !$taskCategory || !$taskDate || !$taskDescription) {
    error_log('Missing required fields: ' . json_encode($_POST));
    exit('Missing required fields');
}

try {
    $conn = connexion_bdd();
    //update de la tache
    $sql_update = "UPDATE TACHE SET Texte = :taskText, Categorie = :taskCategory, Date_Tache = :taskDate, Description = :taskDescription WHERE Tache_ID = :taskID";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bindParam(':taskText', $taskText);
    $stmt_update->bindParam(':taskCategory', $taskCategory);
    $stmt_update->bindParam(':taskDate', $taskDate);
    $stmt_update->bindParam(':taskDescription', $taskDescription);
    $stmt_update->bindParam(':taskID', $taskID, PDO::PARAM_INT);

    if ($stmt_update->execute()) {
        error_log('Task updated successfully: ' . $taskID);

        $group = isset($_SESSION['Groupe']) ? urlencode($_SESSION['Groupe']) : 'none';
        header('Location: ../pages/main.php?groupe=' . $group);
        exit;
    } else {
        $errorInfo = $stmt_update->errorInfo();
        error_log('Error updating task details: ' . json_encode($errorInfo));
        exit('Error updating task details: ' . htmlspecialchars($errorInfo[2]));
    }
} catch (Exception $e) {
    error_log('Exception occurred: ' . $e->getMessage());
    exit('Exception occurred: ' . htmlspecialchars($e->getMessage()));
}
?>
