<?php

#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
#DE L'OR EN BARRE

require_once 'connexion_bdd.php';
require_once 'cookies_uid.php';

require "../vendor/PHPMailer/src/PHPMailer.php";
require "../vendor/PHPMailer/src/SMTP.php";
require "../vendor/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;

function sendMail($send_to, $otp, $name) {
    try{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;


    $mail->Username = "staff.aurana@gmail.com";
    $mail->Password = "bphmshjnsdpqerno";

    $mail->setFrom("staff.aurana@gmail.com", "Equipe Aurana");

    $mail->addAddress($send_to);


    $mail->Subject = "Account Activation";


    $mail->Body = "Hello, {$name}\nTon compte a été créé ! rentre le code suivant pour valider ton inscription : {$otp}.";
    $mail->send();

    header("Location: ../pages/verif.php");
    exit();

    
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

function creation_utilisateur($dbh) {
    try {
        $sql = $dbh->prepare('INSERT INTO UTILISATEUR (Pseudo, Identifiant, Mot_de_passe, Email, Droit) VALUES (:Username, :Login, :Password, :Email, :Droit)');
        
        $Username = $_POST['username'];
        $Login = $Username;
        $longueurmdp = 8; #paramètre back
        if (strlen($_POST['password']) < $longueurmdp){
            header("Location: ../pages/login.php?statut=$longueurmdp");
                exit();
        }
        else {
            $Password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }
        $Email = $_POST['email'];
        $Droits = 0;

        $sql->bindParam(':Username', $Username);
        $sql->bindParam(':Login', $Login);
        $sql->bindParam(':Password', $Password);
        $sql->bindParam(':Email', $Email);
        $sql->bindParam(':Droit', $Droits);

        $sql->execute();
        
        $expiration = time() + (5*60); #trouver un truc pour back
        $Utilisateur_ID = $dbh->lastInsertID();
        // Mettre à jour la date d'inscription de l'utilisateur
        $sql_update = $dbh->prepare("UPDATE UTILISATEUR SET date_inscription = NOW() WHERE Utilisateur_ID = :Utilisateur_ID");
        $sql_update->bindParam(':Utilisateur_ID', $Utilisateur_ID);
        $sql_update->execute();

        session_start();
        $_SESSION['expiration'] = $expiration;
        $_SESSION['Utilisateur_ID'] = $Utilisateur_ID;
        $_SESSION['Droit'] = $Droits;
        $_SESSION['Pseudo'] = $Username;

    $_SESSION["email"] = $_POST["email"];
      

    $_SESSION["otp"] = random_int(100000, 999999);


    $send_to_name = $_POST["username"];

    sendMail($_SESSION["email"], $_SESSION["otp"], $send_to_name);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

creation_utilisateur(connexion_bdd());
