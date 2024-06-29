<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('connexion_bdd.php');
require_once('../vendor/TCPDF/tcpdf.php');

session_start();



function recupererUtilisateurs(){
    $bdd = connexion_bdd();

    //récupère TOUTES les infos de l'utilisateur
    $requete = $bdd->prepare('SELECT * FROM UTILISATEUR
    JOIN est_membre ON UTILISATEUR.Utilisateur_ID = est_membre.Utilisateur_ID
    JOIN GROUPE ON est_membre.GROUPE = GROUPE.Groupe_ID
    JOIN est_membre_projet ON UTILISATEUR.Utilisateur_ID = est_membre_projet.Utilisateur_ID
    JOIN PROJET ON est_membre_projet.Projet_ID = PROJET.ID
    JOIN es_assigner ON UTILISATEUR.Utilisateur_ID = es_assigner.Utilisateur_ID
    JOIN TACHE ON es_assigner.Tache_ID = TACHE.Tache_ID
    WHERE UTILISATEUR.Utilisateur_ID = :id');

    $requete->bindParam(':id', $_SESSION['Utilisateur_ID']);
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 12);

$utilisateurs = recupererUtilisateurs();

//mise en page pour pdf (groupes->projets->taches)
if (!empty($utilisateurs)) {
    $utilisateur = $utilisateurs[0]; 
    $droit = ($utilisateur['Droit'] == 1) ? 'Administrateur' : 'Utilisateur';

    $pdf->Cell(0, 10, 'Nom: ' . $utilisateur['Pseudo'], 0, 1);
    $pdf->Cell(0, 10, 'Email: ' . $utilisateur['Email'], 0, 1);
    $pdf->Cell(0, 10, 'Droit: ' . $droit, 0, 1);
    $pdf->Cell(0, 10, "Date d'inscription: " . $utilisateur['date_inscription'], 0, 1);
    $pdf->Cell(0, 10, 'Dernière activité: ' . $utilisateur['derniere_connexion'], 0, 1);
    $pdf->Ln();

    $data = [];
    foreach ($utilisateurs as $row) {
        $groupeId = $row['GROUPE'];
        $projetId = $row['Projet_ID'];
        $tacheId = $row['Tache_ID'];

        if (!isset($data[$groupeId])) {
            $data[$groupeId] = [
                'Nom' => $row['Nom'],
                'Droit' => $row['droit'],
                'projets' => []
            ];
        }

        if (!isset($data[$groupeId]['projets'][$projetId])) {
            $data[$groupeId]['projets'][$projetId] = [
                'Nom' => $row['nom'],
                'taches' => []
            ];
        }

        $data[$groupeId]['projets'][$projetId]['taches'][$tacheId] = $row['Texte'];
    }

    foreach ($data as $groupeId => $groupe) {
        $droitGroupe = ($groupe['Droit'] == 1) ? 'Administrateur' : 'Utilisateur';

        $pdf->Cell(0, 10, 'Groupe: ' . $groupe['Nom'], 0, 1);
        $pdf->Cell(0, 10, 'Droit: ' . $droitGroupe, 0, 1);

        foreach ($groupe['projets'] as $projetId => $projet) {
            $pdf->Cell(0, 10, 'Projet: ' . $projet['Nom'], 0, 1);

            foreach ($projet['taches'] as $tacheId => $tacheNom) {
                $pdf->Cell(0, 10, 'Tâche: ' . $tacheNom, 0, 1);
            }
            $pdf->Ln();
        }
    }
}

$pdf->Output('utilisateurs.pdf', 'I');
?>
