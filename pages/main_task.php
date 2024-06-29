<?php

require_once '../mysql/cookies_uid.php';
require_once '../mysql/connexion_bdd.php';

session_start();
$conn = connexion_bdd();

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
$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];
$conn = connexion_bdd();
ecriture_log("main_task");
verif_session();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurana - Dashboard</title>
    <link rel="stylesheet" href="../css/main_task.css">
    <link rel="stylesheet" href="../css/button.css">
    <link rel="stylesheet" href="../css/base_main.css">
    <link rel="stylesheet" href="../css/modals.css">
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
                                <?php echo "<a href='main_chat.php?groupe=" . $_GET['groupe'] . "'>" ?>
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
                <?php $nom_groupe = $_GET['groupe']; 
                    if ($nom_groupe != null): ?>
                    <h2><?php echo htmlspecialchars($nom_groupe); ?></h2>
                    <?php endif; ?>
                </div>

                <div class="user">
                    <?php
                    // Affichage de l'avatar
                    $sql = "SELECT Avatar FROM UTILISATEUR WHERE Utilisateur_ID = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $_SESSION['Utilisateur_ID']);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $avatar = $row['Avatar'];

                    if ($avatar) {
                        echo "<div class=\"avatarImg\">";
                        echo "<img src=\"../uploads/avatars/$avatar\" alt=\"Avatar\">";
                        echo "</div>";
                    } else {
                        echo "<div class=\"avatarImg\">";
                        echo "<img src=\"../img/aurana_logo.png\" alt=\"Avatar\">";
                        echo "</div>";
                    }

                    // affichage groupes + menu déroulant

                    $conn = connexion_bdd();
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
                        <span class="material-symbols-outlined">
                            expand_more
                        </span>
                    </div>
                    <div class="menu" style="display: none;">
                        <ul id="menuList">
                            <li><a href="../pages/main_profile.php">Profil</a></li>
                            <?php if ($_SESSION['droit'] = 1)
                            {
                                echo "<li><a href=\"../backoff/b_off.php\">Backoffice</a></li>";    
                            }?> 
                            <li><a href="../pages/choisir_groupe.php">Choisir son groupe</a></li>
                            <li><a href="#" id="openCreateGroupModal">Créer un groupe</a></li>
                            <li><a href="#" id="openJoinGroupModal">Rejoindre un groupe</a></li>
                            <li><a href="#" id="openManageGroupModalBtn" onclick="openManageGroupModal(<?php echo $_SESSION['Groupe_ID']; ?>, '<?php echo $nom_groupe; ?>', 'Description du groupe', 'Code du groupe')">Gérer le groupe</a></li>
                        </ul>
                    </div>
                    </div>
                </div>
        <div class="scroll-container">
            <main>
                <div class="projectCard">                  
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

                            echo "<div class=\"projectBox\">";
                            echo "<div class=\"projectTop\">";
                            echo "<h2>$nom<br><span>$groupe</span></h2>";
                            echo "<div class=\"projectDots\" onclick=\"openProjectDetailModal($id)\">";
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
                                echo "<span class= $css_line style=\"width: 0%;\"></span>"; // éviter division par 0
                            } else {
                                echo "<span class= $css_line style=\"width: " . ($tachefin / $tachetotal) * 100 . "%;\"></span>";
                            }
                            echo "</div>";
                            echo "<div class=$css_due>";
                            echo "<h2>Du pour le : $deadline</h2>";
                            echo "</div>";
                            

                            $sql="SELECT TACHE.Texte, TACHE.categorie, TACHE.done, TACHE.Date_Tache FROM tache_assignee_projet INNER JOIN TACHE ON tache_assignee_projet.id_tache = TACHE.Tache_ID WHERE tache_assignee_projet.id_projet = :id_projet"; 
                            $stmt4 = $conn->prepare($sql);
                            $stmt4->bindParam(':id_projet', $id);
                            $stmt4->execute();
                            $rowcount = $stmt4->rowCount();
                            $result = $stmt4->fetch(PDO::FETCH_ASSOC);


                            echo "<div class=\"myTasks\">";
                            echo "<div class=\"tasksHead\">";
                            echo "<h2>Tâches</h2>";

                            if ($_SESSION['groupe'] == "none"){
                                echo "<button id=\"createTaskBtn\">Créer une tâche</button>";
                            }

                            echo "</div>";
                            echo "<div class=\"tasks\">";

                            if ($rowcount > 0) { // si tache
                                while ($row = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                                    $texte = $row['Texte'];
                                    $categorie = $row['categorie'];
                                    $done = $row['done'];
                                    $date = $row['Date_Tache'];
                                
                                    echo "<div class=\"tasksBox\">";
                                    echo " Nom de la tache : $texte ";
                                    echo "<br>";
                                    echo "Categorie :  $categorie ";
                                    echo "<br>";
                                    echo "Deadline : $date ";
                                    echo "<br>";
                                    if ($done == 1) {
                                        echo "Tache finie";
                                    } else {
                                        echo "Tache en cours";
                                    }
                                    echo "</div>";
                                }

                            } else { // si pas de tache
                                echo "<li>";
                                echo "<div>";
                                echo "<p> Aucune tâche </p>";
                                echo "</div>";
                                echo "</li>";
                            }

                            echo "</div>";

                        echo "</div>";
                        echo "</div>";
                        echo "</li>";

                        }

                    } else { // si pas de projet
                        echo "<li>";
                        echo "<div class=\"projectCard\">";
                        echo "<p> Aucun projet </p>";
                        echo "</div>";
                        echo "</li>";
                    }
                ?>
                </div>
            </main>
        </div>
        </div>
        <!-- fin de droite -->
    </div>

