<?php
// Connexion à la base de données (à remplacer par vos informations de connexion)
$bdd_login = 'aurana';
$bdd_password = 'aurana2024';

    $conn = new PDO('mysql:host=localhost;dbname=Aurana_bdd', $bdd_login, $bdd_password);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Requête SQL pour compter le nombre d'utilisateurs
$sql = "SELECT COUNT(*) as userCount FROM UTILISATEUR";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Récupération des données
    $row = $result->fetch_assoc();
    $userCount = $row["userCount"];
} else {
    $userCount = 0;
}

// Fermeture de la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphique Google avec PHP</title>
    <!-- Chargement de la bibliothèque Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Chargement de la bibliothèque Google Charts
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        // Fonction pour dessiner le graphique
        function drawChart() {
            // Création des données pour le graphique
            var data = google.visualization.arrayToDataTable([
                ['Utilisateurs', 'Nombre'],
                ['Utilisateurs actifs', <?php echo $userCount; ?>],
                ['Utilisateurs suspendus', <?php echo 100 - $userCount; ?>]
            ]);

            // Options du graphique
            var options = {
                title: 'Répartition des utilisateurs',
                pieHole: 0.4,
                colors: ['#4285F4', '#DB4437']
            };

            // Création de l'instance du graphique
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            // Dessin du graphique avec les données et les options
            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <!-- Conteneur pour le graphique -->
    <div id="piechart" style="width: 900px; height: 500px;"></div>
</body>
</html>
