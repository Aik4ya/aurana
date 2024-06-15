<?php
include_once '../mysql/cookies_uid.php';
include_once '../mysql/connexion_bdd.php';


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();
$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

ecriture_log("main");
verif_session();
$conn = connexion_bdd();

$nom_groupe = null;

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
    header("Location: main.php?groupe=none");
    exit;
}

if (isset($_GET['groupe']) && !($_GET['groupe'] == "none")) {
    $utilisateur_id = $_SESSION['Utilisateur_ID'];
    $groupe_id = $_SESSION['Groupe_ID'];

    $sql = "SELECT GROUPE FROM est_membre WHERE Utilisateur_ID = :utilisateur_id AND GROUPE = :groupe_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->bindParam(':groupe_id', $groupe_id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        header("Location: main.php?groupe=none");
        exit;
    }
}

if ($_GET['groupe'] != "none"){
    $sql="SELECT droit FROM est_membre WHERE Utilisateur_ID = :utilisateur_id AND GROUPE = :groupe_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':utilisateur_id', $_SESSION['Utilisateur_ID']);
    $stmt->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
    $stmt->execute();
    if ($stmt->fetch(PDO::FETCH_ASSOC)['droit'] == 1){
        $_SESSION['Droit_groupe'] = 1;
    } else {
        $_SESSION['Droit_groupe'] = 0;
    }
} else {
    $_SESSION['Droit_groupe'] = 0;
}

// avancer / reculer dans le calendrier

if (isset($_GET['cal'])) {
    if ($_GET['cal'] == "'prev'") {
        $_SESSION['mois'] = $_SESSION['mois'] - 1;
        if ($_SESSION['mois'] == 0) {
            $_SESSION['mois'] = 12;
            $_SESSION['annee'] = $_SESSION['annee'] - 1;
        }

        header("Location: main.php?groupe={$_GET['groupe']}");

    } elseif ($_GET['cal'] == "'next'") {
        $_SESSION['mois'] = $_SESSION['mois'] + 1;
        if ($_SESSION['mois'] == 13) {
            $_SESSION['mois'] = 1;
            $_SESSION['annee'] = $_SESSION['annee'] + 1;
        }

        header("Location: main.php?groupe={$_GET['groupe']}");
    }

} elseif (!isset($_SESSION['mois']) || !isset($_SESSION['annee'])){
    $_SESSION['mois'] = date('m');
    $_SESSION['annee'] = date('Y');
}



$stmt_tasks_dates = $conn->prepare("SELECT Date_Tache FROM TACHE WHERE Tache_ID IN (SELECT Tache_ID FROM es_assigner WHERE Utilisateur_ID = :user_id) AND Groupe_ID = :groupe_id");
$stmt_tasks_dates->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
$stmt_tasks_dates->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
$stmt_tasks_dates->execute();
$task_dates = $stmt_tasks_dates->fetchAll(PDO::FETCH_COLUMN);

$stmt_projet_dates = $conn->prepare("SELECT deadline FROM PROJET JOIN est_membre_projet ON PROJET.ID = est_membre_projet.Projet_ID WHERE est_membre_projet.Utilisateur_ID = :user_id AND PROJET.id_groupe = :groupe_id");
$stmt_projet_dates->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
$stmt_projet_dates->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
$stmt_projet_dates->execute();
$projet_dates = $stmt_projet_dates->fetchAll(PDO::FETCH_COLUMN);

