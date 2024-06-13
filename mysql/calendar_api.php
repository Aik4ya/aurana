<?php
session_start();
require '../mysql/connexion_bdd.php'; // This should include your PDO connection setup.

if (!isset($_SESSION['Utilisateur_ID']) || !$_SESSION['expiration'] > time()) {
    // User is not logged in or session has expired.
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

// Function to fetch calendar data.
function getCalendarData($userId, $month, $year) {
    global $pdo;
    $response = [];

    // Fetch tasks
    $tasksStmt = $pdo->prepare("
        SELECT t.Tache_ID, t.Texte, t.Date_Tache 
        FROM TACHE t
        JOIN es_assigner ea ON t.Tache_ID = ea.Tache_ID
        WHERE ea.Utilisateur_ID = :userId AND MONTH(t.Date_Tache) = :month AND YEAR(t.Date_Tache) = :year
    ");
    $tasksStmt->execute(['userId' => $userId, 'month' => $month, 'year' => $year]);
    $response['tasks'] = $tasksStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch projects
    $projectsStmt = $pdo->prepare("
        SELECT p.ID, p.nom, p.deadline
        FROM PROJET p
        JOIN est_membre_projet emp ON p.ID = emp.Projet_ID
        WHERE emp.Utilsateur_ID = :userId AND MONTH(p.deadline) = :month AND YEAR(p.deadline) = :year
    ");
    $projectsStmt->execute(['userId' => $userId, 'month' => $month, 'year' => $year]);
    $response['projects'] = $projectsStmt->fetchAll(PDO::FETCH_ASSOC);

    return $response;
}

// Handle API request.
if (isset($_GET['month']) && isset($_GET['year'])) {
    $data = getCalendarData($_SESSION['Utilisateur_ID'], $_GET['month'], $_GET['year']);
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Invalid parameters']);
}
?>
