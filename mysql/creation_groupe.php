<?php
require_once 'connexion_bdd.php';
session_start();

if (!isset($_POST['groupName']) || empty($_POST['groupName'])) {
    header("Location: ../pages/main.php?error=empty_group_name");
    exit;
}

$groupName = $_POST['groupName'];
$groupDescription = $_POST['groupDescription'];
$groupCode = bin2hex(random_bytes(5));

$conn = connexion_bdd();
$sql = "INSERT INTO GROUPE (Nom, Description_Groupe, Code) VALUES (:groupName, :groupDescription, :groupCode)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':groupName', $groupName);
$stmt->bindParam(':groupDescription', $groupDescription);
$stmt->bindParam(':groupCode', $groupCode);

if ($stmt->execute()) {
    $groupId = $conn->lastInsertId();
    $sql = "INSERT INTO est_membre (Utilisateur_ID, GROUPE, droit) VALUES (:userId, :groupId, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userId', $_SESSION['Utilisateur_ID']);
    $stmt->bindParam(':groupId', $groupId);
    $stmt->execute();
    header("Location: ../pages/main.php?groupe=$groupName");
} else {
    header("Location: ../pages/main.php?error=group_exists");
}
?>
