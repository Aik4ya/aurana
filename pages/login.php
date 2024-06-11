<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once '../mysql/connexion_bdd.php';

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

    $dbh = connexion_bdd();

    $sql = $dbh->prepare('SELECT limite FROM PARAMETRES_BACK WHERE Parametres_Back_ID = 0');
    $sql->execute();
    $resultat = $sql->fetchAll(PDO::FETCH_ASSOC);
    $limite = $resultat[0]['limite'];



    if (isset($_SESSION['essais']))
    {
        if ($_SESSION['essais'] % $limite == 0)
        {
            $sql = $dbh->prepare('SELECT temps FROM PARAMETRES_BACK WHERE Parametres_Back_ID = 0');
            $sql->execute();
            $resultat = $sql->fetchAll(PDO::FETCH_ASSOC);
            $temps = $resultat[0]['temps'];
        
            echo "<div id='temps' temps = $temps></div>";
            echo "<div id='data1' data-essais = 1></div>";
        }

        else
        {
            echo "<div id='data1' data-essais = 0></div>";
            echo $_SESSION['essais'];

        }
    }
    else
    {
        echo "<div id='data1' data-essais = 0></div>";
        $_SESSION['essais'] = 0;
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
            <?php
                if (isset($_GET['statut'])){
                    if(ctype_digit($_GET['statut'])){
                        $nb = $_GET['statut'];
                        echo("<span style='color:red; font-weight:bold;'>Mot de passe incorrect : Minimum {$_GET['statut']} caractères</span>");
                    }
                }
            ?>
            <input type="text" placeholder="Name" name="username"/>
            <input type="email" placeholder="Email" name="email"/>
            <input type="password" placeholder="Password" name="password" />
            <input id="verificationInput" type="verification" placeholder="Code de vérification" name="verification" style="display: none;" />            
            <button id="signupButton" type="submit">S'inscrire</button>
        </form>
    </div>
    <div class="form-container sign-in-container">
      <form action="../mysql/traitement_login.php" method="POST" id="login-form">
            <h1>Connexion</h1>
            <h2 id="countdown"></h2>
            <?php
                if (isset($_GET['statut'])){
                    if ($_GET['statut'] === 'session_expiree'){
                        echo('<span style="color:red; font-weight:bold;">Déconnecté(e) : Session expirée</span>');
                    }

                    elseif ($_GET['statut'] === 'echec'){
                        echo('<span style="color:red; font-weight:bold;">Mot de passe ou email incorrect</span>');
                    } 
                }
            ?>
            <div id="champs">
                <input type="email" placeholder="Email" name="email" />
                <input type="password" placeholder="Password" name="password" />
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
<?php
    $dbh = connexion_bdd();
    $sql = $dbh->prepare('SELECT question, reponse FROM CAPTCHA WHERE actif = 1');
        $sql->execute();
        $resultat = $sql->fetchAll(PDO::FETCH_ASSOC);     
?>
<div id="data" data-captcha='<?php echo json_encode($resultat, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>'></div>
<div id="output"></div>
<script src="../js/login.js"></script>
</body>
</html>
