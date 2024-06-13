<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Importation des classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/PHPMailer/src/PHPMailer.php";
require "../vendor/PHPMailer/src/SMTP.php";
require "../vendor/PHPMailer/src/Exception.php";

require_once '../mysql/connexion_bdd.php';
session_start();

$mail = new PHPMailer(true);
$conn = connexion_bdd();

function getAllSubscribers($conn) {
    $subscribers = [];
    $sql = "SELECT Email FROM UTILISATEUR WHERE derniere_connexion <= DATE_SUB(NOW(), INTERVAL 30 DAY) AND Abonnement_NL = 1";
    if ($result = $conn->query($sql)) {
        while($row = $result->fetch_assoc()) {
            $subscribers[] = $row['Email'];
        }
    }
    return $subscribers;
}

function sendNewsletter($conn, $mail, $subject, $message) {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('SMTP_USERNAME');
    $mail->Password = getenv('SMTP_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('staff.aurana@gmail.com', 'Équipe Aurana');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;

    $subscribers = getAllSubscribers($conn);
    foreach ($subscribers as $email) {
        $mail->addAddress($email);
        if (!$mail->send()) {
            echo "Erreur d'envoi à $email: " . $mail->ErrorInfo . "<br>";
        } else {
            echo "Email envoyé avec succès à $email<br>";
        }
        $mail->clearAddresses();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['preview'])) {
    // Logique de prévisualisation
    echo "<h2>Prévisualisation :</h2>";
    echo "<p><strong>Sujet :</strong> " . $_POST['subject'] . "</p>";
    echo "<p><strong>Message :</strong> " . nl2br($_POST['message']) . "</p>";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    // Logique d'envoi
    sendNewsletter($conn, $mail, $_POST['subject'], $_POST['message']);
}
?>