function fetchTasksWithProjects($dbh) {
    $userID = $_SESSION['Utilisateur_ID'];
    $groupeID = isset($_SESSION['Groupe_ID']) ? $_SESSION['Groupe_ID'] : 0;

    // Inclure les tâches sans projet (personnelles) et celles liées à un projet
    $sql = "SELECT t.Tache_ID, t.Texte, p.nom AS NomProjet
            FROM es_assigner ea
            JOIN TACHE t ON ea.Tache_ID = t.Tache_ID
            LEFT JOIN tache_assignee_projet tap ON t.Tache_ID = tap.id_tache
            LEFT JOIN PROJET p ON tap.id_projet = p.ID
            WHERE ea.Utilisateur_ID = :userID AND (t.Groupe_ID = :groupeID OR t.Groupe_ID IS NULL)
            ORDER BY p.nom ASC, t.Date_Tache DESC";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':groupeID', $groupeID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$tasks = fetchTasksWithProjects(connexion_bdd());

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurana - Dashboard</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/button.css">
    <link rel="stylesheet" href="../css/modals.css">
    <!-- <script type="text/javascript" src="../js/aurana.js"></script> -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<style>
.hidden {
    display: none;
}

.menu {
    position: absolute;
    top: 50px;
    right: 50px;
    background-color: #ffffff;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.4);
    z-index: 1;
    border-radius: 8px;
}

.menu ul {
    list-style-type: none;
    padding: 0;
}

.menu li {
    padding: 12px 16px;
}

.menu li a {
    text-decoration: none;
    color: black;
    display: block;
}

.menu li a:hover {
    background-color: #ddd;
}
.modal-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); 
    z-index: 999; 
    display: flex;
    justify-content: center;
    align-items: center;
}


.close-modal {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    font-size: 20px;
    color: #888;
}

.close-modal:hover {
    color: #555; 
}

.popup {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0);
    background-color: rgba(0,0,0.5);
}

.popup-content {
    background-color: white;
    padding: 20px;
    border: 1px solid #ccc;
    width: 80%;
    box-shadow: 10px;
    border-radius: 10px;
    border: 1px solid #000;
    border-radius: 10px;
    padding: 20px;
    margin: 20px;
}

.group-menu {
    position: absolute;
    right: 0;
    top: 100%;  
    background-color: white;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1000;
    width: 200px; 
    display: none;
}

.group-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.group-menu li a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: black;
}

.group-menu li a:hover {
    background-color: #f0f0f0;
}


