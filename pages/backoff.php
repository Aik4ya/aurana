<?php
	require_once '../mysql/connexion_bdd.php';
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
				<a href="/pages/backoff.html">
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
				<div id="users">
					<h2 class="header">Utilisateurs</h2>
					<div class="user-form">
						<h3>Create User</h3>
						<form action="user_create.php" method="post">
							<label for="username">Username:</label>
							<input type="text" id="username" name="username" required>
							<label for="password">Password:</label>
							<input type="password" id="password" name="password" required>
							<label for="email">Email:</label>
							<input type="email" id="email" name="email" required>
							<label for="admin">Admin:</label>
							<input type="checkbox" id="admin" name="admin">
							<br>
							<input type="submit" value="Submit">
						</form>
					</div>
					<div class="user-list">
						<h3>List of Users</h3>
						<button id="showUsers">Afficher la liste</button>
						<div id="userListContainer" style="display: none;">
							<?php
							connexion_bdd();
							echo "<h4>Users</h4>";
							$sql = "SELECT * FROM UTILISATEUR"; // Replace 'users' with your actual users table name
							$result = $conn->query($sql);
							if ($result->num_rows > 0) {
								echo "<ul>";
								while($row = $result->fetch_assoc()) {
									echo "<li>" . $row["identifiant"] . "</li>";
								}
								echo "</ul>";
							} else {
								echo "No users found";
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>

	</script>
</body>

</html>
