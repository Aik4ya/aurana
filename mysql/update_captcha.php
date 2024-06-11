<?php
require_once '../mysql/connexion_bdd.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbh = connexion_bdd();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'create':
            $question = $_POST['question'];
            $reponse = $_POST['reponse'];
            break;
    }
} else {
}


function creation_captcha($dbh)
{
    try {
        $sql = $dbh->prepare('INSERT INTO CAPTCHA (question, reponse, actif) VALUES 
        (
        :question,
        :reponse,
        1
        )');

        $question = $_POST['question'];
        $reponse = $_POST['reponse'];

        $sql->bindParam(':question', $question);
        $sql->bindParam(':reponse', $reponse);

        $sql->execute();

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}


function modification_captcha($dbh, $id, $question, $reponse, $actif)
{
    try {
        $sql = $dbh->prepare('UPDATE CAPTCHA SET
            question = :question,
            reponse = :reponse,
            actif = :actif
            WHERE id = :id
        ');

        $sql->bindParam(':id', $id);
        $sql->bindParam(':question', $question);
        $sql->bindParam(':reponse', $reponse);
        $sql->bindParam(':actif', $actif);

        $sql->execute();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

function supression_captcha($dbh, $id)
{
    try {
        $sql = $dbh->prepare('DELETE FROM CAPTCHA WHERE id = :id');

        $sql->bindParam(':id', $id);

        $sql->execute();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
