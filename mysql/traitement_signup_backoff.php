<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../mysql/cookies_uid.php';
require_once '../mysql/connexion_bdd.php';
require "../vendor/PHPMailer/src/PHPMailer.php";
require "../vendor/PHPMailer/src/SMTP.php";
require "../vendor/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

ecriture_log("backoffice");
verif_session();

if ($_SESSION['Droit'] == 0) {
    header('Location: ../pages/403.html');
    exit();
}

$pseudo = $_POST['Pseudo'];
$identifiant = $_POST['Identifiant'];
$mot_de_passe = $_POST['Mot_de_passe'];
$email = $_POST['Email'];
$droit = isset($_POST['Droit']) ? 1 : 0; 

$dbh = connexion_bdd();
$stmt = $dbh->prepare("INSERT INTO UTILISATEUR (Pseudo, Identifiant, Mot_de_passe, Email, Droit) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$pseudo, $identifiant, $mot_de_passe, $email, $droit]);

// Préparation du contenu de l'email en fonction des droits de l'utilisateur
if ($droit == 1) {
    $subject = "Bienvenue, Administrateur!";
    $message = "Bonjour $pseudo,<br><br>Vous avez été ajouté en tant qu'administrateur à notre système.<br>Vous pouvez maintenant accéder à toutes les fonctionnalités administratives.<br>Voici le lien de notre Système Administrative : https://myaurana.com/backoff/backoff.php<br>Identifiant : $pseudo<br>Mot de passe : $mot_de_passe<br>Email : $email<br>Cordialement, <br>L'équipe.";
} else {
    $subject = "Bienvenue!";
    $message = "Bonjour $pseudo,<br><br>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter à notre système.<br>Identifiant : $pseudo<br>Mot de passe : $mot_de_passe<br>Email : $email<br><br>Cordialement, <br>L'équipe.";
}

// Fonction pour envoyer l'email
function sendEmail($email, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = "staff.aurana@gmail.com";
        $mail->Password = "bphm shjn sdpq erno";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPDebug = 2;
    
        $mail->setFrom('staff.aurana@gmail.com', 'Equipe Aurana');
        $mail->addAddress($email); 
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (sendEmail($email, $subject, $message)) {
    echo "Utilisateur ajouté avec succès et e-mail envoyé.";
} else {
    echo "Erreur lors de l'ajout de l'utilisateur ou de l'envoi de l'e-mail.";
}
?>
