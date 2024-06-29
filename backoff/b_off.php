<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

	require_once '../mysql/cookies_uid.php';
	
  session_start();
	ecriture_log('BackOffice');
	verif_session();
	$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

	if ($_SESSION['Droit'] == 0) {
        header('Location: ../pages/403.html');
        exit();
    }
?>

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
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>
    <!-- container start -->
    <div class="container">
        <!-- left start -->
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
        <!-- left end -->
        <!-- right start -->
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
                <div class="">
                </div>
            </main>
        </div>
    </div>
</body>
</html>

