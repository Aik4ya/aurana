<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'connexion_bdd.php';

session_start();

function creation_tache($dbh) {
    try {
        echo '<pre>';
        var_dump($_POST);
        echo '</pre>';
        
        $Texte = $_POST['text'];
        $Date_Creation = date("Y-m-d"); // Date de création actuelle
        $Categorie = $_POST['categorie'];
        $Groupe_ID = $_POST['groupeID'];
        $Date_Tache = isset($_POST['date_tache']) && !empty($_POST['date_tache']) ? $_POST['date_tache'] : null;
        $Utilisateur_ID = $_SESSION['Utilisateur_ID'];
        $Projet_ID = isset($_POST['project']) ? $_POST['project'] : null; // ID du projet sélectionné ou null pour personnel

        // Vérifier l'existence du groupe
        $groupeCheckSql = $dbh->prepare('SELECT COUNT(*) FROM GROUPE WHERE Groupe_ID = :Groupe_ID');
        $groupeCheckSql->bindParam(':Groupe_ID', $Groupe_ID);
        $groupeCheckSql->execute();
        if ($groupeCheckSql->fetchColumn() == 0) {
            throw new Exception("Le Groupe_ID spécifié n'existe pas.");
        }

        // Insérer la tâche
        $sql = $dbh->prepare('INSERT INTO TACHE (Texte, Date_Creation, Categorie, Groupe_ID, stars, done, Date_Tache) VALUES (
            :Texte, :Date_Creation, :Categorie, :Groupe_ID, 0, 0, :Date_Tache)');
        $sql->bindParam(':Texte', $Texte);
        $sql->bindParam(':Date_Creation', $Date_Creation);
        $sql->bindParam(':Categorie', $Categorie);
        $sql->bindParam(':Groupe_ID', $Groupe_ID);
        $sql->bindParam(':Date_Tache', $Date_Tache);
        $sql->execute();
        $Tache_ID = $dbh->lastInsertId();

        // Assigner la tâche à l'utilisateur
        $assignerSql = $dbh->prepare('INSERT INTO es_assigner (Utilisateur_ID, Tache_ID) VALUES (:Utilisateur_ID, :Tache_ID)');
        $assignerSql->bindParam(':Utilisateur_ID', $Utilisateur_ID);
        $assignerSql->bindParam(':Tache_ID', $Tache_ID);
        $assignerSql->execute();

        // Lier la tâche au projet si un projet est spécifié
        if (!empty($Projet_ID)) {
            $projetAssignSql = $dbh->prepare('INSERT INTO tache_assignee_projet (id_tache, id_projet) VALUES (:Tache_ID, :Projet_ID)');
            $projetAssignSql->bindParam(':Tache_ID', $Tache_ID);
            $projetAssignSql->bindParam(':Projet_ID', $Projet_ID);
            $projetAssignSql->execute();
        }

        header("Location: ../pages/main.php?groupe={$_SESSION['Groupe']}");
        exit();

    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
    } catch (PDOException $e) {
        echo 'Erreur PDO: ' . $e->getMessage();
    }
}

creation_tache(connexion_bdd());

?>
