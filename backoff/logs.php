<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$logDirectory = '../mysql/log/';
$logs = scandir($logDirectory);

$logFiles = [];
$highlightedLogs = [];

foreach ($logs as $logFile) {
    if ($logFile === '.' || $logFile === '..') {
        continue;
    }

    $logFilePath = $logDirectory. $logFile;

    if (is_file($logFilePath)) {
        $logContent = file_get_contents($logFilePath);
        $logDate = date('Y-m-d H:i:s', filemtime($logFilePath));

        $highlighted = isset($_GET['highlight']) && $_GET['highlight'] == $logFile? 'highlighted' : '';

        $logFiles[] = [
            'name' => $logFile,
            'content' => $logContent,
            'date' => $logDate,
            'highlighted' => $highlighted
        ];
    }
}


echo "<table>";
echo "<thead><tr><th>Nom du Fichier</th><th>Date de Modification</th><th>Action</th></tr></thead>";
echo "<tbody>";
foreach ($logFiles as $log) {
    echo "<tr>";
    echo "<td>{$log['name']}</td>";
    echo "<td>{$log['date']}</td>";
    echo "<td>";
    echo "<button onclick='showLog(\"{$log['name']}\")'>Voir plus</button>";
    echo "<button onclick='deleteLog(\"{$log['name']}\")'>Supprimer</button>";
    echo "<button onclick='highlightLog(\"{$log['name']}\")'>Mettre en avant</button>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
?>

<div id="logModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLogModal()">&times;</span>
        <h2>Informations du log</h2>
        <pre id="logContent"></pre>
    </div>
</div>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    font-size: 16px;
    line-height: 1.5;
    color: #333;
    background-color: #f9f9f9;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    border: 1px solid #ddd;
}

th, td {
    padding: 12px 18px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f2f2f2;
    font-weight: bold;
}

.highlighted {
    background-color: #ffc107;
    font-weight: bold;
}


tr:hover {
    background-color: #f5f5f5;
}

button {
    background-color: #4c59af;
    color: #fff;
    border: none;
    padding: 12px 24px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #93a1ff;
}

button:active {
    background-color: #2e6c31;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
}

.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    overflow: auto;
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.error {
    color: #red;
    font-size: 14px;
    margin-bottom: 10px;
}

@media screen and (max-width: 600px) {
    table {
        font-size: 14px;
    }

    th, td {
        padding: 8px 12px;
    }
}
</style>

<script>

function highlightLog(logName) {
    if (confirm("Voulez-vous vraiment mettre en avant ce fichier de log?")) {
        window.location.href = '../mysql/logs.php?highlight=' + encodeURIComponent(logName);
    }
}


    function showLog(logName) {
        const log = <?php echo json_encode($logFiles); ?>;
        const logContent = log.find(log => log.name === logName).content;
        document.getElementById('logContent').innerText = logContent;
        document.getElementById('logModal').style.display = 'block';
    }

    function deleteLog(logName) {
    if (confirm("Voulez-vous vraiment supprimer ce fichier de log?")) {
        fetch('../mysql/delete_log.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ filename: logName, action: 'delete' }),
        })
      .then(response => response.json())
      .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                throw new Error(data.message);
            }
        })
      .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression du fichier de log');
        });
    }
}



    function closeLogModal() {
        document.getElementById('logModal').style.display = 'none';
    }
</script>
