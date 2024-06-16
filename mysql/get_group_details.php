<?php
require_once 'connexion_bdd.php';

if (isset($_GET['group_id'])) {
    $groupID = $_GET['group_id'];
    $conn = connexion_bdd();

    $sql = "SELECT Groupe_ID, Nom, Description_Groupe, Code FROM GROUPE WHERE Groupe_ID = :groupID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
    $stmt->execute();
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'group' => $group]);
} else {
    echo json_encode(['success' => false, 'error' => 'group_id missing']);
}
