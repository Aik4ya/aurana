<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../mysql/connexion_bdd.php';
require_once '../mysql/cookies_uid.php';

session_start();

$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

verif_session();

if ($_SESSION['Droit'] == 0) {
    http_response_code(403);
    exit("Accès interdit.");
}

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    reactivateUser($userId);
} else {
    http_response_code(400);
    exit("Paramètre userId manquant.");
}

function reactivateUser($userId) {
    try {
        $dbh = connexion_bdd();
        $stmt = $dbh->prepare("UPDATE UTILISATEUR SET Désactivé = 0 WHERE Utilisateur_ID = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        http_response_code(200);
        echo "Utilisateur réactivé avec succès.";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Erreur lors de la réactivation de l'utilisateur : " . $e->getMessage();
    }
}
?>