</style>

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
                            <a href="#">
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
                <div class="disconnect">
                    <div class="decoBtn">
                        <form action="logout.php">
                            <button id='deconnexion'>Deconnexion</button>
                        </form>
                    </div>
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
                    $conn = connexion_bdd();
                    echo "<h2>" . $_SESSION['Pseudo'] . "<br>";

                    if ($_SESSION['Droit_groupe'] == 2) {
                        echo "<span>Administrateur du Groupe</span></h2>";
                    } elseif ($_SESSION['Droit_groupe'] == 1) {
                        echo "<span>Propriétaire du Groupe</span></h2>";
                    }
                    
                    if ($_SESSION['Droit'] == 1) {
                        echo "<span>Admin</span></h2>";
                    }
                    ?>
                    <div class="arrow" onclick="toggleMenu()">
                        <span class="material-symbols-outlined">
                            expand_more
                        </span>
                    </div>
                    <div class="menu" style="display: none;">
                        <ul id="menuList">
                            <li><a href="../pages/main_profile.php">Profil</a></li>

                        <?php
                                $sql="SELECT GROUPE.Nom FROM est_membre INNER JOIN GROUPE ON est_membre.GROUPE = GROUPE.Groupe_ID WHERE est_membre.Utilisateur_ID = {$_SESSION['Utilisateur_ID']}";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<li><a href='main.php?groupe={$row['Nom']}'>{$row['Nom']}</a></li>";
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
                <div class="projectCard">
                    <h2>Projets</h2>
                    <?php 
                        if ($_SESSION['Droit_groupe'] == 1){
                                echo "<button id=\"creaeProjetBtn\">Créer un projet</button>";
                            }
                    ?>
                    <br>
                    <ul>
                    <?php

                    // affichage projets

                        $sql = "SELECT ID, nom, status, priorite, deadline 
                            FROM PROJET 
                            WHERE id_groupe = :id_groupe 
                            AND ID IN (SELECT Projet_ID FROM est_membre_projet WHERE Utilisateur_ID = :id_utilisateur)
                            ORDER BY deadline ASC";
                        $stmt1 = $conn->prepare($sql);
                        $stmt1->bindParam(':id_groupe', $_SESSION['Groupe_ID']);
                        $stmt1->bindParam(':id_utilisateur', $_SESSION['Utilisateur_ID']);
                        $stmt1->execute();
                        $rowcount = $stmt1->rowCount();

                        if ($rowcount > 0) { // si projet
                            while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                                $id = $row['ID'];
                                $nom = $row['nom'];
                                $status = $row['status'];
                                $priorite = $row['priorite'];
                                $deadline = $row['deadline'];
                                $groupe = $_GET['groupe'];


                                $sql="SELECT count(*) FROM tache_assignee_projet INNER JOIN TACHE ON tache_assignee_projet.id_tache = TACHE.Tache_ID WHERE tache_assignee_projet.id_projet = :id_projet AND TACHE.done = 1"; //nombre detache fini
                                $stmt2 = $conn->prepare($sql);
                                $stmt2->bindParam(':id_projet', $id);
                                $stmt2->execute();
                                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                                $tachefin = $row['count(*)'];

                                $sql="SELECT count(*) FROM tache_assignee_projet WHERE id_projet = :id_projet"; //nombre de tache total
                                $stmt3 = $conn->prepare($sql);
                                $stmt3->bindParam(':id_projet', $id);
                                $stmt3->execute();
                                $row = $stmt3->fetch(PDO::FETCH_ASSOC);
                                $tachetotal = $row['count(*)'];

                                if ($tachefin == $tachetotal){ // si toutes les taches sont finies
                                    $css_status = "processFini";
                                    $css_line = "lineFini";
                                    $css_due = "dueFini";
                                } elseif (strtotime($deadline) <= strtotime('+7 days') || strtotime($deadline) < strtotime('today') ) { // si deadline dans moins de 7 jours ou déjà dépassé
                                    $css_status = "processRetard";
                                    $css_line = "lineRetard";
                                    $css_due = "dueRetard";
                                } else { // sinon taches en cours normales
                                    $css_status = "process";
                                    $css_line = "line";
                                    $css_due = "due";

                                }

                                switch ($priorite) {
                                    case "Basse":
                                        $css_priorite = "priorityBasse";
                                        break;
                                    case "Moyenne":
                                        $css_priorite = "priorityMoyenne";
                                        break;
                                    case "Haute":
                                        $css_priorite = "priorityHaute";
                                        break;
                                }

                                echo "<li>";
                                echo "<div class=\"projetBox\">";
                                echo "<div class=\"projectTop\">";
                                echo "<h2>$nom<br><span>$groupe</span></h2>";
                                echo "<div class=\"projectDots\">";
                                echo "<span class=\"material-symbols-outlined\">";
                                echo "more_horiz";
                                echo "</span>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class=\"projectProgress\">";
                                echo "<div class=$css_status>";
                                echo "<h2>$status</h2>";
                                echo "</div>";
                                echo "<div class=$css_priorite>";
                                echo "<h2>$priorite</h2>";
                                echo "</div>";
                                echo "</div>";

                                echo "<div class=\"task\">";
                                echo "<h2>Tâches faites: <strong>" . $tachefin . "</strong> / " . $tachetotal . "</h2>";
                                if ($tachetotal == 0) {
                                    echo "<span class=$css_line style=\"width: 0%;\"></span>"; // éviter division par 0
                                } else {
                                    echo "<span class=$css_line style=\"width: " . ($tachefin / $tachetotal) * 100 . "%;\"></span>";
                                }
                                echo "</div>";
                                echo "<div class=$css_due>";
                                echo "<h2>Du pour le : $deadline</h2>";
                                echo "</div>";
                                echo "</div>";
                                echo "<br>";
                            }

                        } else { // si pas de projet
                            echo "<li>";
                            echo "<div class=\"projectCard\">";
                            echo "<p> Aucun projet assigné</p>";
                            echo "</div>";
                            echo "</li>";
                        }





                    ?>
                    </ul>
                </div>

                <div class="myTasks">
                    <div class="tasksHead">
                        <h2>Mes tâches</h2>
                        <?php if ($_SESSION['groupe'] == "none"){
                            echo "<img src=\"../img/plus.png\" id=\"createTaskBtn\">";
                        }
                        ?>
                    </div>
                    <div class="tasks">
                    <ul>

                    <!-- affichage taches -->
                        <?php
                        if ($_SESSION['groupe']="none"){
                            $stmt_tasks = $conn->prepare("
                                SELECT TACHE.Tache_ID, TACHE.Texte
                                FROM es_assigner
                                INNER JOIN TACHE ON es_assigner.Tache_ID = TACHE.Tache_ID
                                WHERE es_assigner.Utilisateur_ID = :user_id AND TACHE.Groupe_ID = :groupe_id
                                ORDER BY TACHE.Date_Tache
                                LIMIT 7
                            ");
                            $stmt_tasks->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
                            $stmt_tasks->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
                            $stmt_tasks->execute();
                        } else {
                            $stmt_tasks = $conn->prepare("
                                SELECT TACHE.Tache_ID, TACHE.Texte
                                FROM es_assigner
                                INNER JOIN TACHE ON es_assigner.Tache_ID = TACHE.Tache_ID
                                WHERE es_assigner.Utilisateur_ID = :user_id AND TACHE.Date_Tache. <= :date_tache
                            ");
                            $stmt_tasks->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
                            $stmt_tasks->bindParam(':date_tache', (date('Y-m-d') + 10));
                            $stmt_tasks->execute();
                        }

                        while ($row_tasks = $stmt_tasks->fetch(PDO::FETCH_ASSOC)) {
                            $task_id = $row_tasks['Tache_ID'];
                            $task_text = $row_tasks['Texte'];

                            echo "<li>";
                            echo "<span class=\"tasksIconName\">";
                            echo "<span class=\"tasksIcon notDone\" onclick=\"toggleTaskCompletion(this)\">";
                            echo "<span class=\"material-symbols-outlined\"></span>";
                            echo "</span>";
                            echo "<span class=\"tasksName notDone\" id-task-id=\"$task_id\">" . htmlspecialchars($task_text) . "</span>";
                            echo "</span>";
                            echo "<span class=\"tasksStar half\" onclick=\"toggleStarCompletion(this)\">";
                            echo "<span class=\"material-symbols-outlined\">star</span>";
                            echo "</span>";
                            echo "</li>";
                        }
                        ?>
                    </ul>
                    </div>
                </div>

                <!-- debut calendrier -->

                <div class="calendar">
                    <div class="calendarHead">
                        <h2><?php switch ($_SESSION['mois']) {
                            case 1:
                                echo "Janvier {$_SESSION['annee']} ";
                                break;
                            case 2:
                                echo "Février {$_SESSION['annee']} ";
                                break;
                            case 3:
                                echo "Mars {$_SESSION['annee']} ";
                                break;
                            case 4:
                                echo "Avril {$_SESSION['annee']} ";
                                break;
                            case 5:
                                echo "Mai {$_SESSION['annee']} ";
                                break;
                            case 6:
                                echo "Juin {$_SESSION['annee']} ";
                                break;
                            case 7:
                                echo "Juillet {$_SESSION['annee']} ";
                                break;
                            case 8:
                                echo "Août {$_SESSION['annee']} ";
                                break;
                            case 9:
                                echo "Septembre {$_SESSION['annee']} ";
                                break;
                            case 10:
                                echo "Octobre {$_SESSION['annee']} ";
                                break;
                            case 11:
                                echo "Novembre {$_SESSION['annee']} ";
                                break;
                            case 12:
                                echo "Décembre {$_SESSION['annee']} ";
                                break;
                        }?></h2>
                        <div class="calendarIcon">
                            <a href="main.php?groupe=<?php echo $_GET['groupe']; ?>&cal='prev'" class="material-symbols-outlined">
                                chevron_left
                            </a>
                            <a href="main.php?groupe=<?php echo $_GET['groupe']; ?>&cal='next'" class="material-symbols-outlined">

                                chevron_right
                            </a>
                        </div>
                    </div>
                    <div class="calendarData">
                        <ul class="weeks">
                            <li>Lun</li>
                            <li>Mar</li>
                            <li>Mer</li>
                            <li>Jeu</li>
                            <li>Ven</li>
                            <li>Sam</li>
                            <li>Dim</li>
                        </ul>
                        <ul class="days">
                            <?php
                            $currentMonth = date('m');
                            $currentYear = date('Y');
                            $selectedMonth = $_SESSION['mois'];
                            $selectedYear = $_SESSION['annee'];

                            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
                            $firstDay = date('N', strtotime("$selectedYear-$selectedMonth-01"));
                            $lastDay = date('N', strtotime("$selectedYear-$selectedMonth-$daysInMonth"));

                            for ($i = 1; $i < $firstDay; $i++) {
                                echo "<li class='inactive'></li>";
                            }

                            for ($i = 1; $i <= $daysInMonth; $i++) {
                                $currentDate = sprintf("%04d-%02d-%02d", $selectedYear, $selectedMonth, $i);
                                $hasTasks = in_array($currentDate, $task_dates);
                                $hasProjet = in_array($currentDate, $projet_dates);

                                $dayClass = ''; // Initialize day class

                                if ($hasTasks) {
                                    $dayClass .= ' has-tasks'; // Append task class
                                }

                                if ($hasProjet) {
                                    $dayClass .= ' has-projet'; // Append project class
                                }

                                // Date comparison and CSS class assignment
                                if ($i == date('d') && $currentYear == $selectedYear) {
                                    if ($currentMonth == $selectedMonth) {
                                        echo "<li class='active$dayClass'>$i</li>";
                                    }
                                } elseif ($currentMonth > $selectedMonth || $currentYear > $selectedYear) {
                                    echo "<li class='inactive$dayClass'>$i</li>";
                                } elseif ($currentMonth == $selectedMonth && $i < date('d')) {
                                    echo "<li class='inactive$dayClass'>$i</li>";
                                } else {
                                    echo "<li class='$dayClass'>$i</li>";
                                }
                            }
                            ?>
                        </ul>

                    </div>
                </div>
                <!-- affichage messages -->
                <div class="messages">  
                    <div class="messagesHead">
                        <h2>Messages</h2>
                    </div>
                    <?php 
                        $sql = "SELECT * FROM MESSAGE WHERE Destinataire_ID = :groupe_id ORDER BY Date_Envoi DESC LIMIT 5";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
                        $stmt->execute();


                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $message_id = $row['Message_ID'];
                            $message_text = $row['Texte'];
                            $message_date = $row['Date_Envoi'];
                            $message_sender = $row['Auteur_ID'];

                            echo "<div class=\"messagesUser\">";
                            echo "<div class=\"messagesUserImg\">";
                            echo "<img src=\"./groupImg/img1.jpg\" alt=\"img1\">";
                            echo "</div>";
                            echo "<h2>$message_sender<br><span>$message_text</span></h2>";
                            echo "</div>";
                        }
                   ?>
                </div>
            </main>
        </div>
    </div>

<div id="ManageGroupModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeManageGroupModal">&times;</span>
        <h2>Gérer le groupe</h2>
        <form id="manageGroupForm">
            <label for="manageGroupName">Nom du groupe :</label><br>
            <input type="text" id="manageGroupName" name="groupName" readonly><br><br>
            <label for="manageGroupDescription">Description du groupe :</label><br>
            <textarea id="manageGroupDescription" name="groupDescription" rows="4" cols="50" readonly></textarea><br><br>
            <label for="manageGroupCode">Code du groupe :</label><br>
            <input type="text" id="manageGroupCode" name="groupCode" readonly><br><br>
            <button type="button" id="editGroupBtn">Éditer le groupe</button>
        </form>
    </div>
</div>

<!-- Modales -->

    <div id="TaskDetailModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeDetailModal">&times;</span>
        <h2>Détails de la tâche</h2>
        <p id="taskDetails"></p>
    </div>
</div>

<div id="CreateGroupModal" class="modal <?php if (isset($_GET['error']) && in_array($_GET['error'], ['empty_group_name', 'group_exists'])) echo 'show'; ?>">
    <div class="modal-content">
        <span class="close" id="closeCreateGroupModal">&times;</span>
        <h2>Créer un nouveau groupe</h2>
        <?php if (isset($_GET['error']) && in_array($_GET['error'], ['empty_group_name', 'group_exists'])): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="../mysql/creation_groupe.php" method="POST" id="createGroupForm">
            <label for="groupName">Nom du groupe :</label><br>
            <input type="text" id="groupName" name="groupName" required><br><br>
            <label for="groupDescription">Description du groupe :</label><br>
            <textarea id="groupDescription" name="groupDescription" rows="4" cols="50"></textarea><br><br>
            <input type="submit" value="Créer">
        </form>
    </div>
</div>


<div id="JoinGroupModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeJoinGroupModal">&times;</span>
        <h2>Rejoindre un groupe</h2>
        <form action="../mysql/rejoin_groupe.php" method="POST" id="joinGroupForm">
            <label for="groupCode">Code du groupe :</label><br>
            <input type="text" id="groupCode" name="groupCode" required><br><br>
            <input type="submit" value="Rejoindre">
        </form>
    </div>
</div>

<div id="CreateModalTask" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Créer une nouvelle tâche</h2>
        <form action="../mysql/creation_tache.php" method="POST" id="taskForm">
            <input type="hidden" name="groupeID" value="<?php echo $_SESSION['Groupe_ID']; ?>">

            <label for="text">Nom de la tâche :</label><br>
            <input type="text" id="text" name="text" required><br><br>

            <label for="categorie">Catégorie :</label><br>
            <input type="text" id="categorie" name="categorie" required><br><br>

            <label for="priority">Priorité :</label><br>
            <select id="priority" name="priority" required>
                <option value="">Sélectionnez une priorité</option>
                <option value="low">Basse</option>
                <option value="medium">Moyenne</option>
                <option value="high">Haute</option>
            </select><br><br>

            <label for="project">Projet :</label><br>
            <select id="project" name="project">
                <option value="">Personnel (aucun projet)</option>
                <?php
                $sql = "SELECT PROJET.ID, PROJET.nom FROM PROJET
                        JOIN est_membre_projet ON PROJET.ID = est_membre_projet.Projet_ID
                        WHERE est_membre_projet.Utilisateur_ID = :user_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['ID'] . "'>" . htmlspecialchars($row['nom']) . "</option>";
                }
                ?>
            </select><br><br>

            <label for="date_tache">Date de la tâche :</label><br>
            <input type="date" id="date_tache" name="date_tache" required><br><br>

            <input type="submit" value="Créer">
        </form>
    </div>
