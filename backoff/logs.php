

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurana - BackOffice</title>
    <link rel="stylesheet" href="../css/main_profile.css">
    <link rel="stylesheet" href="../css/button.css">
    <link rel="stylesheet" href="../css/base_main.css">
    <link rel="stylesheet" href="../css/backoff_logs.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>
    <!-- container start -->
    <div class="container">
        <div id="logModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeLogModal()">&times;</span>
                <h2>Informations du log</h2>
                <pre id="logContent"></pre>
            </div>
        </div>
        <div class="left">
            <!-- header start -->
            <header>
                <!-- logo start -->
                <div class="logo">
                    <a href="b_off.php"><h2>aurana</h2></a>
                    <div class="close">
                        <span class="material-symbols-outlined">
                            close
                        </span>
                    </div>
                </div>
                <!-- nav start -->
                <nav>
                    <ul>
                        <li>
                            <a href="utilisateurs.php">
                                <span class="material-symbols-outlined full">
                                    group
                                </span>
                                <span class="title">Gestions des Utilisateurs</span>
                            </a>
                        </li>
                        <li>
                            <a href="ticket.php">
                                <span class="material-symbols-outlined full">
                                    stack
                                </span>
                                <span class="title">Tickets</span>
                            </a>
                        </li>
                        <li>
                            <a href="qr_cptcha.php">
                                <span class="material-symbols-outlined full">
                                    help
                                </span>
                                <span class="title">Captcha</span>
                            </a>
                        </li>
                        <li>
                            <a href="logs.php">
                                <span class="material-symbols-outlined full">
                                    wysiwyg
                                </span>
                                <span class="title">Logs</span>
                            </a>
                        </li>
                        <li>
                            <a href="newsletter.php">
                                <span class="material-symbols-outlined full">
                                    mail
                                </span>
                                <span class="title">Newsletter</span>
                            </a>
                    </ul>
                </nav>
                <!-- nav end -->
            </header>
            <!-- header end -->
        </div>
        <div class="right">
            <!-- top start -->
            <div class="top">
                <!-- user start -->
                <div class="user">
                <?php
                    session_start();
                    echo "<h2>" . $_SESSION['Pseudo'] . "<br>";
                    echo "<span>" . ($_SESSION['Droit'] == 1 ? "Administrateur" : "Utilisateur") . "</span></h2>";
                ?>
                </div>
            </div>
            <main>
                <div class="logsBox">
                <?php
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
                </div>
            </main>
        </div>
    </div>
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
</body>
</html>