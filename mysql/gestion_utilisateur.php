<?php

require_once 'connexion_bdd.php';

function deactivate_user($bdh)
{
    $uid = $_POST['uid'];
    $sql = $dbh->prepare('UPDATE UTILISATEUR
    SET Désactivé = 1
    WHERE Utilisateur_ID = :Utilisateur_ID
    ');

    $sql->bindParam(':Utilisateur_ID', $uid);

    $sql->execute();
}

function reactivate_user($bdh)
{
    $uid = $_POST['uid'];
    $sql = $dbh->prepare('UPDATE UTILISATEUR
    SET Désactivé = 0
    WHERE Utilisateur_ID = :Utilisateur_ID
    ');

    $sql->bindParam(':Utilisateur_ID', $uid);

    $sql->execute();
}

function delete_user($bdh)
{
    $uid = $_POST['uid'];
    $sql = $dbh->prepare("DELETE FROM UTILISATEUR WHERE Utilisateur_ID = :Utilisateur_ID");

        $sql->bindParam(':Utilisateur_ID', $uid);

        $sql->execute();
}