</div>

</body>

<script>

document.addEventListener('DOMContentLoaded', function() {
    var createTaskModal = document.getElementById("CreateModalTask");
    var taskDetailModal = document.getElementById("TaskDetailModal");
    var closeCreateModal = document.getElementById("closeModal");
    var closeDetailModal = document.getElementById("closeDetailModal");
    var taskDetailsElement = document.getElementById("taskDetails");

    var createTaskBtn = document.getElementById("createTaskBtn");
if (createTaskBtn) {
    createTaskBtn.onclick = function(event) {
        event.stopPropagation();
        createTaskModal.classList.add("show");
    }
}


    closeCreateModal.onclick = function() {
        createTaskModal.classList.remove("show");
    }

    closeDetailModal.onclick = function() {
        taskDetailModal.classList.remove("show");
    }

    window.onclick = function(event) {
        if (event.target == createTaskModal) {
            createTaskModal.classList.remove("show");
        } else if (event.target == taskDetailModal) {
            taskDetailModal.classList.remove("show");
        } else if (event.target == createGroupModal) {
            createGroupModal.classList.remove("show");
        } else if (event.target == joinGroupModal) {
            joinGroupModal.classList.remove("show");
        }
    };
    function showTaskDetails(taskId) {
    fetch(`../mysql/get_task_details.php?task_id=${taskId}`)
     .then(response => {
          if (!response.ok) throw new Error('La réponse n\'est pas valide');
          return response.json();
      })
     .then(data => {
            if (typeof data!== 'object') throw new Error('Invalide JSON');
            const taskDetailsElement = document.getElementById("taskDetails");
            taskDetailsElement.innerHTML = `
                <p>Nom de la tâche: ${data.nom}</p>
                <p>Catégorie: ${data.categorie}</p>
                <p>Date Limite de la Tache: ${data.dateTache}</p>
            `;
            taskDetailModal.classList.add("show");
        })
     .catch(error => console.error('Erreur:', error));
}
    document.querySelectorAll('.tasksName').forEach(task => {
        task.onclick = function() {
            var taskId = this.getAttribute('id-task-id');
            showTaskDetails(taskId);
        }
    });
});

    // Example: Log scroll position
    // projectCard.addEventListener('scroll', () => {
    //     console.log('ProjectCard scroll position:', projectCard.scrollTop);
    // });

    // myTasks.addEventListener('scroll', () => {
    //     console.log('MyTasks scroll position:', myTasks.scrollTop);
    // });

