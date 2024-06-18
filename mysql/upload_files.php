<?php

require_once '../mysql/connexion_bdd.php';
session_start();

// Définir la taille maximale du fichier à 100MB
$maxFileSize = 100 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileSize = $file['size'];
    $fileName = basename($file['name']);
    $filePath = '../uploads/' . $fileName;

    // Vérifier la taille du fichier
    if ($fileSize > $maxFileSize) {
        echo json_encode(['message' => 'La taille du fichier dépasse la limite de 100MB.', 'success' => false]);
        exit;
    }

    // Vérifier l'existence du répertoire uploads et le créer si nécessaire
    if (!is_dir('../uploads')) {
        mkdir('../uploads', 0777, true);
    }

    // Déplacer le fichier vers le répertoire uploads
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Scanner le fichier avec l'API VirusTotal
        $apiKey = '6bf815eb330e6e3193f9ccbdb9ba65d691c09bc4c0b81df61e5978f459970512';
        $url = 'https://www.virustotal.com/vtapi/v2/file/scan';

        $post = [
            'apikey' => $apiKey,
            'file' => new CURLFile($filePath),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['scan_id'])) {
            // Sauvegarder les informations du fichier dans la base de données
            $conn = connexion_bdd();
            $sql = "INSERT INTO FICHIER (Adresse, Date_Stock, Groupe_ID, fichier_type, fichier_size, Utilisateur_id) VALUES (:adresse, NOW(), :groupe_id, :fichier_type, :fichier_size, :utilisateur_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':adresse', $fileName, PDO::PARAM_STR);
            $stmt->bindParam(':groupe_id', $_SESSION['Groupe_ID'], PDO::PARAM_INT);
            $stmt->bindParam(':fichier_type', $file['type'], PDO::PARAM_STR);
            $stmt->bindParam(':fichier_size', $fileSize, PDO::PARAM_INT);
            $stmt->bindParam(':utilisateur_id', $_SESSION['Utilisateur_ID'], PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['message' => 'Fichier téléversé et scanné avec succès.', 'success' => true]);
        } else {
            echo json_encode(['message' => 'Échec du scan du fichier.', 'success' => false]);
        }
    } else {
        echo json_encode(['message' => 'Échec du téléversement du fichier.', 'success' => false]);
    }
} else {
    echo json_encode(['message' => 'Aucun fichier téléversé.', 'success' => false]);
}
?>
