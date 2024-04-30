<?php

session_start();
if (isset($_SESSION['expiration']) && time() < $_SESSION['expiration'])
{
    if (isset($_SESSION['page_precedente']))
                {
                    $path = $_SESSION['page_precedente'];
                    header("Location: $path");
                    exit();
                }
                
    header("Location: ../pages/main.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="container" id="container">
    <div class="form-container sign-up-container">
        <form action="../mysql/traitement_signup.php" method="POST">
            <h1>S'inscrire</h1>
            <input type="text" placeholder="Name" name="username"/>
            <input type="email" placeholder="Email" name="email"/>
            <input type="password" placeholder="Password" name="password" />
            <button type="submit">S'inscrire</button>
        </form>
    </div>
    <div class="form-container sign-in-container">
      <form action="../mysql/traitement_login.php" method="POST" id="login-form">
            <h1>Connexion</h1>
            <input type="email" placeholder="Email" name="email" />
            <input type="password" placeholder="Password" name="password" />
            <div>
                <p class="captcha-question" id="captcha-question"></p>
                <input type="text" placeholder="Votre réponse ici" name="captcha" id="captcha-answer">
                <button type="button" id="submit-captcha">Connexion</button>
            </div>
            <a href="#">Mot de passe oublié ?</a>
        </form>
    </div>
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Bon retour !</h1>
                <p>Veuillez vous connecter avec vos informations personnelles</p>
                <button class="ghost" id="signIn">Connexion</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Bienvenue !</h1>
                <p>Saisissez vos données personnelles et commencez votre voyage avec nous</p>
                <button class="ghost" id="signUp">S'inscrire</button>
            </div>
        </div>
    </div>
</div>
<script src="../js/login.js"></script>
</body>
</html>
