<?php
	require_once '../mysql/ticket.php';
	require_once '../mysql/cookies_uid.php';
	
	$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

	$log = log_visite($_SERVER['REQUEST_URI']);
	ecriture_log($log['log']);
	verif_session();

	if ($_SESSION['Droit'] == 0) {
        header('Location: ../pages/403.html');
        exit();
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
	<?php
	afficher_tickets(connexion_bdd(),lecture_cookie_uid(), $_SESSION['Droit']);
	?>
</div>
</body>
</html>