<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../mysql/connexion_bdd.php';
require_once '../mysql/cookies_uid.php';
require "../vendor/PHPMailer/src/PHPMailer.php";
require "../vendor/PHPMailer/src/SMTP.php";
require "../vendor/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

verif_session();

if ($_SESSION['Droit'] == 0) {
    http_response_code(403);
    exit("Accès interdit.");
}

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    reactivateUser($userId);
} else {
    http_response_code(400);
    exit("Paramètre userId manquant.");
}

function reactivateUser($userId) {
    try {
        $dbh = connexion_bdd();
        $stmt = $dbh->prepare("UPDATE UTILISATEUR SET Désactivé = 0 WHERE Utilisateur_ID = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        $stmt_email = $dbh->prepare("SELECT Email FROM UTILISATEUR WHERE Utilisateur_ID = :userId");
        $stmt_email->bindParam(':userId', $userId);
        $stmt_email->execute();
        $user = $stmt_email->fetch();
        $email = $user['Email'];
        sendNotificationEmail($email);


        http_response_code(200);
        echo "Utilisateur réactivé avec succès.";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Erreur lors de la réactivation de l'utilisateur : " . $e->getMessage();
    }
}


function sendNotificationEmail($email) {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'staff.aurana@gmail.com'; 
        $mail->Password = 'bphm shjn sdpq erno'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPDebug = 2; 

        $mail->setFrom('staff.aurana@gmail.com', 'Equipe Aurana');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Compte Reactiver';
        $mail->Body = 'Votre compte a été réactiver.<br>Vous pouvez vous reconnectez avec votre compte :)<br>https://myaurana.com/pages/login.php<br>Cordialement<br>L\'Equipe Aurana';

        $mail->send();
        echo 'Le Message a bien été envoyé';
    } catch (Exception $e) {
        echo "Le Message ne sait pas envoyé error: {$mail->ErrorInfo}";
    }
}



?>
