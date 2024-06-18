<?php
// Inclusion du fichier de connexion à la base de données
require_once '../mysql/connexion_bdd.php';

// Démarrage de la session
session_start();

// Enregistrement des logs pour le débogage
if (isset($_SESSION['expiration'])) {
    error_log("Session expiration time: " . $_SESSION['expiration']);
    error_log("Current time: " . time());
}

// Vérification de l'expiration de la session
if (isset($_SESSION['expiration']) && time() < $_SESSION['expiration']) {
    // Redirection vers la page précédente ou la page principale si la session est valide
    if (isset($_SESSION['page_precedente'])) {
        $path = $_SESSION['page_precedente'];
        header("Location: $path");
        exit();
    } else {
        header("Location: ../pages/main.php");
        exit();
    }
}

// Connexion à la base de données
$dbh = connexion_bdd();

// Récupérer la limite d'essais de connexion depuis les paramètres
$sql = $dbh->prepare('SELECT limite FROM PARAMETRES_BACK WHERE Parametres_Back_ID = 0');
$sql->execute();
$resultat = $sql->fetchAll(PDO::FETCH_ASSOC);
$limite = $resultat[0]['limite'];

// Vérification du nombre d'essais de connexion
if (isset($_SESSION['essais'])) {
    if ($_SESSION['essais'] % $limite == 0 && $_SESSION['essais'] != 0) {
        // Si le nombre d'essais est atteint, récupération du temps d'attente
        $sql = $dbh->prepare('SELECT temps FROM PARAMETRES_BACK WHERE Parametres_Back_ID = 0');
        $sql->execute();
        $resultat = $sql->fetchAll(PDO::FETCH_ASSOC);
        $temps = $resultat[0]['temps'];
        
        echo "<div id='temps' temps='$temps'></div>";
        echo "<div id='data1' data-essais='1'></div>";
    } else {
        echo "<div id='data1' data-essais='0'></div>";
    }
} else {
    echo "<div id='data1' data-essais='0'></div>";
    $_SESSION['essais'] = 0;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion</title>
    <!-- Préconnexion aux Google Fonts pour améliorer les performances -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
    <!-- Spécification du protocole HTTPS pour améliorer la sécurité -->
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!-- Activation du CSP pour améliorer la sécurité -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' https://*; script-src 'self'; style-src 'self' 'unsafe-inline'; object-src 'none'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'">
</head>
<body>
<div class="container" id="container">
    <div class="form-container sign-up-container">
        <form action="../mysql/traitement_signup.php" method="POST">
            <h1>S'inscrire</h1>
            <?php
                // Affichage des messages d'erreur en cas de statut d'inscription
                if (isset($_GET['statut'])){
                    $statut = htmlspecialchars($_GET['statut']); // Sécurisation de l'entrée utilisateur
                    if ($statut === 'email_used') {
                        echo('<span style="color:red; font-weight:bold;">Email déjà utilisé</span>');
                    } elseif ($statut === 'username_used') {
                        echo('<span style="color:red; font-weight:bold;">Nom d\'utilisateur déjà utilisé</span>');
                    } elseif ($statut === 'email_and_username_used') {
                        echo('<span style="color:red; font-weight:bold;">Email et nom d\'utilisateur déjà utilisés</span>');
                    } elseif (ctype_digit($statut)) {
                        $nb = htmlspecialchars($statut);
                        echo("<span style='color:red; font-weight:bold;'>Mot de passe incorrect : Minimum {$nb} caractères</span>");
                    }
                }
            ?>
            <input type="text" placeholder="Name" name="username" required/>
            <input type="email" placeholder="Email" name="email" required/>
            <input type="password" placeholder="Password" name="password" required/>
            <input id="verificationInput" type="verification" placeholder="Code de vérification" name="verification" style="display: none;" />            
            <button id="signupButton" type="submit">S'inscrire</button>
        </form>
    </div>
    <div class="form-container sign-in-container">
        <form action="../mysql/traitement_login.php" method="POST" id="login-form">
            <h1>Connexion</h1>
            <h2 id="countdown"></h2>
            <?php
                // Affichage des messages d'erreur en cas de statut de connexion
                if (isset($_GET['statut'])){
                    $statut = htmlspecialchars($_GET['statut']); // Sécurisation de l'entrée utilisateur
                    if ($statut === 'session_expiree'){
                        $_SESSION['essais'] = 0;
                        echo('<span style="color:red; font-weight:bold;">Déconnecté(e) : Session expirée</span>');
                    } elseif ($statut === 'echec'){
                        echo('<span style="color:red; font-weight:bold;">Mot de passe ou email incorrect</span>');
                    } 
                }
            ?>
            <div id="champs">
                <input type="email" placeholder="Email" name="email" required/>
                <input type="password" placeholder="Password" name="password" required/>
                <p class="captcha-question" id="captcha-question"></p>
                <input type="text" placeholder="Votre réponse ici" name="captcha" id="captcha-answer" required>
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
    // Connexion à la base de données pour récupérer les questions de captcha actives
    $dbh = connexion_bdd();
    $sql = $dbh->prepare('SELECT question, reponse FROM CAPTCHA WHERE actif = 1');
    $sql->execute();
    $resultat = $sql->fetchAll(PDO::FETCH_ASSOC);     
?>
<!-- Stockage des données captcha dans un attribut data -->
<div id="data" data-captcha='<?php echo json_encode($resultat, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>'></div>
<div id="output"></div>
<script src="../js/login.js"></script>
</body>
</html>
