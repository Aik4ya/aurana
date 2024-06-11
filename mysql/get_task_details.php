<?php
require_once '../mysql/connexion_bdd.php';

$conn = connexion_bdd();

if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    // Sélectionnez uniquement les colonnes Texte, Categorie et Date_Tache
    $sql = "SELECT Texte AS nom, Categorie, Date_Tache FROM TACHE WHERE Tache_ID = :task_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':task_id', $task_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Renvoie les données en JSON
        echo json_encode([
            'nom' => $row['nom'], // Notez l'utilisation de 'nom' au lieu de 'Texte'
            'categorie' => $row['Categorie'],
            'dateTache' => $row['Date_Tache'],
        ]);
    } else {
        echo json_encode(['error' => 'Task not found']);
    }
} else {
    echo json_encode(['error' => 'No task_id provided']);
}
?>
