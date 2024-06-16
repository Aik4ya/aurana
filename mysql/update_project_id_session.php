<?php
session_start();
if (isset($_GET['projectID'])) {
    $_SESSION['projectID'] = $_GET['projectID'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
