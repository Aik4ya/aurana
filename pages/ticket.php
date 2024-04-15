<?php
require '../mysql/cookies_uid.php';

$page = 'main';
$uid = lecture_cookie_uid();
ecriture_log($uid, $page);

function connexion_bdd()
{
    $bdd_login = 'aurana';
    $bdd_password = 'aurana2024';

    try {
        $dbh = new PDO('mysql:host=localhost;dbname=Aurana_bdd', $bdd_login, $bdd_password);
        return $dbh;

    } catch (PDOException $e) {
        var_dump($e);
        return null;
    }
}

function afficher_tickets($dbh, $uid, $isAdmin)
{
    try {
        if ($isAdmin) {
            $stmt = $dbh->query("SELECT * FROM TICKET");
        } else {
            $stmt = $dbh->prepare("SELECT * FROM TICKET WHERE Demandeur_ID = ?");
            $stmt->execute([$uid]);
        }

        echo "<table border='1'>
                <tr>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Date de création</th>
                    <th>Priorité</th>
                    <th>État</th>
                    <th>Description</th>
                    <th>Email du demandeur</th>
                    <th>Action</th>
                </tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['Titre_Ticket'] . "</td>";
            echo "<td>" . $row['Categorie_Ticket'] . "</td>";
            echo "<td>" . $row['Date_Creation'] . "</td>";
            echo "<td>" . $row['Priorite'] . "</td>";
            echo "<td>" . $row['Etat'] . "</td>";
            echo "<td>" . $row['Description_Ticket'] . "</td>";
            
            // Récupérer l'email du demandeur
            $demandeurId = $row['Demandeur_ID'];
            $stmt_demandeur = $dbh->prepare("SELECT Email FROM UTILISATEUR WHERE Utilisateur_ID = ?");
            $stmt_demandeur->execute([$demandeurId]);
            $row_demandeur = $stmt_demandeur->fetch(PDO::FETCH_ASSOC);
            $email_demandeur = $row_demandeur['Email'];

            echo "<td>" . $email_demandeur . "</td>";

            // Ajout du bouton "Répondre" avec un formulaire pour chaque ticket
            echo "<td>
                    <form action='' method='post'>
                        <input type='hidden' name='ticketId' value='" . $row['Ticket_ID'] . "'>
                        <textarea name='reponse' rows='4' cols='40' placeholder='Votre réponse'></textarea><br>
                        <input type='submit' value='Répondre'>
                    </form>
                </td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
    }
}

function repondre_ticket($dbh, $ticketId, $reponse)
{
    try {
        // Récupérer les emails des demandeurs de ticket
        $stmt = $dbh->prepare("SELECT Email FROM UTILISATEUR WHERE Utilisateur_ID IN (SELECT Demandeur_ID FROM TICKET WHERE Ticket_ID = ?)");
        $stmt->execute([$ticketId]);
        $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Envoyer la réponse par email à chaque demandeur de ticket
        foreach ($emails as $email) {
            $subject = "Réponse à votre ticket";
            $message = "Votre ticket a reçu une réponse. Veuillez vous connecter pour consulter la réponse.";
            $headers = "From: staff.aurana@gmail.com";

            mail($email, $subject, $message, $headers);
        }

        // Mettre à jour la base de données avec la réponse
        $stmt = $dbh->prepare("UPDATE TICKET SET Reponse = ? WHERE Ticket_ID = ?");
        $stmt->execute([$reponse, $ticketId]);

        echo "Réponse envoyée avec succès.";
    } catch (PDOException $e) {
        echo "Erreur lors de la réponse au ticket : " . $e->getMessage();
    }
}

$dbh = connexion_bdd();

if ($dbh) {
    try {
        $stmt = $dbh->prepare("SELECT Droit FROM UTILISATEUR WHERE Utilisateur_ID = ?");
        $stmt->execute([$uid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $isAdmin = ($row['Droit'] == 1);

        afficher_tickets($dbh, $uid, $isAdmin);
    } catch (PDOException $e) {
        echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
    }
} else {
    echo "Erreur de connexion à la base de données.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketId = $_POST['ticketId'];
    $reponse = $_POST['reponse'];

    repondre_ticket($dbh, $ticketId, $reponse);
}
?>
