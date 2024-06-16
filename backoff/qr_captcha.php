<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CAPTCHA</title>
    <link rel="stylesheet" type="text/css" href="../css/backoff_captcha.css">
</head>
<body>
<form id="captcha-form">
    <input type="text" id="new-question" placeholder="Enter question...">
    <input type="text" id="new-reponse" placeholder="Enter response...">
    <button type="button" id="btn-create-captcha">Créer un nouveau CAPTCHA</button>
</form>
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once '../mysql/connexion_bdd.php';

function affichage_captcha($dbh)
{
    try {
        $sql = $dbh->prepare('SELECT id, question, reponse, actif FROM CAPTCHA');
        $sql->execute();
        $result = $sql->fetchAll();

        if ($result) {
            echo "<table border='1'>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Question</th>";
            echo "<th>Reponse</th>";
            echo "<th>Actif</th>";
            echo "</tr>";
            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>". $row["id"]. "</td>";
                echo "<td id='question-". $row["id"]. "'>". $row["question"]. "</td>";
                echo "<td id='reponse-". $row["id"]. "'>". $row["reponse"]. "</td>";
                echo "<td>". $row["actif"]. "</td>";
                echo "<td><button onclick='modification_captcha(". $row["id"]. ")'>Modifier</button></td>";
                echo "<td><button onclick='supression_captcha(". $row["id"]. ")'>Supprimer</button></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (PDOException $e) {
        echo "Erreur : ". $e->getMessage();
    }
}

affichage_captcha(connexion_bdd());
?>

<script>
        document.getElementById('btn-create-captcha').addEventListener('click', function() {
            creation_captcha();
        });

        function modification_captcha(id) {
            const question = document.getElementById('question-' + id).innerText;
            const reponse = document.getElementById('reponse-' + id).innerText;

            const newQuestion = prompt("Enter une nouvelle question:", question);
            const newReponse = prompt("Enter une nouvelle response:", reponse);
            const newActif = confirm("Voulez vous l'activer ?")? 1 : 0;

            if (newQuestion && newReponse) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../mysql/update_captacha.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send("action=modify&id=" + id + "&question=" + encodeURIComponent(newQuestion) + "&reponse=" + encodeURIComponent(newReponse) + "&actif=" + newActif);
            }
        }

        function supression_captcha(id) {
            if (confirm("Etes vous sur de supprimer le captcha ?")) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../mysql/update_captacha.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send("action=delete&id=" + id);
            }
        }

        function creation_captcha() {
            const question = document.getElementById('new-question').value;
            const reponse = document.getElementById('new-reponse').value;

            if (question && reponse) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../mysql/update_captcha.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send("action=create&question=" + encodeURIComponent(question) + "&reponse=" + encodeURIComponent(reponse));
            }
        }
    </script>
</body>
</html>
