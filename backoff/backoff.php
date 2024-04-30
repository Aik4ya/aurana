<?php
	require_once '../pages/ticket.php';
	require_once '../mysql/cookies_uid.php';
	
	ecriture_log('BackOffice');
	verif_session();
	$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en" class="dv">

<head>
	<meta charset="UTF-8">
	<title>Aurana - BackOffice</title>
	<link rel="stylesheet" href="/css/backoff.css">
</head>

<body>
	<div class="admin-panel clearfix">
		<div class="slidebar">
			<div class="logo">
				<a href="../pages/main.php">
					<img src="/img/aurana_logo.png" alt="Aurana" />
				</a>
			</div>
			<ul>
				<li><a href="#dashboard" id="targeted">dashboard</a></li>
				<li><a href="#pages">pages</a></li>
				<li><a href="#ticket">ticket</a></li>
				<li><a href="#users">utilisateur</a></li>
				<li><a href="#groupe">groupe</a></li>
				<li><a href="#settings">settings</a></li>
			</ul>
		</div>
		<div class="main">
			<div class="mainContent clearfix">
				<div id="dashboard">
					<h2 class="header"><span class="icon"></span>Dashboard</h2>
					<div class="monitor">
						<div class="clearfix">
						</div>
					</div>
				</div>
				<div id="ticket">
					<h2 class="header">Ticket</h2>
					<?php
						afficher_tickets(connexion_bdd(),lecture_cookie_uid(), $_SESSION['Droit']);
					?>
				</div>
				<div id="groupe">
					<h2 class="header"><span class="icon"></span>Liste des groupes</h2>
					<div class="monitor">
						<div class="clearfix">
							<?php
							$dbh = connexion_bdd();
							echo "<h4>Groupe</h4>";
							$sql = $dbh->prepare("SELECT * FROM GROUPE;");
							$sql->execute();
							$result = $sql->fetchAll();

							if ($result) {
								echo "<table border='1'>";
								echo "<tr>";
								echo "<th>Groupe_ID</th>";
								echo "<th>Nom</th>";
								echo "<th>Description_Groupe</th>";
								echo "<th>Theme</th>";
								echo "<th>Autorisation_Fichier</th>";
								echo "<th>Action</th>"; 
								echo "</tr>";
								foreach ($result as $row) {
									echo "<tr>";
									echo "<td>" . $row["Groupe_ID"] . "</td>";
									echo "<td>" . $row["Nom"] . "</td>";
									echo "<td>" . $row["Description_Groupe"] . "</td>";
									echo "<td>" . $row["Theme"] . "</td>";
									echo "<td>" . $row["Autorisation_Fichier"] . "</td>";
									echo "<td><button onclick='deleteGroup(" . $row["Groupe_ID"] . ")'>Delete</button></td>";
									echo "</tr>";
								}
								echo "</table>";
							} else {
								echo "Aucun groupe trouvé";
							}
							?>
						</div>
					</div>
				</div>

				<div id="users">
					<h2 class="header">Utilisateurs</h2>
					<div class="monitor">
					<div class="user-form">
						<h3>Créé un Utilisateur</h3>
						<form action="traitement_signup.php" method="post">
							<label for="username">Pseudo:</label>
							<input type="text" id="username" name="pseudo" required>
							<label for="password">Password:</label>
							<input type="password" id="password" name="mot de passe" required>
							<label for="email">Email:</label>
							<input type="email" id="email" name="email" required>
							<label for="admin">Admin:</label>
							<input type="checkbox" id="admin" name="admin">
							<br>
							<input type="submit" value="Validé">
						</form>
					</div>
					<div class="user-list">
						<h3>Liste des Utilisateur</h3>
						<button id="showUsers">Afficher la liste</button>
						<div id="userListContainer" style="display: none;">
							<?php
							$dbh = connexion_bdd();
							echo "<h4>utilisateurs</h4>";
							$sql = $dbh->prepare ("SELECT * FROM UTILISATEUR;");
							$sql->execute();
							$result = $sql->fetchAll();
							
							if ($result) {
								echo "<table border='1'>";
								echo "<tr>";
								echo "<th>Utilisateur_ID</th>";
								echo "<th>Pseudo</th>";
								echo "<th>Identifiant</th>";
								echo "<th>Droit</th>";
								echo "<th>Email</th>";
								echo "</tr>";
								foreach ($result as $row) {
									echo "<tr>";
									echo "<td>" . $row["Utilisateur_ID"] . "</td>";
									echo "<td>" . $row["Pseudo"] . "</td>";
									echo "<td>" . $row["Identifiant"] . "</td>";
									echo "<td>" . $row["Droit"] . "</td>";
									echo "<td>" . $row["Email"] . "</td>";
									echo "</tr>";
								}
								echo "</table>";
							} else {
								echo "Aucun utilisateur trouvé";
							}
							?>
						</div>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		document.getElementById('showUsers').addEventListener('click', function() {
			var userListContainer = document.getElementById('userListContainer');
			if (userListContainer.style.display === 'none') {
				userListContainer.style.display = 'block';
			} else {
				userListContainer.style.display = 'none';
			}
		});

			function deleteGroup(groupId) {
				if (confirm("Êtes-vous sûr de vouloir supprimer ce groupe ?")) {
					alert("Le groupe avec l'ID " + groupId + " a été supprimé.");
				}
			}
			

	</script>
</body>
</html>
