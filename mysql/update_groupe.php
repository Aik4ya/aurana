<?php
require_once 'connexion_bdd.php';
session_start();

if (!isset($_POST['groupName']) || !isset($_POST['groupDescription']) || !isset($_POST['groupCode'])) {
    header("Location: ../pages/main.php?error=missing_group_info");
    exit;
}

$groupId = $_SESSION['Groupe_ID'];
$groupName = $_POST['groupName'];
$groupDescription = $_POST['groupDescription'];
$groupCode = $_POST['groupCode'];

$conn = connexion_bdd();
$sql = "UPDATE GROUPE SET Nom = :groupName, Description_Groupe = :groupDescription, Code = :groupCode WHERE Groupe_ID = :groupId";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':groupName', $groupName);
$stmt->bindParam(':groupDescription', $groupDescription);
$stmt->bindParam(':groupCode', $groupCode);
$stmt->bindParam(':groupId', $groupId);

if ($stmt->execute()) {
    header("Location: ../pages/main.php?groupe=$groupName");
} else {
    header("Location: ../pages/main.php?error=group_update_failed");
}
?>
