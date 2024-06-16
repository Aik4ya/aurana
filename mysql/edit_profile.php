<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'connexion_bdd.php';  // Assurez-vous que ce chemin est correct
session_start();

$conn = connexion_bdd();  // Établissez la connexion à la base de données

$utilisateur_id = $_SESSION['Utilisateur_ID'];
$username = $_POST['username'];
$email = $_POST['email'];

// Vérification et création du répertoire des avatars
$avatar_directory = '../uploads/avatars/';
if (!is_dir($avatar_directory)) {
    mkdir($avatar_directory, 0755, true);
}

// Vérification et upload de l'avatar
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);

    if (in_array($file_extension, $allowed_extensions)) {
        $avatar_filename = $utilisateur_id . '.' . $file_extension;
        $avatar_path = $avatar_directory . $avatar_filename;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
            // Mise à jour de l'avatar dans la base de données
            $sql = "UPDATE UTILISATEUR SET Avatar = :avatar WHERE Utilisateur_ID = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':avatar', $avatar_filename);
            $stmt->bindParam(':id', $utilisateur_id);
            $stmt->execute();

            // Mise à jour de la session
            $_SESSION['Avatar'] = $avatar_filename;
        } else {
            echo "Erreur lors du téléchargement de l'avatar.";
            exit;
        }
    } else {
        echo "Extension de fichier non autorisée.";
        exit;
    }
}

// Mise à jour des informations de l'utilisateur
$sql = "UPDATE UTILISATEUR SET Pseudo = :username, Email = :email WHERE Utilisateur_ID = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':id', $utilisateur_id);
$stmt->execute();

header('Location: ../pages/main_profile.php');
?>
