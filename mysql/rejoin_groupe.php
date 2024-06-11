<?php
require_once 'connexion_bdd.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$code_groupe = $_POST['groupCode'];
$utilisateur_id = $_SESSION['Utilisateur_ID'];

$conn = connexion_bdd();

$sql = "SELECT Groupe_ID, Nom FROM GROUPE WHERE Code = :code_groupe";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':code_groupe', $code_groupe);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {

    $groupe_id = $row['Groupe_ID'];
    $nom_groupe = $row['Nom'];

    $sql = "SELECT ID FROM est_membre WHERE Groupe = :groupe_ID AND Utilisateur_ID = :utilisateur_ID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':groupe_ID', $groupe_id);
    $stmt->bindParam(':utilisateur_ID', $utilisateur_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        header("Location: ../pages/main.php?groupe={$nom_groupe}&error=already_in_group");
        exit;
    } else {
        $sql = "INSERT INTO est_membre (Utilisateur_ID, GROUPE) VALUES (:utilisateur_id, :groupe_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);
        $stmt->bindParam(':groupe_id', $groupe_id);
        $stmt->execute();

        header("Location: ../pages/main.php?groupe=$nom_groupe");
    }
} else {
    header("Location: ../pages/main.php?groupe=none");
}
exit;
?>
