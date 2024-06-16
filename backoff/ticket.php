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
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ticket</title>
</head>
<body>
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
