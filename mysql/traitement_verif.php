<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'connexion_bdd.php';
require_once 'cookies_uid.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_otp = htmlspecialchars($_POST['verification']);
    $email = htmlspecialchars($_POST['email']);
    $dbh = connexion_bdd();

    $sql = $dbh->prepare('SELECT * FROM TEMP_USERS WHERE OTP = :otp AND Email = :email AND Expiration > :current_time');
    $sql->bindParam(':otp', $user_otp);
    $sql->bindParam(':email', $email);
    $sql->bindParam(':current_time', time());
    $sql->execute();
    $temp_user = $sql->fetch(PDO::FETCH_ASSOC);

    if ($temp_user) {
        $username = $temp_user['Username'];
        $password = $temp_user['Password'];

        //création définitive après vérif OTP ok
        $sql = $dbh->prepare('INSERT INTO UTILISATEUR (Pseudo, Identifiant, Mot_de_passe, Email, Droit) VALUES (:Username, :Login, :Password, :Email, :Droit)');
        $Droits = 0;

        $sql->bindParam(':Username', $username);
        $sql->bindParam(':Login', $username);
        $sql->bindParam(':Password', $password);
        $sql->bindParam(':Email', $email);
        $sql->bindParam(':Droit', $Droits);
        $sql->execute();

        // Mettre à jour la date d'inscription de l'utilisateur
        $Utilisateur_ID = $dbh->lastInsertID();
        $sql_update = $dbh->prepare("UPDATE UTILISATEUR SET date_inscription = NOW() WHERE Utilisateur_ID = :Utilisateur_ID");
        $sql_update->bindParam(':Utilisateur_ID', $Utilisateur_ID);
        $sql_update->execute();

        // Initialiser session
        $_SESSION['expiration'] = time() + (30 * 24 * 60 * 60); // 30 jours
        $_SESSION['Utilisateur_ID'] = $Utilisateur_ID;
        $_SESSION['Droit'] = $Droits;
        $_SESSION['Pseudo'] = $username;
        $_SESSION['Email'] = $email;

        // Supprimer données temporaire
        $sql_delete = $dbh->prepare('DELETE FROM TEMP_USERS WHERE OTP = :otp');
        $sql_delete->bindParam(':otp', $user_otp);
        $sql_delete->execute();

        // Rediriger vers page principale
        header("Location: ../pages/main.php?status=success");
        exit();
    } else {
        // OTP invalide ou expiré
        header("Location: ../pages/verif.php?status=failed");
        exit();
    }
} else {
    // Méthode de requête invalide
    header("Location: ../pages/verif.php?status=invalidrequest");
    exit();
}
?>
