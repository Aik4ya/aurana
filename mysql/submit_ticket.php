<?php

require_once '../mysql/connexion_bdd.php';
require '../mysql/cookies_uid.php';

session_start();
$uid = $_SESSION['uid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $categorie = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $priorite = filter_input(INPUT_POST, 'priorite', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $demandeur_id = $uid;

    $date_creation = date('Y-m-d H:i:s');

    $etat = "En attente";

    $dbh = connexion_bdd();

    if ($dbh) {
        try {
            //envoi ticket
            $stmt = $dbh->prepare("INSERT INTO TICKET (Titre_Ticket, Description_Ticket, Categorie_Ticket, Priorite, Demandeur_ID, Date_Creation, Etat) VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([$titre, $description, $categorie, $priorite, $demandeur_id, $date_creation, $etat]);

            if ($stmt->rowCount() > 0) {
                echo "Ticket soumis avec succès.";
            } else {
                echo "Erreur lors de la soumission du ticket.";
            }
        } catch (PDOException $e) {
            echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
        }
    } else {
        echo "Erreur de connexion à la base de données.";
    }
} else {
    echo "Erreur : méthode de requête incorrecte.";
}
?>
