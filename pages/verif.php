<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$email = isset($_SESSION['temp_user']['email']) ? $_SESSION['temp_user']['email'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de vérification</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/verif.css">
    <!-- C'est pour spécifier le protocole HTTPS -->
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!-- Sa active le CSP pour améliorer la sécurité -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' https://*; script-src 'self'; style-src 'self' 'unsafe-inline'; object-src 'none'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'">
</head>
<body>
<div class="container" id="container">
    <form action="../mysql/traitement_verif.php" method="post">
        <h1>Code de vérification</h1>
        <input id="verificationInput" type="text" placeholder="Code de vérification" name="verification" required/>
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>"/>
        <button id="verifBtn" type="submit">Vérifier</button>
    </form>
</div>
</body>
</html>
