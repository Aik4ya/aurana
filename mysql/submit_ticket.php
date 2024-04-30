<?php

require_once '../mysql/connexion_bdd.php';
require '../mysql/cookies_uid.php';

session_start();
$uid = $_SESSION['uid'];

// Vérifier si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire et les filtrer
    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $categorie = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $priorite = filter_input(INPUT_POST, 'priorite', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Récupérer l'ID de l'utilisateur à partir des cookies
    $demandeur_id = $uid;

    // Récupérer la date actuelle
    $date_creation = date('Y-m-d H:i:s');

    // État du ticket
    $etat = "En attente";

    // Connexion à la base de données
    $dbh = connexion_bdd();

    if ($dbh) {
        try {
            // Préparer la requête d'insertion
            $stmt = $dbh->prepare("INSERT INTO TICKET (Titre_Ticket, Description_Ticket, Categorie_Ticket, Priorite, Demandeur_ID, Date_Creation, Etat) VALUES (?, ?, ?, ?, ?, ?, ?)");

            // Exécuter la requête avec les valeurs du formulaire, la date actuelle et l'état
            $stmt->execute([$titre, $description, $categorie, $priorite, $demandeur_id, $date_creation, $etat]);

            // Vérifier si l'insertion a réussi
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
