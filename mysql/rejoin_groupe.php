<?php
require_once 'connexion_bdd.php';
session_start();

if (!isset($_POST['groupCode']) || empty($_POST['groupCode'])) {
    header("Location: ../pages/main.php?error=empty_group_code");
    exit;
}

$groupCode = $_POST['groupCode'];
$conn = connexion_bdd();

//vérifie si le code correspond à un groupe
$sql = "SELECT Groupe_ID, Nom FROM GROUPE WHERE Code = :groupCode";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':groupCode', $groupCode);
$stmt->execute();

$group = $stmt->fetch(PDO::FETCH_ASSOC);

if ($group) {
    $groupId = $group['Groupe_ID'];
    $groupName = $group['Nom'];
    
    //si condition ok ajouter l'utilisateur au groupe
    $sql = "INSERT INTO est_membre (Utilisateur_ID, GROUPE, droit) VALUES (:userId, :groupId, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userId', $_SESSION['Utilisateur_ID']);
    $stmt->bindParam(':groupId', $groupId);
    $stmt->execute();
    
    header("Location: ../pages/main.php?groupe=$groupName");
} else {
    header("Location: ../pages/main.php?error=group_not_found");
}
?>
