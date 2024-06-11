<?php
// get_due_dates.php
require_once 'connexion_bdd.php';

session_start();
$user_id = $_SESSION['Utilisateur_ID'];
$groupe_id = $_SESSION['Groupe_ID'];

$conn = connexion_bdd();

$sql = "
    SELECT Tache_ID, Due_Date 
    FROM TACHE 
    WHERE Groupe_ID = :groupe_id AND Tache_ID IN (
        SELECT Tache_ID 
        FROM es_assigner 
        WHERE Utilisateur_ID = :user_id
    ) AND Due_Date IS NOT NULL";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':groupe_id', $groupe_id);
$stmt->execute();

$due_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($due_dates);
?>
