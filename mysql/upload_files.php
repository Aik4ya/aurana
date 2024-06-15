<?php

require_once '../mysql/connexion_bdd.php';
session_start();

$maxFileSize = 100 * 1024 * 1024; // 100MB

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileSize = $file['size'];
    $fileName = basename($file['name']);
    $filePath = '../uploads/' . $fileName;

    if ($fileSize > $maxFileSize) {
        echo json_encode(['message' => 'File size exceeds 100MB.', 'success' => false]);
        exit;
    }

    // Ensure the uploads directory exists
    if (!is_dir('../uploads')) {
        mkdir('../uploads', 0777, true);
    }

    // Move the file to the uploads directory
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Scan the file using VirusTotal API
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
            // Save the file info to the database
            $conn = connexion_bdd();
            $sql = "INSERT INTO FICHIER (Adresse, Date_Stock, Groupe_ID, fichier_type, fichier_size, Utilisateur_id) VALUES (:adresse, NOW(), :groupe_id, :fichier_type, :fichier_size, :utilisateur_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':adresse', $fileName); // Store only the file name
            $stmt->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
            $stmt->bindParam(':fichier_type', $file['type']);
            $stmt->bindParam(':fichier_size', $fileSize);
            $stmt->bindParam(':utilisateur_id', $_SESSION['Utilisateur_ID']);
            $stmt->execute();

            echo json_encode(['message' => 'File uploaded and scanned successfully.', 'success' => true]);
        } else {
            echo json_encode(['message' => 'Failed to scan file.', 'success' => false]);
        }
    } else {
        echo json_encode(['message' => 'Failed to upload file.', 'success' => false]);
    }
} else {
    echo json_encode(['message' => 'No file uploaded.', 'success' => false]);
}
?>
