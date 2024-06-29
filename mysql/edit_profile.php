<?php
require '../mysql/cookies_uid.php';
require '../mysql/connexion_bdd.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Invalid request method';
    exit;
}

ecriture_log("main_chat");
verif_session();

// Validation des données reçues
$username = isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : null;
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : null;

if (!$username || !$email) {
    echo 'Missing required fields';
    exit;
}

// Traitement de l'avatar
$avatar = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $avatar = basename($_FILES['avatar']['name']);
    $uploadDir = '../uploads/avatars/';
    $uploadFile = $uploadDir . $_SESSION['Utilisateur_ID'] . '.' . pathinfo($avatar, PATHINFO_EXTENSION);

    // Vérifiez si le répertoire d'upload est accessible
    if (!is_writable($uploadDir)) {
        echo 'Upload directory is not writable.';
        exit;
    }

    // Déplacez le fichier téléchargé
    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
        echo 'Erreur lors du téléchargement de l\'avatar.';
        error_log('Erreur lors du déplacement du fichier téléchargé: ' . print_r($_FILES, true));
        exit;
    }

    $avatar = $_SESSION['Utilisateur_ID'] . '.' . pathinfo($avatar, PATHINFO_EXTENSION);
} else if (isset($_FILES['avatar'])) {
    // Log the error if the file upload fails
    error_log('Erreur lors du téléchargement de l\'avatar: ' . $_FILES['avatar']['error']);
    switch ($_FILES['avatar']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            echo 'Le fichier téléchargé est trop volumineux.';
            break;
        case UPLOAD_ERR_PARTIAL:
            echo 'Le fichier n\'a été que partiellement téléchargé.';
            break;
        case UPLOAD_ERR_NO_FILE:
            echo 'Aucun fichier n\'a été téléchargé.';
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            echo 'Le dossier temporaire est manquant.';
            break;
        case UPLOAD_ERR_CANT_WRITE:
            echo 'Échec de l\'écriture du fichier sur le disque.';
            break;
        case UPLOAD_ERR_EXTENSION:
            echo 'Une extension PHP a arrêté le téléchargement du fichier.';
            break;
        default:
            echo 'Erreur inconnue lors du téléchargement du fichier.';
            break;
    }
    exit;
}

$conn = connexion_bdd();

// Mettre à jour les informations de l'utilisateur
$sql_update = "UPDATE UTILISATEUR SET Pseudo = :username, Email = :email";
if ($avatar) {
    $sql_update .= ", Avatar = :avatar";
}
$sql_update .= " WHERE Utilisateur_ID = :userID";

$stmt_update = $conn->prepare($sql_update);
$stmt_update->bindParam(':username', $username);
$stmt_update->bindParam(':email', $email);
if ($avatar) {
    $stmt_update->bindParam(':avatar', $avatar);
}
$stmt_update->bindParam(':userID', $_SESSION['Utilisateur_ID'], PDO::PARAM_INT);

if ($stmt_update->execute()) {
    $_SESSION['Pseudo'] = $username;
    $_SESSION['Email'] = $email;
    if ($avatar) {
        $_SESSION['Avatar'] = $avatar;
    }
    header('Location: ../pages/main_profile.php');
    exit;
} else {
    echo 'Error updating profile details';
    var_dump($stmt_update->errorInfo());
}
?>
