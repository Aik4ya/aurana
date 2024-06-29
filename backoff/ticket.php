<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../mysql/connexion_bdd.php';
require_once '../mysql/cookies_uid.php';

// if ($_SESSION['Droit'] == 0) {
// 	header('Location: ../pages/403.html');
// 	exit();
// }

function affichage_ticket($dbh)
{
	try {
		$sql = $dbh->prepare('SELECT Ticket_ID, Categorie_Ticket, Date_Creation, Description_Ticket, Priorite, Etat, Demandeur_ID FROM TICKET');
		$sql->execute();
		$result = $sql->fetchAll();

		if ($result) {
			echo "<table border='1'>";
			echo "<tr>";
			echo "<th>ID</th>";
			echo "<th>Catégorie</th>";
			echo "<th>Date de création</th>";
			echo "<th>Description</th>";
			echo "<th>Priorité</th>";
			echo "<th>Etat</th>";
			echo "<th>Demandeur</th>";
			echo "</tr>";
			foreach ($result as $row) {
				echo "<tr>";
				echo "<td>". $row["Ticket_ID"]. "</td>";
				echo "<td>". $row["Categorie_Ticket"]. "</td>";
				echo "<td>". $row["Date_Creation"]. "</td>";
				echo "<td>". $row["Description_Ticket"]. "</td>";
				echo "<td>". $row["Priorite"]. "</td>";
				echo "<td>". $row["Etat"]. "</td>";
				echo "<td>". $row["Demandeur_ID"]. "</td>";
				echo "<td><button onclick='modification_ticket(". $row["Ticket_ID"]. ")'>Modifier</button></td>";
				echo "<td><button onclick='suppression_ticket(". $row["Ticket_ID"]. ")'>Supprimer</button></td>";
				echo "</tr>";
			}
			echo "</table>";
		}

	} catch (PDOException $e) {
		echo "Erreur : ". $e->getMessage();
	}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurana - BackOffice</title>
    <link rel="stylesheet" href="../css/main_profile.css">
    <link rel="stylesheet" href="../css/button.css">
    <link rel="stylesheet" href="../css/base_main.css">
    <link rel="stylesheet" href="../css/backoff_ticket.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>
    <!-- container start -->
    <div class="container">
        <!-- left start -->
        <div class="left">
            <!-- header start -->
            <header>
                <!-- logo start -->
                <div class="logo">
                    <a href="b_off.php"><h2>aurana</h2></a>
                    <div class="close">
                        <span class="material-symbols-outlined">
                            close
                        </span>
                    </div>
                </div>
                <!-- nav start -->
                <nav>
                    <ul>
                        <li>
                            <a href="utilisateurs.php">
                                <span class="material-symbols-outlined full">
                                    group
                                </span>
                                <span class="title">Gestions des Utilisateurs</span>
                            </a>
                        </li>
                        <li>
                            <a href="ticket.php">
                                <span class="material-symbols-outlined full">
                                    stack
                                </span>
                                <span class="title">Tickets</span>
                            </a>
                        </li>
                        <li>
                            <a href="qr_cptcha.php">
                                <span class="material-symbols-outlined full">
                                    help
                                </span>
                                <span class="title">Captcha</span>
                            </a>
                        </li>
                        <li>
                            <a href="logs.php">
                                <span class="material-symbols-outlined full">
                                    wysiwyg
                                </span>
                                <span class="title">Logs</span>
                            </a>
                        </li>
                        <li>
                            <a href="newsletter.php">
                                <span class="material-symbols-outlined full">
                                    mail
                                </span>
                                <span class="title">Newsletter</span>
                            </a>
                    </ul>
                </nav>
                <!-- nav end -->
            </header>
            <!-- header end -->
        </div>
        <!-- left end -->
        <!-- right start -->
        <div class="right">
            <!-- top start -->
            <div class="top">
                <!-- user start -->
                <div class="user">
                <?php
                    session_start();
                    echo "<h2>" . $_SESSION['Pseudo'] . "<br>";
                    echo "<span>" . ($_SESSION['Droit'] == 1 ? "Administrateur" : "Utilisateur") . "</span></h2>";
                ?>
                </div>
            </div>
            <main>
                <div class="ticketBox">
					<div id="ticket">
						<h2 class="header">Ticket</h2>
						<form id="ticket-form">
							<input type="text" id="new-ticket" placeholder="Enter ticket...">
							<button type="button" id="btn-create-ticket">Créer un nouveau ticket</button>
						</form>
						<?php
						affichage_ticket(connexion_bdd());
						?>
					</div>
                </div>
            </main>
        </div>
    </div>
	<script>
		document.getElementById('btn-create-ticket').addEventListener('click', function() {
			creation_ticket();
		});

		function modification_ticket(id) {
			alert('Modification du ticket ' + id);
		}

		function suppression_ticket(id) {
			alert('Suppression du ticket ' + id);
		}

		function creation_ticket() {
			alert('Création d\'un nouveau ticket');
		}
	</script>
</body>
</html>
