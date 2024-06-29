<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('connexion_bdd.php');
require_once('../vendor/TCPDF/tcpdf.php');

session_start();

$bdd = connexion_bdd();

function recupererUtilisateurs(){
    global $bdd;
    $requete = $bdd->prepare('SELECT * FROM UTILISATEUR WHERE Utilisateur_ID = :id');
    $requete->bindParam(':id', $_SESSION['Utilisateur_ID']);
    $requete->execute();
    return $requete->fetchAll();
}


$pdf = new TCPDF();

$pdf->AddPage();

$pdf->SetFont('dejavusans', '', 12);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$utilisateurs = recupererUtilisateurs();


foreach ($utilisateurs as $utilisateur) {
    $pdf->Cell(0, 10, 'Nom: ' . $utilisateur['Pseudo'], 0, 1);
    $pdf->Cell(0, 10, 'Email: ' . $utilisateur['Email'], 0, 1);
    $pdf->Ln();
}

$pdf->Output('utilisateurs.pdf', 'I'); // Affiche le PDF dans le navigateur