<?php
require_once '../mysql/cookies_uid.php';
require_once '../mysql/connexion_bdd.php';


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

$stmt_tasks_dates = $conn->prepare("SELECT Date_Tache FROM TACHE WHERE Tache_ID IN (SELECT Tache_ID FROM es_assigner WHERE Utilisateur_ID = :user_id) AND Groupe_ID = :groupe_id
");
$stmt_tasks_dates->bindParam(':groupe_id', $_SESSION['Groupe_ID']);
$stmt_tasks_dates->bindParam(':user_id', $_SESSION['Utilisateur_ID']);
$stmt_tasks_dates->execute();
$task_dates = $stmt_tasks_dates->fetchAll(PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurana - Dashboard</title>
    <link rel="stylesheet" href="../css/main.css">
    <script type="text/javascript" src="../js/aurana.js"></script>
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

.modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgb(0,0,0); 
    background-color: rgba(0,0,0,0.4); 
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

.modal.show {
    display: block;
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

.dropdown-menu {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 999;
    padding: 5px 0;
    }

    .dropdown-toggle:hover +.dropdown-menu,
    .dropdown-toggle:focus +.dropdown-menu {
    display: block;
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
                    $conn = connexion_bdd();
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
                    <ul>
                    <?php

                    // affichage projets

                        $sql="SELECT ID, nom, status, priorite, deadline FROM PROJET WHERE id_groupe = :id_groupe"; //nombre de projet dans grupe actuel
                        $stmt1 = $conn->prepare($sql);
                        $stmt1->bindParam(':id_groupe', $_SESSION['Groupe_ID']);
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

                                
                                echo "<li>";
                                echo "<div>";
                                echo "<div class=\"projectTop\">";
                                echo "<h2>$nom<br><span>$groupe</span></h2>";
                                echo "<div class=\"projectDots\">";
                                echo "<span class=\"material-symbols-outlined\">";
                                echo "more_horiz";
                                echo "</span>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class=\"projectProgress\">";
                                echo "<div class=\"process\">";
                                echo "<h2>$status</h2>";
                                echo "</div>";
                                        echo "<div class=\"priority\">";
                                echo "<h2>$priorite</h2>";
                                echo "</div>";
                                echo "</div>";
                                
                                echo "<div class=\"task\">";
                                echo "<h2>Tâches faites: <strong>" . $tachefin . "</strong> / " . $tachetotal . "</h2>";
                                if ($tachetotal == 0) {
                                    echo "<span class=\"line\" style=\"width: 0%;\"></span>"; // éviter division par 0
                                } else {
                                    echo "<span class=\"line\" style=\"width: " . ($tachefin / $tachetotal) * 100 . "%;\"></span>";
                                }
                                echo "</div>";
                                echo "<div class=\"due\">";
                                echo "<h2>Du pour le : $deadline</h2>";
                                echo "</div>";
                                echo "</div>";
                            }
                            
                        } else { // si pas de projet
                            echo "<li>";
                            echo "<div class=\"projectCard\">";
                            echo "<p> Aucun projet </p>";
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
                            echo "<button id=\"createTaskBtn\">Créer une tâche</button>";
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
                            echo "<span class=\"tasksIcon notDone\" onclick=\"TaskIcon(this)\">";
                            echo "<span class=\"material-symbols-outlined\"></span>";
                            echo "</span>";
                            echo "<span class=\"tasksName\" id-task-id=\"$task_id\">" . htmlspecialchars($task_text) . "</span>";
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
                            
                                if ($hasTasks){
                                    $dayClass = 'has-tasks';
                                } else {
                                    $dayClass = '';
                                }
                                
                                // affichage taches
                            
                                if ($i == date('d') && $currentYear == $selectedYear) {
                                    if ($currentMonth == $selectedMonth) {
                                        echo "<li class='active'>$i</li>";
                                    }
                                } elseif ($currentMonth > $selectedMonth || $currentYear > $selectedYear) {
                                    if ($currentYear >= $selectedYear){
                                        echo "<li class='inactive'>$i</li>";
                                    } else {
                                        echo "<li class=$dayClass>$i</li>";
                                    }
                                } elseif ($currentMonth == $selectedMonth && $i < date('d')) {
                                    echo "<li class='inactive'>$i</li>";
                                } else {
                                    echo "<li class=$dayClass>$i</li>";
                                }
                            }
                            
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="messages">
                    <div class="messagesHead">
                        <h2>Messages</h2>
                    </div>
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img1.jpg" alt="img1">
                        </div>
                        <h2>Marvin McKinney<br><span>Commodo volutpot noc</span></h2>
                    </div>
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img2.jpg" alt="img2">
                        </div>
                        <h2>Wade Warren<br><span>Commodo volutpot noc</span></h2>
                    </div>
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img3.jpg" alt="img3">
                        </div>
                        <h2>John Cooper<br><span>Commodo volutpot noc</span></h2>
                    </div>
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img4.jpg" alt="img4">
                        </div>
                        <h2>Darlene Robertson<br><span>Commodo volutpot noc</span></h2>
                    </div>
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img5.jpg" alt="img5">
                        </div>
                        <h2>Kristin Watson<br><span>Commodo volutpot noc</span></h2>
                    </div>
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

    <label for="date_tache">Date de la tâche :</label><br>
    <input type="date" id="date_tache" name="date_tache" required><br><br>

    <input type="submit" value="Créer">
</form>


    </div>
</div>
</body>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Définir toggleMenu en premier
    window.toggleMenu = () => {
        const menu = document.querySelector('.menu');
        menu.style.display = (menu.style.display === 'none' || !menu.style.display) ? 'block' : 'none';
    };

    const toggle = document.querySelector('.toggle');
    const left = document.querySelector('.left');
    const right = document.querySelector('.right');
    const body = document.querySelector('body');
    const searchBx = document.querySelector('.searchBx');
    const modals = document.querySelectorAll('.modal');
    const calendarHead = document.querySelector('.calendarHead h2');
    const daysList = document.querySelector('.days');
    const taskDetailsElement = document.getElementById("taskDetails");

    const toggleClass = (element, className) => element.classList.toggle(className);

    toggle.addEventListener('click', () => {
        toggleClass(toggle, 'active');
        toggleClass(left, 'active');
        toggleClass(right, 'overlay');
        body.style.overflow = 'hidden';
    });

    const closeElements = () => {
        toggle.classList.remove('active');
        left.classList.remove('active');
        right.classList.remove('overlay');
        body.style.overflow = '';
    };

    document.querySelectorAll('.close').forEach(btn => btn.onclick = closeElements);
    right.onclick = (e) => e.target == right && closeElements();

    document.querySelector('.searchOpen').onclick = () => searchBx.classList.add('active');
    document.querySelector('.searchClose').onclick = () => searchBx.classList.remove('active');

    const updateCalendar = () => {
        let date = new Date();
        let month = date.getMonth();
        let year = date.getFullYear();
        let daysInMonth = new Date(year, month + 1, 0).getDate();
        let startDay = new Date(year, month, 1).getDay();
        let monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

        calendarHead.textContent = `${monthNames[month]} ${year}`;
        daysList.innerHTML = '';

        for (let i = 0; i < startDay - 1; i++) daysList.appendChild(createDayElement('inactive'));
        for (let i = 1; i <= daysInMonth; i++) daysList.appendChild(createDayElement(i === date.getDate() ? 'active' : '', i));
        for (let i = new Date(year, month, daysInMonth).getDay(); i < 7; i++) daysList.appendChild(createDayElement('inactive'));
    };

    const createDayElement = (className, text = '') => {
        let li = document.createElement('li');
        if (className) li.classList.add(className);
        li.textContent = text;
        return li;
    };

    updateCalendar();

    const toggleModal = (modal, action) => modal.classList[action]('show');

    document.querySelectorAll('[data-modal]').forEach(btn => {
        const targetModal = document.getElementById(btn.dataset.modal);
        btn.onclick = (event) => {
            event.stopPropagation();
            toggleModal(targetModal, 'add');
        };
    });

    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.onclick = () => {
            modals.forEach(modal => toggleModal(modal, 'remove'));
        };
    });

    window.onclick = (event) => {
        modals.forEach(modal => {
            if (event.target == modal) toggleModal(modal, 'remove');
        });
    };

    // Afficher les détails de la tâche
    document.querySelectorAll('.tasksName').forEach(task => {
        task.onclick = function () {
            fetch(`../mysql/get_task_details.php?task_id=${this.getAttribute('id-task-id')}`)
                .then(response => response.ok ? response.json() : Promise.reject('Erreur de réseau'))
                .then(data => {
                    if (typeof data !== 'object') throw new Error('Invalide JSON');
                    taskDetailsElement.innerHTML = `
                        <p>Nom de la tâche: ${data.nom}</p>
                        <p>Catégorie: ${data.categorie}</p>
                        <p>Date Limite de la Tache: ${data.dateTache}</p>
                    `;
                    toggleModal(taskDetailsElement.closest('.modal'), 'add');
                })
                .catch(error => console.error('Erreur:', error));
        };
    });

    // Toggle étoile
    window.toggleStarCompletion = (starIcon, taskId) => {
        toggleClass(starIcon, 'full');
        toggleClass(starIcon, 'half');
        let starValue = starIcon.classList.contains('full') ? 1 : 0;
        let doneValue = starIcon.classList.contains('full') ? 0 : 1;
        fetch('../update_star_done.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `taskId=${taskId}&star=${starValue}&done=${doneValue}`
        }).catch(error => console.error('Problème avec fetch:', error));
    };

    document.querySelector('.some-element').onclick = window.toggleMenu;

    // Gérer les groupes
    document.getElementById("openManageGroupModalBtn").onclick = () => {
        const groupID = 1;
        const groupName = "Nom du Groupe";
        const groupDescription = "Description du Groupe";
        const groupCode = "Code du Groupe";
        const manageGroupModal = document.getElementById("ManageGroupModal");
        document.getElementById("manageGroupName").value = groupName;
        document.getElementById("manageGroupDescription").value = groupDescription;
        document.getElementById("manageGroupCode").value = groupCode;
        toggleModal(manageGroupModal, 'add');
    };

    // Gestion des groupes via dropdown
    $('.dropdown-menu li a').on('click', function () {
        const groupe = $(this).data('groupe');
        $.post('main.php', { groupe, action: 'view' }, data => $('#page-content').html(data));
    });
});


</script>
</html>