<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "/var/www/html/vendor/PHPMailer/src/PHPMailer.php";
require "/var/www/html/vendor/PHPMailer/src/SMTP.php";
require "/var/www/html/vendor/PHPMailer/src/Exception.php";

$mail = new PHPMailer(true);

require_once '/var/www/html/mysql/connexion_bdd.php';

session_start();

function getAllMemberAfk()
{
    $members = [];
    $conn = connexion_bdd();
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $sql = $conn->prepare("SELECT * FROM UTILISATEUR WHERE DATEDIFF(CURDATE(), derniere_connexion) > 90 ORDER BY derniere_connexion DESC");
    $sql->execute();
    
    while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $members[] = $row;
    }
    return $members;
}

$subject = "Notification d'inactivité";
$message = "Votre dernière activité date de plus de 90 jours. Nous vous prions de vous connecter avant que votre compte soit supprimé.";

$members = getAllMemberAfk();

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = "staff.aurana@gmail.com";
$mail->Password = "bphm shjn sdpq erno";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->SMTPDebug = 2;

$mail->setFrom('staff.aurana@gmail.com', 'Aurana Staff');
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';

foreach ($members as $member) {
    $email = $member['Email'];
    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $message;
    
    if (!$mail->send()) {
        echo "Une erreur est survenue lors de l'envoi à " . $email . ": " . $mail->ErrorInfo;
    } else {
        echo "Email envoyé à " . $email . "<br>";
    }
    
    $mail->clearAddresses();
}
?>