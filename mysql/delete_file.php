<?php

require_once '../mysql/connexion_bdd.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['file_id'])) {
        $fileId = $input['file_id'];

        $conn = connexion_bdd();

        //vérif si fichier existe
        $sql = "SELECT Adresse FROM FICHIER WHERE Fichier_ID = :file_id AND Groupe_ID = :groupe_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->bindParam(':groupe_id', $_SESSION['Groupe_ID'], PDO::PARAM_INT);
        $stmt->execute();
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            $filePath = '../uploads/' . $file['Adresse'];

            //si condition ok supprimer
            $sql = "DELETE FROM FICHIER WHERE Fichier_ID = :file_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
            $stmt->execute();

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            echo json_encode(['message' => 'Fichier supprimé avec succès.', 'success' => true]);
        } else {
            echo json_encode(['message' => 'Fichier non trouvé ou non autorisé.', 'success' => false]);
        }
    } else {
        echo json_encode(['message' => 'ID du fichier manquant.', 'success' => false]);
    }
} else {
    echo json_encode(['message' => 'Requête non autorisée.', 'success' => false]);
}
?>