<!-- Modal pour les détails du projet -->
<div id="ProjectDetailModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" id="closeProjectDetailModal">&times;</span>
        <h2>Détails du projet</h2>
        <div id="currentProjectMembers">
            <h3>Membres du projet</h3>
            <ul id="projectMembersList"></ul>
        </div>
        <div id="addProjectMembers" style="display: none;">
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

    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                var searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    window.location.href = `main.php?groupe=${urlParams.get('groupe')}&search=${encodeURIComponent(searchTerm)}`;
                }
            }
        });
    }

    window.clearSearch = function() {
        if (searchInput) {
            searchInput.value = '';
            window.location.href = `main.php?groupe=${urlParams.get('groupe')}`;
        }
    };

    window.toggleMenu = function() {
        var menu = document.querySelector('.menu');
        if (menu) {
            menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        }
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

    if (closeCreateModal) {
        closeCreateModal.onclick = function() {
            createTaskModal.classList.remove("show");
        }
    }

    if (closeDetailModal) {
        closeDetailModal.onclick = function() {
            taskDetailModal.classList.remove("show");
        }
    }

    window.onclick = function(event) {
        if (event.target === createTaskModal) {
            createTaskModal.classList.remove("show");
        } else if (event.target === taskDetailModal) {
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
                    showTaskDetails(taskIdArray[0]);
                }
            }
        });
    });

    function showTaskDetails(taskId) {
        fetch(`../mysql/get_task_details.php?task_id=${taskId}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error fetching task details');
                }
                const taskDetailsForm = document.getElementById("taskDetailsForm");
                if (!taskDetailsForm) {
                    console.error('Form element not found');
                    return;
                }

                const task = data.task;
                document.getElementById("taskName").value = task.Texte;
                document.getElementById("taskCategory").value = task.Categorie;
                document.getElementById("taskDate").value = task.Date_Tache;
                document.getElementById("taskDescription").value = task.Description;
                document.getElementById("taskID").value = task.Tache_ID;

                taskDetailModal.classList.add("show");
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

    if (closeCreateGroupModal) {
        closeCreateGroupModal.onclick = function() {
            createGroupModal.style.display = 'none';
        }
    }

    if (closeJoinGroupModal) {
        closeJoinGroupModal.onclick = function() {
            joinGroupModal.style.display = 'none';
        }
    }

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

    if (closeManageGroupModal) {
        closeManageGroupModal.onclick = function() {
            manageGroupModal.classList.remove("show");
        }
    }

    window.onclick = function(event) {
        if (event.target === manageGroupModal) {
            manageGroupModal.classList.remove("show");
        } else if (event.target === createGroupModal) {
            createGroupModal.style.display = 'none';
        } else if (event.target === joinGroupModal) {
            joinGroupModal.style.display = 'none';
        }
    };

    if (generateGroupCodeBtn) {
        generateGroupCodeBtn.onclick = function() {
            var newCode = generateRandomCode(6);
            document.getElementById("manageGroupCode").value = newCode;
        }
    }

    function generateRandomCode(length) {
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var code = '';
        for (var i = 0; i < length; i++) {
            code += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return code;
    }

    var openManageGroupModalBtn = document.getElementById("openManageGroupModalBtn");
    if (openManageGroupModalBtn) {
        openManageGroupModalBtn.onclick = function() {
            openManageGroupModal(<?php echo $_SESSION['Groupe_ID']; ?>);
        };
    }

    document.querySelectorAll('.tasksIcon').forEach(icon => {
        icon.addEventListener('click', function() {
            toggleTaskCompletion(this);
        });
    });

    function toggleTaskCompletion(element) {
        var taskIcon = element;
        var taskName = element.parentElement.querySelector('.tasksName');
        var taskId = taskName.getAttribute('data-task-id');
        var status = taskIcon.classList.contains('notDone') ? 1 : 0;

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
                return;
            }
            if (status) {
                taskIcon.classList.remove('notDone');
                taskIcon.classList.add('done');
                taskName.classList.remove('notDone');
                taskName.classList.add('done', 'tasksLine');
            } else {
                taskIcon.classList.remove('done');
                taskIcon.classList.add('notDone');
                taskName.classList.remove('done', 'tasksLine');
                taskName.classList.add('notDone');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    document.querySelectorAll('.tasksStar').forEach(icon => {
        icon.addEventListener('click', function() {
            toggleStarCompletion(this);
        });
    });

    function toggleStarCompletion(element) {
        var taskIcon = element;
        var taskName = element.parentElement.querySelector('.tasksName');
        var taskId = taskName.getAttribute('data-task-id');
        var status = taskIcon.classList.contains('half') ? 1 : 0;

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
                return;
            }
            if (status) {
                taskIcon.classList.remove('half');
                taskIcon.classList.add('full');
                taskName.classList.remove('half');
                taskName.classList.add('full', 'tasksLine');
            } else {
                taskIcon.classList.remove('full');
                taskIcon.classList.add('half');
                taskName.classList.remove('full', 'tasksLine');
                taskName.classList.add('half');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    var projectSelect = document.getElementById('projectSelect');
    if (projectSelect) {
        projectSelect.addEventListener('change', function() {
            var selectedProjectID = this.value;
            window.location.href = `main.php?groupe=${urlParams.get('groupe')}&projectID=${selectedProjectID}`;
        });
    }

    var projectDetailModal = document.getElementById('ProjectDetailModal');
    var closeProjectDetailModal = document.getElementById('closeProjectDetailModal');
    var saveProjectMembers = document.getElementById('saveProjectMembers');
    var searchInput = document.getElementById('searchMembersInput');
    var addProjectMembers = document.getElementById('addProjectMembers');

    if (closeProjectDetailModal) {
        closeProjectDetailModal.onclick = function() {
            projectDetailModal.style.display = 'none';
        };
    }

    window.onclick = function(event) {
        if (event.target === projectDetailModal) {
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

        if (saveProjectMembers) {
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
                        fetchProjectDetails(projectID);
                    } else {
                        alert('Erreur lors de l\'ajout des membres');
                    }
                });
            };
        }

        if (searchInput) {
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
    }

    window.promoteToAdmin = function(projectID, userID) {
        if (confirm("Voulez-vous vraiment promouvoir cet utilisateur en tant qu'administrateur ?")) {
            fetch(`../mysql/promote_to_admin.php?project_id=${projectID}&user_id=${userID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Utilisateur promu avec succès');
                        fetchProjectDetails(projectID);
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
                        fetchProjectDetails(projectID);
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