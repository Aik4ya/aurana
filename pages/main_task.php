<?php

require_once '../mysql/cookies_uid.php';

ecriture_log('main_task');
verif_session();
$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurana - Dashboard</title>
    <link rel="stylesheet" href="../css/main_task.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>
    <!-- début du conteneur -->
    <div class="container">
        <!-- début de gauche -->
        <div class="left">
            <!-- header start -->
            <header>
                <!-- logo start -->
                <div class="logo">
                    <h2>aurana</h2>
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
                            <a href="main.php">
                                <span class="material-symbols-outlined full">
                                    dashboard
                                </span>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="main_task.php">
                                <span class="material-symbols-outlined">
                                    check_box
                                </span>
                                <span class="title">Tâches</span>
                            </a>
                        </li>
                        <li>
                            <a href="main_chat.php">
                                <span class="material-symbols-outlined">
                                    chat_bubble
                                </span>
                                <span class="title">Messages</span>
                            </a>
                        </li>
                        <li>
                            <a href="main_files.php">
                                <span class="material-symbols-outlined">
                                    account_balance_wallet
                                </span>
                                <span class="title">Fichiers</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- nav end -->
            </header>
            <!-- header end -->
        </div>
        <!-- fin de gauche -->
        <!-- début de droite -->
        <div class="right">
            <!-- début du haut -->
            <div class="top">
                <!-- début de l'utilisateur -->
                <div class="user">
                    <?php
                        session_start();
                        echo "<h2>" . $_SESSION['Pseudo'] . "<br>";
                        echo "<span>" . $_SESSION['Droit'] . "</span></h2>";
                    ?>
                    <div class="arrow">
                        <span class="material-symbols-outlined">
                            expand_more
                        </span>
                    </div>
                    <div class="toggle">
                        <span class="material-symbols-outlined">
                            menu
                        </span>
                        <span class="material-symbols-outlined">
                            close
                        </span>
                    </div>
                </div>
                <!-- fin de l'utilisateur -->
            </div>
            <main>
                <!-- projectCard start -->
                <div class="projectCard">
                    <!-- projectTop start -->
                    <div class="projectTop">
                        <h2>Aurana<br><span>Groupe 3</span></h2>
                        <div class="projectDots">
                            <span class="material-symbols-outlined">
                                more_horiz
                            </span>
                        </div>
                    </div>
                    <!-- projectTop end -->
                    <!-- projectProgress start -->
                    <div class="projectProgress">
                        <div class="process">
                            <h2>En cours de validation</h2>
                        </div>
                        <div class="priority">
                            <h2>Haute priorité</h2>
                        </div>
                    </div>
                    <!-- projectProgress end -->
                    <!-- task start -->
                    <div class="task">
                        <h2>Réussi à: <bold>35</bold> / 50</h2>
                        <span class="line"></span>
                    </div>
                    <!-- task end -->
                    <!-- due start -->
                    <div class="due">
                        <h2>Date limite: 13/09</h2>
                    </div>
                    <!-- due end -->
                </div>
                <!-- projectCard end -->
                <!-- projectCard2 start -->
                <div class="projectCard projectCard2">
                    <div class="projectTop">
                        <h2>Nom du projet<br><span>Nom du Groupe</span></h2>
                        <div class="projectDots">
                            <span class="material-symbols-outlined">
                                more_horiz
                            </span>
                        </div>
                    </div>
                    <div class="projectProgress">
                        <div class="process">
                            <h2>En cours</h2>
                        </div>
                        <div class="priority">
                            <h2>Haute priorité</h2>
                        </div>
                    </div>
                    <div class="task">
                        <h2>Réussi à: <bold>35</bold> / 50</h2>
                        <span class="line"></span>
                    </div>
                    <div class="due">
                        <h2>Date limite: 06/07</h2>
                    </div>
                </div>
                <!-- projectCard2 end -->
            </main>
        </div>
        <!-- fin de droite -->
    </div>
</body>