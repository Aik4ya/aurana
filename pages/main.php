<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once '../mysql/cookies_uid.php';
require_once '../mysql/connexion_bdd.php';
require_once '../mysql/verif_groupe.php';

session_start();
$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

ecriture_log("main");
verif_session();
$conn = connexion_bdd();

$nom_groupe = null;

if (isset($_GET['groupe'])) {
    $groupe = $_GET['groupe'];
    $_SESSION['Groupe'] = $groupe;
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

if ($_GET['groupe'] != "none") {
    $sql = "SELECT droit FROM est_membre WHERE Utilisateur_ID = :utilisateur_id AND GROUPE = :groupe_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':utilisateur_id', $_SESSION['Utilisateur_ID']);
    $stmt->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
    $stmt->execute();
    if ($stmt->fetch(PDO::FETCH_ASSOC)['droit'] == 1) {
        $_SESSION['Droit_groupe'] = 1;
    } else {
        $_SESSION['Droit_groupe'] = 0;
    }
} else {
    $_SESSION['Droit_groupe'] = 0;
}


if (isset($_GET['search'])) {
    $searchTerm = '%' . filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) . '%';

    // Rechercher dans les projets
    $sql = "SELECT ID, nom, status, priorite, deadline 
            FROM PROJET 
            WHERE id_groupe = :id_groupe 
            AND nom LIKE :searchTerm 
            AND ID IN (SELECT Projet_ID FROM est_membre_projet WHERE Utilisateur_ID = :id_utilisateur)
            ORDER BY deadline ASC";
    $stmt1 = $conn->prepare($sql);
    $stmt1->bindParam(':id_groupe', $_SESSION['Groupe_ID']);
    $stmt1->bindParam(':id_utilisateur', $_SESSION['Utilisateur_ID']);
    $stmt1->bindParam(':searchTerm', $searchTerm);
    $stmt1->execute();
    $projects = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // Rechercher dans les tâches
    $sql = "SELECT TACHE.Tache_ID, TACHE.Texte 
            FROM es_assigner
            INNER JOIN TACHE ON es_assigner.Tache_ID = TACHE.Tache_ID
            WHERE (es_assigner.Utilisateur_ID = :user_id 
            AND (TACHE.Groupe_ID = :groupe_id OR :groupe_id = 0)) 
            AND TACHE.Texte LIKE :searchTerm 
            ORDER BY TACHE.Date_Tache";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
    $stmt2->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
    $stmt2->bindParam(':searchTerm', $searchTerm);
    $stmt2->execute();
    $tasks = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}


