<?php
require '../mysql/cookies_uid.php';

ecriture_log('main_chat');
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
    <link rel="stylesheet" href="../css/main_chat.css">
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
                <!-- user end -->
            </div>
            <!-- top end -->
            <!-- main start -->
            <main>
                <!-- projectCard start -->
                <div class="projectCard">
                    <!-- projectTop start -->
                    <div class="projectTop">
                        <h2>Pseudo<br><span>Groupe</span></h2>*
                    </div>
                    <!-- projectTop end -->
                    <div class="chat_messages">
                        <?php
                        // Afficher les messages existants
                        /*


                        $stmt_messages = $conn->query("SELECT * FROM MESSAGE");
                        while ($row_message = $stmt_messages->fetch(PDO::FETCH_ASSOC)) {
                            $message_id = $row_message['Message_ID'];
                            $message_text = $row_message['Texte'];
                            $auteur_id = $row_message['Auteur_ID'];
                            $destinataire_id = $row_message['Destinataire_ID'];

                            // Vérifier si l'utilisateur a le droit de supprimer le message
                            $delete_permission = ($_SESSION['Droit'] == 0 && $auteur_id == $_SESSION['Utilisateur_ID']);

                            // Afficher le message avec l'option de suppression si autorisé
                            echo "<div class='message'>";
                            echo "<p>$message_text</p>";
                            if ($delete_permission) {
                                echo "<span class='delete'>Demander à l'admin de le supprimer</span>";
                            }
                            echo "</div>";
                        }


                        */
                        ?>
                        <!-- Formulaire pour ajouter un nouveau message avec AJAX -->
                        <form id="newMessageForm">
                            <textarea id="newMessageInput" name="nouveau_message" placeholder="Ajouter un nouveau message"></textarea>
                            <button type="submit">Envoyer</button>
                        </form>
                    </div>
                </div>
                <!-- projectCard end -->
                <!-- myTasks start -->
                <div class="myTasks">
                    <!-- tasksHead start -->
                    <div class="tasksHead">
                        <h2>Messages</h2>
                        <div class="tasksDots">
                            <span class="material-symbols-outlined">
                                more_horiz
                            </span>
                        </div>
                    </div>
                    <!-- tasksHead end -->
                    <!-- tasks start -->
                    <div class="tasks">
                        <ul>
                            <li>
                                <span class="tasksIconName">
                                    <span class="tasksName">
                                        Pseudo
                                    </span>
                                </span>
                            </li>
                            <li>
                                <span class="tasksIconName">
                                    <span class="tasksName">
                                        Pseudo 2
                                    </span>
                                </span>

                            </li>
                            <li>
                                <span class="tasksIconName">
                                    <span class="tasksName">
                                        Pseudo 3
                                    </span>
                                </span>
                            </li>
                            <li>
                                <span class="tasksIconName">
                                    <span class="tasksName">
                                        <underline>Pseudo 4</underline>
                                    </span>
                                </span>
                            </li>
                            <li>
                                <span class="tasksIconName">
                                    <span class="tasksName">
                                        Pseudo 5
                                    </span>
                                </span>
                                <span class="tasksStar full">
                                    <span class="material-symbols-outlined">
                                        star
                                    </span>
                                </span>
                            </li>
                            <li>
                                <span class="tasksIconName">
                                    <span class="tasksName">
                                        Pseudo 6
                                    </span>
                                </span>
                                <span class="tasksStar full">
                                    <span class="material-symbols-outlined">
                                        star
                                    </span>
                                </span>
                            </li>
                            <li>
                                <span class="tasksIconName">
                                    <span class="tasksName">
                                        Pseudo 7
                                    </span>
                                </span>

                            </li>
                        </ul>
                    </div>
                    <!-- tasks ens -->
                </div>
                <!-- myTasks end -->
            </main>
            <script src="../js/main_chat.js"></script>
</body>

</html>