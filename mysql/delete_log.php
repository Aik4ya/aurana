<?php
$logDirectory = '../mysql/log/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['filename'])) {
        $filename = basename($data['filename']);
        $filePath = $logDirectory. $filename;

        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                echo json_encode(array('success' => true, 'message' => 'Fichier de log supprimé avec succès'));
                exit();
            } else {
                echo json_encode(array('success' => false, 'message' => 'Erreur lors de la suppression du fichier de log'));
                exit();
            }
        } else {
            echo json_encode(array('success' => false, 'message' => 'Le fichier de log spécifié n\'existe pas'));
            exit();
        }
    } else {
        echo json_encode(array('success' => false, 'message' => 'Nom de fichier de log non spécifié dans la requête'));
        exit();
    }
} else {
    http_response_code(405);
    echo json_encode(array('success' => false, 'message' => 'Méthode HTTP non autorisée'));
    exit();
}
?>
