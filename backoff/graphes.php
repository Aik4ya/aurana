<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../mysql/connexion_bdd.php";

$dbh = connexion_bdd();

$sql = "SELECT HOUR(ADDTIME(derniere_connexion, '2:00:00')) as heure, COUNT(*) as nombre FROM UTILISATEUR GROUP BY HOUR(ADDTIME(derniere_connexion, '2:00:00'))";
$stmt = $dbh->prepare($sql);
$stmt->execute();

$nombre_utilisateurs_par_heure = array_fill(0, 24, 0); // Remplit le tableau de 24 heures avec des 0

if ($stmt->rowCount() > 0) {
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nombre_utilisateurs_par_heure[$row['heure']] = $row['nombre'];
    }
}

$max_value = !empty($nombre_utilisateurs_par_heure) ? max($nombre_utilisateurs_par_heure) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphique des utilisateurs connectés</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .bar-chart-container {
            position: relative;
            height: 400px; /* Augmenter la hauteur pour inclure l'axe des X */
            margin: 20px 0;
            padding: 20px;
            background: #e9ecef;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .bar-chart {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            height: 80%; /* Ajuster la hauteur pour laisser de la place pour les labels */
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            position: relative;
        }
        .bar {
            width: calc((100% / 24) - 4px); /* Calculer la largeur des barres pour qu'elles rentrent toutes */
            margin: 0 2px;
            background-color: #3498db;
            text-align: center;
            color: white;
            border-radius: 5px 5px 0 0;
            position: relative;
        }
        .bar span {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            margin-bottom: 5px;
            font-size: 12px;
            color: #333;
        }
        .y-axis {
            position: absolute;
            left: -50px;
            bottom: 20px;
            height: 80%; /* Ajuster la hauteur pour correspondre à celle de la barre-chart */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
        }
        .y-axis div {
            position: relative;
            right: -10px;
            font-size: 12px;
            color: #333;
            margin-bottom: -8px; /* Ajustement pour éviter le chevauchement */
        }
        .x-axis {
            display: flex;
            justify-content: space-between;
            padding-left: 20px; /* Décalage pour correspondre à l'axe des Y */
            padding-right: 2px;
            margin-top: 10px;
        }
        .x-axis div {
            flex-basis: calc(100% / 24); /* Largeur égale pour chaque heure */
            text-align: center;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Nombre d'utilisateurs connectés par heure</h2>
        <div class="bar-chart-container">
            <div class="bar-chart">
                <?php
                foreach ($nombre_utilisateurs_par_heure as $heure => $nombre) {
                    $height = $max_value > 0 ? ($nombre / $max_value) * 100 : 0;
                    echo "<div class='bar' style='height: {$height}%;'>
                            <span>{$nombre}</span>
                          </div>";
                }
                ?>
            </div>
            <div class="y-axis">
                <?php
                for ($i = $max_value; $i >= 1; $i--) {
                    echo "<div>{$i}</div>";
                }
                ?>
            </div>
        </div>
        <div class="x-axis">
            <?php
            for ($heure = 0; $heure < 24; $heure++) {
                echo "<div>{$heure}h</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
