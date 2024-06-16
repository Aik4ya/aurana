<?php
require_once 'connexion_bdd.php';
session_start();

$nom_groupe = $_POST['groupName'];
$description_groupe = isset($_POST['groupDescription']) ? $_POST['groupDescription'] : '';
$utilisateur_id = $_SESSION['Utilisateur_ID'];

if (empty($nom_groupe)) {
    header("Location: ../main.php?error=empty_group_name");
    exit;
}

// Fonction pour générer un code aléatoire
function genererCodeAleatoire($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

try {
    $conn = connexion_bdd();

    // Vérifier si le groupe existe déjà
    $sql = "SELECT Groupe_ID FROM GROUPE WHERE Nom = :nom_groupe";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom_groupe', $nom_groupe);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        header("Location: ../main.php?error=group_exists");
        exit;
    }

    // Générer un code aléatoire pour le groupe
    $code_groupe = genererCodeAleatoire();

    $sql = "INSERT INTO GROUPE (Nom, Description_Groupe, Code) VALUES (:nom_groupe, :description_groupe, :code_groupe)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom_groupe', $nom_groupe);
    $stmt->bindParam(':description_groupe', $description_groupe);
    $stmt->bindParam(':code_groupe', $code_groupe);
    $stmt->execute();

    $groupe_id = $conn->lastInsertId();

    $sql = "INSERT INTO est_membre (Utilisateur_ID, GROUPE, droit) VALUES (:utilisateur_id, :groupe_id, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->bindParam(':groupe_id', $groupe_id);
    $stmt->execute();

    header("Location: ../pages/main.php?groupe=$nom_groupe");
} catch (Exception $e) {
    header("Location: ../pages/main.php?error=" . $e->getMessage());
}
exit;
?>
