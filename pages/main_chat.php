<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


require '../mysql/cookies_uid.php';
require '../mysql/connexion_bdd.php';
$conn = connexion_bdd();
session_start();

$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

ecriture_log("main_chat");
verif_session();

if (isset($_GET['groupe'])) {
    $groupe = $_GET['groupe'];
    $sql = "SELECT Groupe_ID, Nom FROM GROUPE WHERE Nom = :groupe";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':groupe', $groupe);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $_SESSION['Groupe_ID'] = $row['Groupe_ID'];
        $nom_groupe = $row['Nom'];
    }
} else {
    $_GET['groupe'] = "none";
    header("Location: main_chat.php?groupe=none");
    exit;
}

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
    <div class="container">
            <div class="left">
                <header>
                    <div class="logo">
                        <h2>aurana</h2>
                        <div class="close">
                            <span class="material-symbols-outlined">
                                close
                            </span>
                        </div>
                    </div>
                    <nav>
                        <ul>
                            <li>
                            <?php echo "<a href='main.php?groupe=" . $_GET['groupe'] . "'>" ?>
                                    <span class="material-symbols-outlined full">
                                        dashboard
                                    </span>
                                    <span class="title">Dashboard</span>
                                </a>
                            </li>
                            <li>
                            <?php echo "<a href='main_task.php?groupe=" . $_GET['groupe'] . "'>" ?>
                                    <span class="material-symbols-outlined">
                                        check_box
                                    </span>
                                    <span class="title">Tâches</span>
                                </a>
                            </li>
                            <li>
                                <?php echo "<a href='main_chat.php?groupe=" . $_GET['groupe'] . "'>" ?>
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
                </header>
                    <div class="decoBtn">
                        <form action="logout.php">
                            <button id='deconnexion'>Deconnexion</button>
                        </form>
                    </div>
            </div>
            <div class="right">
                <div class="top">
                    <div class="searchBx">
                        <?php if ($nom_groupe !== null): ?>
                        <h2><?php echo htmlspecialchars($nom_groupe); ?></h2>
                        <?php endif; ?>
                    </div>
                    
                    <div class="user">
                        <?php

                        // affichage groupes + menu déroulant

                        session_start();
                        
                        echo "<h2>" . $_SESSION['Pseudo'] . "<br>";

                        if ($_SESSION['Droit'] == 0) {
                            echo "<span>User</span></h2>";
                        } elseif ($_SESSION['Droit'] == 1) {
                            echo "<span>Admin</span></h2>";
                        }

                        $sql = "SELECT GROUPE.Nom FROM est_membre INNER JOIN GROUPE ON est_membre.GROUPE = GROUPE.Groupe_ID WHERE est_membre.Utilisateur_ID = {$_SESSION['Utilisateur_ID']}";
                        $result = $conn->query($sql);

                        if ($result->rowCount() > 0) {
                            echo "<p>Groupe(s): ";
                            $groupes = [];
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $groupes[] = $row["Nom"];
                            }
                            echo implode("; ", $groupes);
                            echo "</p>";
                        } else {
                            echo "<p>Aucun Groupe</p>";
                        }
                        ?>
                        <div class="arrow" onclick="toggleMenu()">
                            <span class="material-symbols-outlined">
                                expand_more
                            </span>
                        </div>
                        <div class="menu" style="display: none;">
                            <ul id="menuList">

                            <?php 
                                    $sql="SELECT GROUPE.Nom FROM est_membre INNER JOIN GROUPE ON est_membre.GROUPE = GROUPE.Groupe_ID WHERE est_membre.Utilisateur_ID = {$_SESSION['Utilisateur_ID']}";
                                    $result = $conn->query($sql);
                                    if ($result->rowCount() > 0) {
                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<li><a href='main_chat.php?groupe={$row['Nom']}'>{$row['Nom']}</a></li>";
                                        }
                                    }
                            ?>
                            <li><a href="#" id="openCreateGroupModal">Créer un groupe</a></li>
                            <li><a href="#" id="openJoinGroupModal">Rejoindre un groupe</a></li>
                            <li><a href="#" id="openManageGroupModalBtn" onclick="openManageGroupModal(<?php echo $_SESSION['Groupe_ID']; ?>, '<?php echo $nom_groupe; ?>', 'Description du groupe', 'Code du groupe')">Gérer le groupe</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <main>
                <!-- projectCard start -->
                <div class="projectCard">
                    <!-- projectTop start -->
                    <div class="projectTop">
                        <h2>Pseudo<br><span>Groupe</span></h2>
                    </div>
                    <!-- projectTop end -->
                    <div class="chat_messages">
                        <?php
                        // Afficher les messages existants
                        

                        // $conn = connexion_bdd();
                        // $sql_messages = "SELECT * FROM MESSAGE WHERE Destinataire_ID = :groupe_id";
                        // $stmt_messages = $conn->prepare($sql_messages);
                        // $stmt_messages->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
                        // $stmt_messages->execute();
                        

                        // while ($row_message = $stmt_messages->fetch(PDO::FETCH_ASSOC)) {
                        //     $message_id = $row_message['Message_ID'];
                        //     $message_text = $row_message['Texte'];
                        //     $auteur_id = $row_message['Auteur_ID'];
                        //     $destinataire_id = $row_message['Destinataire_ID'];

                        //     // Vérifier si l'Utilisateur a le droit de supprimer le message
                        //     $delete_permission = ($_SESSION['Droit'] == 0 && $auteur_id == $_SESSION['Utilisateur_ID']);

                        //     // Afficher le message avec l'option de suppression si autorisé
                        //     echo "<div class='message'>";
                        //     echo "<p>$message_text</p>";
                        //     if ($delete_permission) {
                        //         echo "<span class='delete'>Demander à l'admin de le supprimer</span>";
                        //     }
                        //     echo "</div>";
                        // }


                        
                        ?>
                        <!-- Formulaire pour ajouter un nouveau message avec AJAX -->
                        <form id="newMessageForm">
                            <textarea id="newMessageInput" name="nouveau_message" placeholder="Ajouter un nouveau message"></textarea>
                            <button type="submit">Envoyer</button>
                        </form>
                    </div>
                </div>
                <!-- projectCard end -->
                <!-- myfriends start -->
                <div class="myfriends">
                    <!-- friendsHead start -->
                    <div class="friendsHead">
                        <h2>Messages</h2>
                        <div class="friendsDots">
                            <span class="material-symbols-outlined">
                                more_horiz
                            </span>
                        </div>
                    </div>
                    <!-- friendsHead end -->
                    <!-- friends start -->
                    <div class="friends">
                        <ul>
                            <?php
                                $dbh = connexion_bdd();

                                $sql="SELECT UTILISATEUR.Pseudo 
                                FROM UTILISATEUR
                                JOIN est_membre ON UTILISATEUR.Utilisateur_ID = est_membre.Utilisateur_ID 
                                WHERE est_membre.GROUPE = '{$_SESSION['Groupe_ID']}';";

                                $result=$dbh->query($sql);

                                $html = "";
                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                    $userName = $row['Pseudo'];
                                    $html .= "
                                        <li>
                                            <span class=\"friendsIconName\">
                                                <span class=\"friendsName\">$userName</span>
                                            </span>
                                        </li>
                                    ";
                                }
                                echo $html;
                            ?>
                            <!-- <li>
                                <span class="friendsIconName">
                                    <span class="friendsName">
                                        Pseudo 2
                                    </span>
                                </span>

                            </li> -->
                        </ul>
                    </div>
                    <!-- friends ens -->
                </div>
                <!-- myfriends end -->
            </main>
            <script src="../js/main_chat.js"></script>
</body>

</html>