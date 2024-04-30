<?php
function connexion_bdd()
{
    $bdd_login = 'aurana';
    $bdd_password = 'aurana2024';

    try {
        $dbh = new PDO('mysql:host=localhost;dbname=Aurana_bdd', $bdd_login, $bdd_password);
        return $dbh;

    } catch (PDOException $e) {
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
        return null;
    }
}

connexion_bdd();