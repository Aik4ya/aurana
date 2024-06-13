<?php

require '../mysql/connexion_bdd.php';
require_once '../mysql/cookies_uid.php';
$conn = connexion_bdd();
session_start();

verif_session();

if (isset($_GET['groupe'])) {
    $groupe = $_GET['groupe'];
    $stmt = $conn->prepare("SELECT Groupe_ID, Nom FROM GROUPE WHERE Nom = :groupe");
    $stmt->bindParam(':groupe', $groupe);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $_SESSION['Groupe_ID'] = $row['Groupe_ID'];
        $nom_groupe = $row['Nom'];
    } else {
        header("Location: main_files.php?groupe=none");
        exit;
    }
} else {
    $_GET['groupe'] = "none";
    header("Location: main_files.php?groupe=none");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurana - Fichiers du groupe</title>
    <link rel="stylesheet" href="../css/main_files.css">
    <link rel="stylesheet" href="../css/button.css">
</head>
<body>
<div class="container">
    <div class="sidebar">
    </div>
    <div class="main-content">
        <h1>Gestionnaire de fichiers pour <?= htmlspecialchars($nom_groupe) ?></h1>
        
        <form action="uploadFile.php" method="post" enctype="multipart/form-data">
            Sélectionnez le fichier à télécharger :
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Envoyer le fichier" name="submit">
        </form>

        <h2>Fichiers existants</h2>
        <ul>
            <?php
            $filesQuery = $conn->prepare("SELECT * FROM FILES WHERE Groupe_ID = ?");
            $filesQuery->execute([$_SESSION['Groupe_ID']]);
            while ($file = $filesQuery->fetch(PDO::FETCH_ASSOC)) {
                echo '<li><a href="../uploads/' . htmlspecialchars($file['filename']) . '">' . htmlspecialchars($file['filename']) . '</a></li>';
            }
            ?>
        </ul>
    </div>
</div>
<script src="../js/main_files.js"></script>
</body>
</html>
