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

//vérfier si le groupe existe
$sql = "SELECT Groupe_ID FROM GROUPE WHERE Nom = :groupName";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':groupName', $groupName);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    header("Location: ../pages/main.php?groupe={$_SESSION['Groupe']}&error=group_exists");
    exit;
}

//si test ok création du groupe
$sql = "INSERT INTO GROUPE (Nom, Description_Groupe, Code) VALUES (:groupName, :groupDescription, :groupCode)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':groupName', $groupName);
$stmt->bindParam(':groupDescription', $groupDescription);
$stmt->bindParam(':groupCode', $groupCode);
$stmt->execute();

//ajout du créateur au groupe
$groupId = $conn->lastInsertId();
$sql = "INSERT INTO est_membre (Utilisateur_ID, GROUPE, droit) VALUES (:userId, :groupId, 1)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':userId', $_SESSION['Utilisateur_ID']);
$stmt->bindParam(':groupId', $groupId);
$stmt->execute();
header("Location: ../pages/main.php?groupe=$groupName");
exit;
?>