// avancer / reculer dans le calendrier
if (isset($_GET['cal'])) {
    if ($_GET['cal'] == "'prev'") {
        $_SESSION['mois'] -= 1;
        if ($_SESSION['mois'] == 0) {
            $_SESSION['mois'] = 12;
            $_SESSION['annee'] -= 1;
        }
    } elseif ($_GET['cal'] == "'next'") {
        $_SESSION['mois'] += 1;
        if ($_SESSION['mois'] == 13) {
            $_SESSION['mois'] = 1;
            $_SESSION['annee'] += 1;
        }
    }
    header("Location: main.php?groupe={$_GET['groupe']}");
    exit;
} elseif (!isset($_SESSION['mois']) || !isset($_SESSION['annee'])) {
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

function fetchTasksWithProjects($dbh, $projectID = 'all') {
    $userID = $_SESSION['Utilisateur_ID'];
    $groupeID = isset($_SESSION['Groupe_ID']) ? $_SESSION['Groupe_ID'] : 0;

    $sql = "SELECT t.Tache_ID, t.Texte, p.nom AS NomProjet, t.Date_Tache
            FROM es_assigner ea
            JOIN TACHE t ON ea.Tache_ID = t.Tache_ID
            LEFT JOIN tache_assignee_projet tap ON t.Tache_ID = tap.id_tache
            LEFT JOIN PROJET p ON tap.id_projet = p.ID
            WHERE ea.Utilisateur_ID = :userID AND (t.Groupe_ID = :groupeID OR t.Groupe_ID IS NULL)";
    
    if ($projectID !== 'all') {
        $sql .= " AND tap.id_projet = :projectID";
    }
    
    $sql .= " ORDER BY p.nom ASC, t.Date_Tache DESC";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':groupeID', $groupeID);
    if ($projectID !== 'all') {
        $stmt->bindParam(':projectID', $projectID);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchTasksForProject($dbh, $groupeID, $projectID = 'all') {
    $userID = $_SESSION['Utilisateur_ID'];

    $sql = "SELECT Date_Tache FROM TACHE 
            WHERE Tache_ID IN (SELECT Tache_ID FROM es_assigner WHERE Utilisateur_ID = :userID) 
            AND Groupe_ID = :groupeID";

    if ($projectID !== 'all') {
        $sql .= " AND Tache_ID IN (SELECT id_tache FROM tache_assignee_projet WHERE id_projet = :projectID)";
    }

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':groupeID', $groupeID);
    if ($projectID !== 'all') {
        $stmt->bindParam(':projectID', $projectID);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


$projectID = isset($_GET['projectID']) ? $_GET['projectID'] : 'all';
$task_dates = fetchTasksForProject($conn, $_SESSION['Groupe_ID'], $projectID);

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
    <link rel="stylesheet" href="../css/base_main.css">
    <script type="text/javascript" src="../js/aurana.js"></script>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<style>
.btn {
    display: inline-block;
    padding: 5px 5px;
    font-size: 14px;
    font-weight: 400;
    text-align: center;
    text-decoration: none;
    white-space: nowrap;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin: 5px;
}

.btn-primary {
    background-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-danger {
    background-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
}

.btn-info {
    background-color: #17a2b8;
}

.btn-info:hover {
    background-color: #138496;
}

#searchMembersInput {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
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
                            <a href="main_task.php?groupe=<?php echo $_GET['groupe']; ?>">
                                <span class="material-symbols-outlined">
                                    check_box
                                </span>
                                <span class="title">Tâches</span>
                            </a>
                        </li>
                        <?php if ($_GET['groupe'] != "none"): ?>
                            <li>
                                <a href="main_chat.php?groupe=<?php echo $_GET['groupe']; ?>">
                                    <span class="material-symbols-outlined">chat_bubble</span>
                                    <span class="title">Messages</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="main_files.php?groupe=<?php echo $_GET['groupe']; ?>">
                                <span class="material-symbols-outlined">account_balance_wallet</span>
                                <span class="title">Fichiers</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </header>
            <div class="disconnect">
                <div class="decoBtn">
                    <form action="logout.php">
                        <button id='deconnexion'>Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="top">
                <div class="searchBx">
                    <?php if ($nom_groupe != null): ?>
                        <h2><?php echo htmlspecialchars($nom_groupe); ?></h2>
                    <?php endif; ?>
                    <div class="inputBx">
                    <span class="material-symbols-outlined searchOpen">
                        search
                    </span>
                    <input type="text" id="searchInput" placeholder="Rechercher...">
                    <span class="material-symbols-outlined searchClose" onclick="clearSearch()">
                        close
                    </span>
                </div>
                </div>



                <div class="user">
                    <?php
                    // Affichage groupes + menu déroulant
                    echo "<h2>" . $_SESSION['Pseudo'] . "<br>";

                    if ($_SESSION['Droit_groupe'] == 2) {
                        echo "<span>Administrateur du Groupe</span></h2>";
                    } elseif ($_SESSION['Droit_groupe'] == 1) {
                        echo "<span>Propriétaire du Groupe</span></h2>";
                    }
                    
                    if ($_SESSION['Droit'] == 1) {
                        echo "<br><span>Admin</span></h2>";
                    }
                    ?>
                    <div class="arrow" onclick="toggleMenu()">
                        <span class="material-symbols-outlined">expand_more</span>
                    </div>
                    <div class="menu" style="display: none;">
                        <ul id="menuList">
                            <li><a href="../pages/main_profile.php">Profil</a></li>
                            <li><a href="../pages/choisir_groupe.php">Choisir son groupe</a></li>
                            <li><a href="#" id="openCreateGroupModal">Créer un groupe</a></li>
                            <li><a href="#" id="openJoinGroupModal">Rejoindre un groupe</a></li>
                            <li><a href="#" id="openManageGroupModalBtn" onclick="openManageGroupModal(<?php echo $_SESSION['Groupe_ID']; ?>, '<?php echo $nom_groupe; ?>', 'Description du groupe', 'Code du groupe')">Gérer le groupe</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <main>
                <div class="projectCard">
                    <div class="projectHead">
                    <h2>Projets</h2>
                    <?php if ($_SESSION['Droit_groupe'] == 1): ?>
                        <img src="../img/plus.png" id="createProjectBtn">
                    <?php endif; ?>
                    </div>
                    <br>
                    <ul>
                        <?php
                        // Affichage projets
                        $sql = "SELECT ID, nom, status, priorite, deadline 
                            FROM PROJET 
                            WHERE id_groupe = :id_groupe 
                            AND ID IN (SELECT Projet_ID FROM est_membre_projet WHERE Utilisateur_ID = :id_utilisateur)
                            ORDER BY deadline ASC LIMIT 3";
                        $stmt1 = $conn->prepare($sql);
                        $stmt1->bindParam(':id_groupe', $_SESSION['Groupe_ID']);
                        $stmt1->bindParam(':id_utilisateur', $_SESSION['Utilisateur_ID']);
                        $stmt1->execute();
                        $rowcount = $stmt1->rowCount();

                        if ($rowcount > 0) {
                            while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                                $id = $row['ID'];
                                $nom = $row['nom'];
                                $status = $row['status'];
                                $priorite = $row['priorite'];
                                $deadline = $row['deadline'];
                                $jours = strtotime($deadline) - strtotime('today');
                                $jours = floor($jours / (60 * 60 * 24));
                                $groupe = $_GET['groupe'];

                                $sql = "SELECT count(*) FROM tache_assignee_projet INNER JOIN TACHE ON tache_assignee_projet.id_tache = TACHE.Tache_ID WHERE tache_assignee_projet.id_projet = :id_projet AND TACHE.done = 1";
                                $stmt2 = $conn->prepare($sql);
                                $stmt2->bindParam(':id_projet', $id);
                                $stmt2->execute();
                                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                                $tachefin = $row['count(*)'];

                                $sql = "SELECT count(*) FROM tache_assignee_projet WHERE id_projet = :id_projet";
                                $stmt3 = $conn->prepare($sql);
                                $stmt3->bindParam(':id_projet', $id);
                                $stmt3->execute();
                                $row = $stmt3->fetch(PDO::FETCH_ASSOC);
                                $tachetotal = $row['count(*)'];

                                if ($tachefin == $tachetotal) {
                                    $css_status = "processFini";
                                    $css_line = "lineFini";
                                    $css_due = "dueFini";
                                } elseif (strtotime($deadline) <= strtotime('+7 days') || strtotime($deadline) < strtotime('today')) {
                                    $css_status = "processRetard";
                                    $css_line = "lineRetard";
                                    $css_due = "dueRetard";
                                } else {
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
                                echo "<div class=\"projectDots\" onclick=\"openProjectDetailModal($id)\">";
                                echo "<span class=\"material-symbols-outlined\">more_horiz</span>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class=\"projectProgress\">";
                                echo "<div class=\"$css_status\">";
                                echo "<h2>$status</h2>";
                                echo "</div>";
                                echo "<div class=\"$css_priorite\">";
                                echo "<h2>$priorite</h2>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class=\"task\">";
                                echo "<h2>Tâches faites: <strong>$tachefin</strong> / $tachetotal</h2>";
                                if ($tachetotal == 0) {
                                    echo "<span class=\"$css_line\" style=\"width: 0%;\"></span>";
                                } else {
                                    echo "<span class=\"$css_line\" style=\"width: " . ($tachefin / $tachetotal) * 100 . "%;\"></span>";
                                }
                                echo "</div>";
                                echo "<div class=\"$css_due\">";
                                echo "<h2>Du pour le : $deadline ($jours J)</h2>";
                                echo "</div>";
                                echo "</div>";
                                echo "<br>";
                                echo "</li>";
                            }
                        } else {
                            echo "<li>";
                            echo "<div class=\"projectCard\">";
                            echo "<p>Aucun projet assigné</p>";
                            echo "</div>";
                            echo "</li>";
                        }
                        ?>
                    </ul>
                </div>

                <div class="myTasks">
                    <div class="tasksHead">
                        <h2>Mes tâches</h2>
                        <?php if ($_SESSION['groupe'] != "none"): ?>
                            <img src="../img/plus.png" id="createTaskBtn">
                        <?php endif; ?>
                    </div>
                    <div class="tasks">
                        <ul>
                        <?php
                    $stmt_tasks = $conn->prepare("
                        SELECT TACHE.Tache_ID, TACHE.Texte
                        FROM es_assigner
                        INNER JOIN TACHE ON es_assigner.Tache_ID = TACHE.Tache_ID
                        WHERE (es_assigner.Utilisateur_ID = :user_id 
                        AND (TACHE.Groupe_ID = :groupe_id OR :groupe_id = 0)) AND TACHE.done = 0
                        ORDER BY TACHE.Date_Tache
                        LIMIT 7
                    ");
                    $stmt_tasks->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
                    $stmt_tasks->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
                    $stmt_tasks->execute();

                    while ($row_tasks = $stmt_tasks->fetch(PDO::FETCH_ASSOC)) {
                        $task_id = $row_tasks['Tache_ID'];
                        $task_text = $row_tasks['Texte'];
                    
                        echo "<li>";
                        echo "<span class=\"tasksIconName\">";
                        echo "<span class=\"tasksIcon notDone\" onclick=\"toggleTaskCompletion(this)\">";
                        echo "<span class=\"material-symbols-outlined\"></span>";
                        echo "</span>";
                        echo "<span class=\"tasksName notDone\" data-task-id=\"$task_id\">" . htmlspecialchars($task_text) . "</span>";
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

                <!-- début calendrier -->
                <div class="calendar">
                    <div class="calendarHead">
                        <h2><?php
                            $mois_francais = [
                                1 => "Janvier", 2 => "Février", 3 => "Mars", 4 => "Avril", 5 => "Mai", 6 => "Juin",
                                7 => "Juillet", 8 => "Août", 9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"
                            ];
                            echo $mois_francais[$_SESSION['mois']] . " " . $_SESSION['annee'];
                        ?></h2>
                        <div class="calendarIcon">
                            <a href="main.php?groupe=<?php echo $_GET['groupe']; ?>&cal='prev'" class="material-symbols-outlined">
                                chevron_left
                            </a>
                            <a href="main.php?groupe=<?php echo $_GET['groupe']; ?>&cal='next'" class="material-symbols-outlined">
                                chevron_right
                            </a>
                        </div>
                        <select id="projectSelect">
                            <option value="all">Tous les projets</option>
                            <?php
                            $sql = "SELECT PROJET.ID, PROJET.nom FROM PROJET 
                                    JOIN est_membre_projet ON PROJET.ID = est_membre_projet.Projet_ID 
                                    WHERE est_membre_projet.Utilisateur_ID = :user_id";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = ($projectID == $row['ID']) ? "selected" : "";
                                echo "<option value='" . $row['ID'] . "' $selected>" . htmlspecialchars($row['nom']) . "</option>";
                            }
                            ?>
                        </select>
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

                                $dayClass = '';

                                if ($hasTasks) {
                                    $dayClass .= ' has-tasks';
                                }

                                if ($hasProjet) {
                                    $dayClass .= ' has-projet';
                                }

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

                            for ($i = $lastDay; $i < 7; $i++) {
                                echo "<li class='inactive'></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <!-- affichage messages -->
                <div class="messages">
                    <div class="messagesHead">
                        <h2>Messages récents</h2>
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

                            $sql2 = "SELECT Pseudo, Avatar FROM UTILISATEUR WHERE Utilisateur_ID = :user_id";
                            $stmt2 = $conn->prepare($sql2);
                            $stmt2->bindParam(':user_id', $message_sender);
                            $stmt2->execute();
                            $user_data = $stmt2->fetch(PDO::FETCH_ASSOC);
                            $pseudo = $user_data['Pseudo'];
                            $avatar_path = $user_data['Avatar'];

                            // Si l'avatar n'existe pas, utiliser une image par défaut
                            if (!$avatar_path) {
                                $avatar_path = '../img/aurana_logo.png';
                            } else {
                                $avatar_path = '../uploads/avatars/' . $avatar_path;
                            }

                            echo "<div class=\"messagesUser\">";
                            echo "<div class=\"messagesUserImg\">";
                            echo "<img src=\"" . htmlspecialchars($avatar_path) . "\" alt=\"Avatar\">";
                            echo "</div>";
                            echo "<h2>" . htmlspecialchars($pseudo) . "<br><span>" . htmlspecialchars($message_text) . "</span></h2>";
                            echo "</div>";
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modales -->

    <div id="ManageGroupModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeManageGroupModal">&times;</span>
        <h2>Gérer le groupe</h2>
        <form id="manageGroupForm" action="../mysql/update_groupe.php" method="POST">
            <input type="hidden" id="manageGroupID" name="groupID">
            <label for="manageGroupName">Nom du groupe :</label><br>
            <input type="text" id="manageGroupName" name="groupName"><br><br>
            <label for="manageGroupDescription">Description du groupe :</label><br>
            <textarea id="manageGroupDescription" name="groupDescription" rows="4" cols="50"></textarea><br><br>
            <label for="manageGroupCode">Code du groupe :</label><br>
            <div style="display: flex;">
                <input type="text" id="manageGroupCode" name="groupCode" readonly style="flex: 1;"><br><br>
                <button type="button" id="generateGroupCode" style="margin-left: 10px;">Modifier</button>
            </div><br>
            <button type="submit" id="editGroupBtn">Sauvegarder</button>
        </form>
    </div>
</div>




    <div id="TaskDetailModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeDetailModal">&times;</span>
        <h2>Détails de la tâche</h2>
        <p id="taskDetails"></p>
    </div>
</div>


<div id="CreateGroupModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeCreateGroupModal">&times;</span>
            <h2>Créer un nouveau groupe</h2>
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

    <div id="CreateProjectModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeProjectModal">&times;</span>
            <h2>Créer un nouveau projet</h2>
            <form action="../mysql/creation_projet.php" method="POST" id="projectForm">
                <input type="hidden" name="groupeID" value="<?php echo $_SESSION['Groupe_ID']; ?>">

                <label for="projectName">Nom du projet :</label><br>
                <input type="text" id="projectName" name="projectName" required><br><br>

                <label for="projectStatus">Status :</label><br>
                <input type="text" id="projectStatus" name="projectStatus" required><br><br>

                <label for="projectPriority">Priorité :</label><br>
                <select id="projectPriority" name="projectPriority" required>
                    <option value="">Sélectionnez une priorité</option>
                    <option value="Basse">Basse</option>
                    <option value="Moyenne">Moyenne</option>
                    <option value="Haute">Haute</option>
                </select><br><br>

                <label for="projectDeadline">Deadline :</label><br>
                <input type="date" id="projectDeadline" name="projectDeadline" required><br><br>

                <input type="submit" value="Créer">
            </form>
        </div>
    </div>

    <div id="ProjectDetailModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeProjectDetailModal">&times;</span>
        <h2>Détails du projet</h2>
        <div id="currentProjectMembers">
            <h3>Membres du projet</h3>
            <ul id="projectMembersList"></ul>
        </div>
        <div id="addProjectMembers">
            <h3>Ajouter une personne au projet</h3>
            <input type="text" id="searchMembersInput" placeholder="Rechercher un membre...">
            <form id="addProjectMembersForm">
                <div id="nonMembersList"></div>
                <button type="button" id="saveProjectMembers" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
        <div id="projectTasks">
            <h3>Liste des tâches</h3>
            <ul id="projectTasksList"></ul>
        </div>
    </div>
</div>


    <script>
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error') && urlParams.get('error') === 'group_exists') {
        alert('Ce groupe existe déjà.');
    }
});
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('searchInput');
    
    // Fonction pour exécuter la recherche
    searchInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            var searchTerm = searchInput.value.trim();
            if (searchTerm) {
                window.location.href = `main.php?groupe=<?php echo $_GET['groupe']; ?>&search=${encodeURIComponent(searchTerm)}`;
            }
        }
    });
    
    // Fonction pour nettoyer la recherche et retourner à la page principale
    window.clearSearch = function() {
        searchInput.value = '';
        window.location.href = `main.php?groupe=<?php echo $_GET['groupe']; ?>`;
    };

    // Fonction pour basculer le menu utilisateur
    window.toggleMenu = function() {
        var menu = document.querySelector('.menu');
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
    };

    var createTaskModal = document.getElementById("CreateModalTask");
    var taskDetailModal = document.getElementById("TaskDetailModal");
    var closeCreateModal = document.getElementById("closeModal");
    var closeDetailModal = document.getElementById("closeDetailModal");

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
        }
    };

    document.querySelectorAll('.tasksName').forEach(task => {
        task.onclick = function() {
            var taskId = this.getAttribute('data-task-id');
            showTaskDetails(taskId);
        }
    });

    var calendarDays = document.querySelectorAll('.days li.has-tasks');
    calendarDays.forEach(function(day) {
        day.addEventListener('click', function() {
            var taskIds = this.getAttribute('data-task-ids');
            if (taskIds) {
                var taskIdArray = taskIds.split(',');
                if (taskIdArray.length > 0) {
                    showTaskDetails(taskIdArray[0]); // Open the modal with the first task ID
                }
            }
        });
    });

    function showTaskDetails(taskId) {
        fetch(`../mysql/get_task_details.php?task_id=${taskId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error fetching task details');
                }
                const taskDetailsElement = document.getElementById("taskDetails");
                const task = data.task;

                const taskDate = new Date(task.Date_Tache);
                const currentDate = new Date();
                const daysRemaining = Math.floor((taskDate - currentDate) / (1000 * 60 * 60 * 24) + 1);

                taskDetailsElement.innerHTML = `
                    <p>Nom de la tâche: ${task.Texte}</p>
                    <p>Catégorie: ${task.Categorie}</p>
                    <p>Date Limite de la Tache: ${task.Date_Tache} (${daysRemaining} J)</p> 
                    <p>Assignée à: ${task.Pseudo}</p>
                `;
                document.getElementById("TaskDetailModal").classList.add("show");
            })
            .catch(error => console.error('Error:', error));
    }

    var manageGroupModal = document.getElementById("ManageGroupModal");
    var createGroupModal = document.getElementById("CreateGroupModal");
    var joinGroupModal = document.getElementById("JoinGroupModal");
    var closeManageGroupModal = document.getElementById("closeManageGroupModal");
    var closeCreateGroupModal = document.getElementById("closeCreateGroupModal");
    var closeJoinGroupModal = document.getElementById("closeJoinGroupModal");
    var generateGroupCodeBtn = document.getElementById("generateGroupCode");

    document.getElementById('openCreateGroupModal').addEventListener('click', function () {
        createGroupModal.style.display = 'block';
    });

    document.getElementById('openJoinGroupModal').addEventListener('click', function () {
        joinGroupModal.style.display = 'block';
    });

    closeCreateGroupModal.onclick = function() {
        createGroupModal.style.display = 'none';
    };

    closeJoinGroupModal.onclick = function() {
        joinGroupModal.style.display = 'none';
    };

    window.openManageGroupModal = function(groupID) {
        fetch(`../mysql/get_group_details.php?group_id=${groupID}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var group = data.group;
                    document.getElementById("manageGroupID").value = group.Groupe_ID;
                    document.getElementById("manageGroupName").value = group.Nom;
                    document.getElementById("manageGroupDescription").value = group.Description_Groupe;
                    document.getElementById("manageGroupCode").value = group.Code;
                    manageGroupModal.classList.add("show");
                } else {
                    console.error('Error fetching group details:', data.error);
                }
            })
            .catch(error => console.error('Error:', error));
    };

    closeManageGroupModal.onclick = function() {
        manageGroupModal.classList.remove("show");
    };

    window.onclick = function(event) {
        if (event.target == manageGroupModal) {
            manageGroupModal.classList.remove("show");
        } else if (event.target == createGroupModal) {
            createGroupModal.style.display = 'none';
        } else if (event.target == joinGroupModal) {
            joinGroupModal.style.display = 'none';
        }
    };

    generateGroupCodeBtn.onclick = function() {
        var newCode = generateRandomCode(6);
        document.getElementById("manageGroupCode").value = newCode;
    };

    function generateRandomCode(length) {
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var code = '';
        for (var i = 0; i < length; i++) {
            code += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return code;
    }

    document.getElementById("openManageGroupModalBtn").onclick = function() {
        openManageGroupModal(<?php echo $_SESSION['Groupe_ID']; ?>);
    };

    function toggleTaskCompletion(element) {
        var taskIcon = element;
        var taskName = element.parentElement.querySelector('.tasksName');

        if (taskIcon.classList.contains('notDone')) {
            taskIcon.classList.remove('notDone');
            taskIcon.classList.add('done');
            taskName.classList.remove('notDone');
            taskName.classList.add('done', 'tasksLine');
            updateTaskStatus(taskName.getAttribute('data-task-id'), 1);
        } else if (taskIcon.classList.contains('done')) {
            taskIcon.classList.remove('done');
            taskIcon.classList.add('notDone');
            taskName.classList.remove('done', 'tasksLine');
            taskName.classList.add('notDone');
            updateTaskStatus(taskName.getAttribute('data-task-id'), 0);
        }
    }

    function updateTaskStatus(taskId, status) {
        fetch('../mysql/update_done.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `taskId=${taskId}&task_status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error updating task status:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function toggleStarCompletion(element) {
        var taskIcon = element;
        var taskName = element.parentElement.querySelector('.tasksName');

        if (taskIcon.classList.contains('half')) {
            taskIcon.classList.remove('half');
            taskIcon.classList.add('full');
            taskName.classList.remove('half');
            taskName.classList.add('full', 'tasksLine');
            updateStarStatus(taskName.getAttribute('data-task-id'), 1);
        } else if (taskIcon.classList.contains('full')) {
            taskIcon.classList.remove('full');
            taskIcon.classList.add('half');
            taskName.classList.remove('full', 'tasksLine');
            taskName.classList.add('half');
            updateStarStatus(taskName.getAttribute('data-task-id'), 0);
        }
    }

    function updateStarStatus(taskId, status) {
        fetch('../mysql/update_stars.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `taskId=${taskId}&task_status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error updating task status:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    var projectSelect = document.getElementById('projectSelect');
    projectSelect.addEventListener('change', function() {
        var selectedProjectID = this.value;
        window.location.href = `main.php?groupe=<?php echo $_GET['groupe']; ?>&projectID=${selectedProjectID}`;
    });

    var projectDetailModal = document.getElementById('ProjectDetailModal');
    var closeProjectDetailModal = document.getElementById('closeProjectDetailModal');
    var saveProjectMembers = document.getElementById('saveProjectMembers');
    var searchInput = document.getElementById('searchMembersInput');
    var addProjectMembers = document.getElementById('addProjectMembers');

    closeProjectDetailModal.onclick = function() {
        projectDetailModal.style.display = 'none';
    };

    window.onclick = function(event) {
        if (event.target == projectDetailModal) {
            projectDetailModal.style.display = 'none';
        }
    };

    window.openProjectDetailModal = function(projectID) {
        fetchProjectDetails(projectID);
        projectDetailModal.style.display = 'block';
    };

    function fetchProjectDetails(projectID) {
        fetch(`../mysql/get_project_details.php?project_id=${projectID}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error fetching project details');
                }
                updateProjectDetailModal(data, projectID);
            })
            .catch(error => console.error('Error:', error));
    }

    function updateProjectDetailModal(data, projectID) {
        var projectMembersList = document.getElementById('projectMembersList');
        var isAdmin = data.isAdmin;

        projectMembersList.innerHTML = data.members.map(member => `
            <li>
                ${member.Pseudo} ${member.admin ? '(Admin)' : ''}
                ${isAdmin ? `
                    <button class="btn btn-primary promote-btn" onclick="promoteToAdmin(${projectID}, ${member.Utilisateur_ID})">Promouvoir</button>
                    <button class="btn btn-danger remove-btn" onclick="removeMember(${projectID}, ${member.Utilisateur_ID})">Supprimer</button>
                ` : ''}
            </li>
        `).join('');

        var projectTasksList = document.getElementById('projectTasksList');
        projectTasksList.innerHTML = data.tasks.map(task => `
            <li>
                ${task.Texte} - ${task.Pseudo} - ${task.Date_Tache}
                ${isAdmin ? `<button class="btn btn-info" onclick="openTaskDetailModal(${task.Tache_ID})">Modifier</button>` : ''}
            </li>
        `).join('');

        if (isAdmin) {
            addProjectMembers.style.display = 'block';
        } else {
            addProjectMembers.style.display = 'none';
        }

        const nonMembersList = document.getElementById('nonMembersList');
        nonMembersList.innerHTML = data.nonMembers.map(nonMember => `
            <label><input type='checkbox' name='projectMembers[]' value='${nonMember.Utilisateur_ID}'> ${nonMember.Pseudo}</label><br>
        `).join('');

        saveProjectMembers.onclick = function() {
            const formData = new FormData(document.getElementById('addProjectMembersForm'));
            formData.append('project_id', projectID);
            fetch('../mysql/add_project_members.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Membres ajoutés avec succès');
                    fetchProjectDetails(projectID); // Refresh the details
                } else {
                    alert('Erreur lors de l\'ajout des membres');
                }
            });
        };

        searchInput.addEventListener('input', function() {
            var searchQuery = searchInput.value.toLowerCase();
            var filteredMembers = data.nonMembers.filter(member => 
                member.Pseudo.toLowerCase().includes(searchQuery)
            );
            nonMembersList.innerHTML = filteredMembers.map(nonMember => `
                <label><input type='checkbox' name='projectMembers[]' value='${nonMember.Utilisateur_ID}'> ${nonMember.Pseudo}</label><br>
            `).join('');
        });
    }

    window.promoteToAdmin = function(projectID, userID) {
        if (confirm("Voulez-vous vraiment promouvoir cet utilisateur en tant qu'administrateur ?")) {
            fetch(`../mysql/promote_to_admin.php?project_id=${projectID}&user_id=${userID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Utilisateur promu avec succès');
                        fetchProjectDetails(projectID); // Refresh the details
                    } else {
                        alert('Erreur lors de la promotion de l\'utilisateur');
                    }
                });
        }
    };

    window.removeMember = function(projectID, userID) {
        if (confirm("Voulez-vous vraiment supprimer cet utilisateur du projet ?")) {
            fetch(`../mysql/remove_member.php?project_id=${projectID}&user_id=${userID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Utilisateur supprimé avec succès');
                        fetchProjectDetails(projectID); // Refresh the details
                    } else {
                        alert('Erreur lors de la suppression de l\'utilisateur');
                    }
                });
        }
    }

    function openTaskDetailModal(taskId) {
        showTaskDetails(taskId);
    }
});

setInterval(function () {
    fetch('../mysql/fetch_session.php')
}, 5000);





    </script>
</body>
</html>
