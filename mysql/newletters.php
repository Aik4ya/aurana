<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Importer les classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/PHPMailer/src/PHPMailer.php";
require "../vendor/PHPMailer/src/SMTP.php";
require "../vendor/PHPMailer/src/Exception.php";

$mail = new PHPMailer(true);

// Connexion à la base de données
require_once '../mysql/connexion_bdd.php';

// Démarrer la session
session_start();

// Fonction pour récupérer les abonnés
function getAllSubscribers()
{
    $subscribers = [];
    $conn = connexion_bdd();
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $sql = $conn->prepare("SELECT Email FROM UTILISATEUR WHERE Abonnement_NL = 1");
    $sql->execute();
    
    while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $subscribers[] = $row;
    }
    return $subscribers;
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    $subscribers = getAllSubscribers();

    // Configurer PHPMailer
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
    
    foreach ($subscribers as $subscriber) {
        $email = $subscriber['Email'];
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
}
?>