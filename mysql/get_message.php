<?php
session_start();
require_once 'connexion_bdd.php';

$conn = connexion_bdd();
$sql_messages = "SELECT * FROM MESSAGE WHERE Destinataire_ID = :groupe_id";
$stmt_messages = $conn->prepare($sql_messages);
$stmt_messages->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
$stmt_messages->execute();
$result = $stmt_messages->get_result();

$messages = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

echo json_encode($messages);