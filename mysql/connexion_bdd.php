<?php
function connexion_bdd()
{
    $bdd_login = 'aurana';
    $bdd_password = 'gH98SxjWP3hesN8h7Z94';

    try {
        $dbh = new PDO('mysql:host=localhost;dbname=Aurana_bdd', $bdd_login, $bdd_password);
        return $dbh;

    } catch (PDOException $e) {
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
        return null;
    }
}

connexion_bdd();