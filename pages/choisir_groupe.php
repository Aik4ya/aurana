<?php
// Activer l'affichage des erreurs pour le débogage
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Inclure les fichiers nécessaires
session_start();
require_once '../mysql/connexion_bdd.php';

// Connexion à la base de données
$dbh = connexion_bdd();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir un Groupe</title>
    <link rel="stylesheet" href="../css/choisir_groupe.css">
    <!-- Préconnexion aux Google Fonts pour améliorer les performances -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <!-- Spécifier le protocole HTTPS -->
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
</head>
<body>
    <div class="container">
        <div class="content">
            <h1>Aurana</h1>
            <p>Veuillez choisir un groupe pour continuer :</p>
            <form action="main.php" method="get">
                <div class="select-wrapper">
                    <select name="groupe" id="groupe" required>
                        <option value="" disabled selected>Choisissez un groupe</option>
                        <?php
                        $sql = "SELECT GROUPE.Nom FROM est_membre INNER JOIN GROUPE ON est_membre.GROUPE = GROUPE.Groupe_ID WHERE est_membre.Utilisateur_ID = :Utilisateur_ID";
                        $stmt = $dbh->prepare($sql);
                        $stmt->bindParam(':Utilisateur_ID', $_SESSION['Utilisateur_ID'], PDO::PARAM_INT);
                        $stmt->execute();
                        $result = $stmt;

                        if ($result->rowCount() > 0) {
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . htmlspecialchars($row['Nom']) . "'>" . htmlspecialchars($row['Nom']) . "</option>";
                            }
                        } else {
                            echo "<option value='' disabled>Aucun groupe disponible</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                <button type="submit">Continuer</button>
            </form>
            <button onclick="window.location.href='create_group.php'">Créer un groupe</button>
            </div>           
        </div>
        </div>
        <div class="background">
        </div>
    <form action="logout.php" method="post">
        <button type="submit" class="logout-button">Déconnexion</button>
    </form>
</body>
</html>
