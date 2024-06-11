<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'connexion_bdd.php';
require_once 'cookies_uid.php';
require "../vendor/PHPMailer/src/PHPMailer.php";
require "../vendor/PHPMailer/src/SMTP.php";
require "../vendor/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generate_password($length = 3) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $str = '';
    $max = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str.= $chars[random_int(0, $max)];
    }

    return $str;
}

$userId = isset($_GET['userId'])? $_GET['userId'] : null;

if (!$userId) {
    die("ID utilisateur non trouvé.");
}

$dbh = connexion_bdd();
$sql = $dbh->prepare("SELECT Email FROM UTILISATEUR WHERE Utilisateur_ID = :userId");
$sql->bindParam(':userId', $userId, PDO::PARAM_INT);
$sql->execute();
$result = $sql->fetch();

if (!$result) {
    die("Utilisateur non trouvé.");
}

// Générer le nouveau mot de passe
$newPassword = generate_password();

// Hacher le nouveau mot de passe
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Mise à jour du mot de passe dans la base de données
$updateSql = $dbh->prepare("UPDATE UTILISATEUR SET Mot_de_passe = :hashedPassword WHERE Utilisateur_ID = :userId");
$updateSql->bindParam(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
$updateSql->bindParam(':userId', $userId, PDO::PARAM_INT);
$updateSql->execute();

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = "staff.aurana@gmail.com";
    $mail->Password = "bphm shjn sdpq erno"; // Assurez-vous que ceci est le mot de passe d'application généré
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPDebug = 2;

    $mail->setFrom('staff.aurana@gmail.com', 'Equipe Aurana');

    $mail->addAddress($result['Email']);

    $mail->isHTML(true);
    $mail->Subject = "Reinitialisation de votre mot de passe";
    $mail->Body = "Votre nouveau mot de passe est : ".$newPassword.". Veuillez le changer dès que possible.";

    $mail->send();
    echo 'Le Message a bien été envoyé';
    } catch (Exception $e) {
    echo "Le Message ne sait pas envoyé error: {$mail->ErrorInfo}";
    }
?>
