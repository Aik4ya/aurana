<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../mysql/cookies_uid.php';
require '../../mysql/connexion_bdd.php';


verif_session();
$conn = connexion_bdd();


$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $sql="INSERT INTO messages (user_id, message, date) VALUES (:user_id, :message, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
    }
}


$sql = "SELECT m.message, m.date, u.username FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.date ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
    <style>
        /* CSS pour chat */
    </style>
</head>
<body>
    <div id="chat-container">
        <div id="messages">
            <?php foreach ($messages as $message): ?>
                <div class="message">
                    <span class="username"><?php echo $message['username']; ?></span>
                    <span class="date"><?php echo $message['date']; ?></span>
                    <p><?php echo $message['message']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <form id="send-message-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="text" name="message" placeholder="Entrez votre message" required>
            <button type="submit">Envoyer</button>
        </form>
    </div>
    <script>
        // Code JavaScript pour actualiser automatiquement les messages (avec l'approche Polling)
        setInterval(function() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('messages').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }, 3000); // Actualiser toutes les 3 secondes
    </script>
</body>
</html>