<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion des fichiers nécessaires
require_once 'connexion_bdd.php';
require_once 'cookies_uid.php';
require "../vendor/PHPMailer/src/PHPMailer.php";
require "../vendor/PHPMailer/src/SMTP.php";
require "../vendor/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;

// Fonction pour envoyer l'email de vérification
function sendMail($send_to, $otp, $name) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->Username = "staff.aurana@gmail.com";
        $mail->Password = "bphmshjnsdpqerno";
        $mail->setFrom("staff.aurana@gmail.com", "Equipe Aurana");
        $mail->addAddress($send_to);
        $mail->Subject = "Activation du compte";
        $mail->Body = "Bonjour, {$name}\nTon compte a été créé ! Rentrez le code suivant pour valider ton inscription : {$otp}.";
        $mail->send();

        // Rediriger vers la page de vérification
        header("Location: ../pages/verif.php");
        exit();
    } catch (Exception $e) {
        echo "Erreur d'envoi de mail : " . $e->getMessage();
    }
}

// Fonction pour stocker temporairement les détails de l'utilisateur
function store_temp_user($dbh, $username, $email, $password, $otp) {
    try {
        $sql = $dbh->prepare('INSERT INTO TEMP_USERS (Username, Email, Password, OTP, Expiration) VALUES (:Username, :Email, :Password, :OTP, :Expiration)');
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $expiration = time() + (5 * 60); // OTP valable 5 minutes

        $sql->bindParam(':Username', $username);
        $sql->bindParam(':Email', $email);
        $sql->bindParam(':Password', $hashedPassword);
        $sql->bindParam(':OTP', $otp);
        $sql->bindParam(':Expiration', $expiration);

        $sql->execute();

        // Stocker les données dans la session pour une vérification ultérieure
        session_start();
        $_SESSION['temp_user'] = [
            'email' => $email
        ];

        sendMail($email, $otp, $username);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Fonction pour traiter l'inscription
function process_signup($dbh) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];
        $otp = random_int(100000, 999999);

        // Vérifier si l'email ou le nom d'utilisateur est déjà utilisé
        $sql_check = $dbh->prepare('SELECT COUNT(*) AS count FROM UTILISATEUR WHERE Email = :Email OR Pseudo = :Username');
        $sql_check->bindParam(':Email', $email);
        $sql_check->bindParam(':Username', $username);
        $sql_check->execute();
        $result = $sql_check->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // Vérifier si l'email ou le nom d'utilisateur est déjà utilisé et rediriger avec le bon statut
            $sql_check_email = $dbh->prepare('SELECT COUNT(*) AS count FROM UTILISATEUR WHERE Email = :Email');
            $sql_check_email->bindParam(':Email', $email);
            $sql_check_email->execute();
            $result_email = $sql_check_email->fetch(PDO::FETCH_ASSOC);

            $sql_check_username = $dbh->prepare('SELECT COUNT(*) AS count FROM UTILISATEUR WHERE Pseudo = :Username');
            $sql_check_username->bindParam(':Username', $username);
            $sql_check_username->execute();
            $result_username = $sql_check_username->fetch(PDO::FETCH_ASSOC);

            if ($result_email['count'] > 0 && $result_username['count'] > 0) {
                header("Location: ../pages/login.php?statut=email_and_username_used");
            } elseif ($result_email['count'] > 0) {
                header("Location: ../pages/login.php?statut=email_used");
            } elseif ($result_username['count'] > 0) {
                header("Location: ../pages/login.php?statut=username_used");
            }
            exit();
        }

        // Vérifier la longueur du mot de passe
        $longueurmdp = 8; // longueur minimale du mot de passe
        if (strlen($password) < $longueurmdp) {
            header("Location: ../pages/login.php?statut=$longueurmdp");
            exit();
        }

        // Stocker temporairement l'utilisateur
        store_temp_user($dbh, $username, $email, $password, $otp);
    }
}

process_signup(connexion_bdd());
?>
