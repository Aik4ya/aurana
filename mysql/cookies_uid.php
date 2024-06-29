<?php 

function ecriture_log($page)
{
    //ecriture log sur le serv
    $uid = $_SESSION['Utilisateur_ID'];
    $entree_log = date('Y-m-d H:i:s') . " - Utilisateur $uid a visité la page $page\n";
    $nom_fichier = "log-" . date('Y-m-d') . '.txt';
    $path = "../mysql/log/" . $nom_fichier;

    if (file_exists($path)) {
        file_put_contents($path, $entree_log, FILE_APPEND | LOCK_EX);
    } else {
        touch($path);
        file_put_contents($path, $entree_log, FILE_APPEND | LOCK_EX);
    }
}

//vérif session au chargement
function verif_session()
{
    include_once 'connexion_bdd.php';

    session_start();
    $conn = connexion_bdd();

    $sql_update = $conn->prepare("UPDATE UTILISATEUR SET derniere_connexion = NOW() WHERE Utilisateur_ID = :Utilisateur_ID");
    $sql_update->bindParam(':Utilisateur_ID', $_SESSION['Utilisateur_ID']);
    $sql_update->execute();

    if (!isset($_SESSION['expiration']) || time() > $_SESSION['expiration']) { //variable inexistante (accès à la page sans connexion) ou session expirée
        session_destroy();
        header("Location: ../pages/login.php?statut=session_expiree");
        exit();
    }
}
?>
