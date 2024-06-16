<!DOCTYPE html>
<html>
<head>
    <title>Backoffice de la Newsletter</title>
</head>
<body>
    <h1>Backoffice de la Newsletter</h1>

    <form method="POST" action="../mysql/newletters.php">
        <label for="subject">Sujet :</label><br>
        <input type="text" id="subject" name="subject" required><br><br>

        <label for="message">Message :</label><br>
        <textarea id="message" name="message" required></textarea><br><br>

        <input type="submit" value="Envoyer la Newsletter">
    </form>
</body>
</html>