const daysWithTasks = document.querySelectorAll('.has-tasks');

function toggleMenu() {
            var menu = document.querySelector('.menu');
            menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
            }
            
            var createGroupModal = document.getElementById("CreateGroupModal");
    var joinGroupModal = document.getElementById("JoinGroupModal");
    var openCreateGroupModal = document.getElementById("openCreateGroupModal");
    var openJoinGroupModal = document.getElementById("openJoinGroupModal");
    var closeCreateGroupModal = document.getElementById("closeCreateGroupModal");
    var closeJoinGroupModal = document.getElementById("closeJoinGroupModal");


    openCreateGroupModal.onclick = function(event) {
        event.preventDefault();
        createGroupModal.classList.add("show");
    }

    closeCreateGroupModal.onclick = function() {
        createGroupModal.classList.remove("show");
    }

    openJoinGroupModal.onclick = function(event) {
        event.preventDefault();
        joinGroupModal.classList.add("show");
    }

    closeJoinGroupModal.onclick = function() {
        joinGroupModal.classList.remove("show");
    }


document.addEventListener('DOMContentLoaded', function() {
    var manageGroupModal = document.getElementById("ManageGroupModal");
    var closeManageGroupModal = document.getElementById("closeManageGroupModal");

    function openManageGroupModal(groupID, groupName, groupDescription, groupCode) {
        document.getElementById("manageGroupName").value = groupName;
        document.getElementById("manageGroupDescription").value = groupDescription;
        document.getElementById("manageGroupCode").value = groupCode;
        manageGroupModal.classList.add("show");
    }

    closeManageGroupModal.onclick = function() {
        manageGroupModal.classList.remove("show");
    }

    window.onclick = function(event) {
        if (event.target == manageGroupModal) {
            manageGroupModal.classList.remove("show");
        }
    };

    document.getElementById("openManageGroupModalBtn").onclick = function() {
        var groupID = 1;
        var groupName = "Nom du Groupe";
        var groupDescription = "Description du Groupe";
        var groupCode = "Code du Groupe";
        openManageGroupModal(groupID, groupName, groupDescription, groupCode);
    };
});

function toggleTaskCompletion(element) {
    var taskIcon = element;
    var taskName = element.parentElement.querySelector('.tasksName');

    if (taskIcon.classList.contains('notDone')) {
        taskIcon.classList.remove('notDone');
        taskIcon.classList.add('done');
        taskName.classList.remove('notDone');
        taskName.classList.add('done', 'tasksLine');
    } else if (taskIcon.classList.contains('done')) {
        taskIcon.classList.remove('done');
        taskIcon.classList.add('notDone');
        taskName.classList.remove('done', 'tasksLine');
        taskName.classList.add('notDone');
    }
}


var interval = setInterval(function () {
    fetch('../mysql/fetch_session.php')
    }, 5000);

</script>
</html>