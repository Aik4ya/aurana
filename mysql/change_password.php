<?php
$conn = connexion_bdd();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = $conn->prepare("SELECT Mot_De_Passe FROM UTILISATEUR WHERE ID_Utilisateur = :id");
$sql->bindParam(':id', $_SESSION['ID_Utilisateur']);
$sql->execute();
$result = $sql->fetch(PDO::FETCH_ASSOC);

if (password_verify($_POST['currentPassword'], $result['Mot_De_Passe'])) {
    if ($_POST['newPassword'] === $_POST['confirmPassword']) {
        $sqlupdate = $conn->prepare("UPDATE UTILISATEUR SET Mot_De_Passe = :password WHERE ID_Utilisateur = :id");
        $sqlupdate->bindParam(':id', $_SESSION['ID_Utilisateur']);
        $sqlupdate->bindParam(':password', password_hash($_POST['newPassword'], PASSWORD_BCRYPT));
        $sqlupdate->execute();
        echo "Mot de passe mis à jour avec succès";
    } else {
        echo "Les nouveaux mots de passe ne correspondent pas";
    }
} else {
    echo "Mot de passe incorrect";
}
?>