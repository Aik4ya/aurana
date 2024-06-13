<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Backoffice Newsletter</title>
</head>
<body>
    <h1>Backoffice Newsletter</h1>
    <form method="POST">
        <label for="subject">Sujet :</label><br>
        <input type="text" id="subject" name="subject" required><br><br>
        <label for="message">Message :</label><br>
        <textarea id="message" name="message" required></textarea><br><br>
        <input type="submit" name="preview" value="Prévisualiser">
        <input type="submit" name="send" value="Envoyer la Newsletter">
    </form>
</body>
</html>