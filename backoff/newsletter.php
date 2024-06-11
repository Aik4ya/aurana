<!DOCTYPE html>
<html>
<head>
    <title>Newsletter Backoffice</title>
</head>
<body>
    <h1>Newsletter Backoffice</h1>

    <form method="POST" action="../mysql/newletters.php">
        <label for="subject">Subject:</label><br>
        <input type="text" id="subject" name="subject" required><br><br>

        <label for="message">Message:</label><br>
        <textarea id="message" name="message" required></textarea><br><br>

        <input type="submit" value="Send Newsletter">
    </form>
</body>
</html>