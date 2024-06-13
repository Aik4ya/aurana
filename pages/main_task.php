<?php

require_once '../mysql/cookies_uid.php';
require_once '../mysql/connexion_bdd.php';

session_start();
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
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        /* Modal */
        .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 30%;
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
    </style>
</head>
<body>
<!-- Modal -->
<div id="taskModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Tâches</h2>
    <ul id="taskList">
      <?php
      if (isset($_GET['project_id'])) {
        $projectId = $_GET['project_id'];

        $sql = "SELECT TACHE.Texte, TACHE.categorie, TACHE.done, TACHE.Date_Tache
                FROM tache_assignee_projet
                INNER JOIN TACHE ON tache_assignee_projet.id_tache = TACHE.Tache_ID
                WHERE tache_assignee_projet.id_projet = :id_projet";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_projet', $projectId);
        $stmt->execute();
        $rowcount = $stmt->rowCount();

        if ($rowcount > 0) {
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $texte = $row['Texte'];
            $categorie = $row['categorie'];
            $done = $row['done'];
            $date = $row['Date_Tache'];
            echo "<li>";
            echo "<div>";
            echo "$texte $categorie $done $date";
            echo "</div>";
            echo "</li>";
          }
        } else {
          echo "<li><div><p>Aucune tâche</p></div></li>";
        }
      }
      ?>
    </ul>
  </div>
</div>

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
                <div class="cardlist">                  
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

                            echo "<div class=\"projectCard\">";
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
                            

                            $sql="SELECT TACHE.Texte, TACHE.categorie, TACHE.done, TACHE.Date_Tache FROM tache_assignee_projet INNER JOIN TACHE ON tache_assignee_projet.id_tache = TACHE.Tache_ID WHERE tache_assignee_projet.id_projet = :id_projet"; 
                            $stmt4 = $conn->prepare($sql);
                            $stmt4->bindParam(':id_projet', $id);
                            $stmt4->execute();
                            $rowcount = $stmt4->rowCount();
                            $result = $stmt4->fetch(PDO::FETCH_ASSOC);


                            echo "<div class=\"myTasks\">";
                            echo "<div class=\"tasksHead\">";
                            echo "<h2>Mes tâches</h2>";

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
                                
                                    echo "<li>";
                                    echo "<div>";
                                    echo " $texte ";
                                    echo " $categorie ";
                                    echo " $done ";
                                    echo " $date ";
                                    echo "</div>";
                                    echo "</li>";
                                }

                            } else { // si pas de tache
                                echo "<li>";
                                echo "<div>";
                                echo "<p> Aucune tâche </p>";
                                echo "</div>";
                                echo "</li>";
                            }

                        echo "</ul>";
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
        <!-- fin de droite -->
    </div>
    <script>
    window.onload = function() {
    // Obtenir la modal
    var modal = document.getElementById("taskModal");

    // Vérifier si la modal existe
    if (modal) {
        // Obtenir les boutons qui ouvrent la modal
        var projectDots = document.getElementsByClassName("projectDots");
        for (var i = 0; i < projectDots.length; i++) {
        projectDots[i].onclick = function() {
            modal.style.display = "block";
            // Remplir la liste des tâches
            var taskList = document.getElementById("taskList");
            taskList.innerHTML = ""; // Vider la liste

            // Récupérer l'ID du projet correspondant
            var projectCard = this.closest(".projectCard");
            var projectId = projectCard.getAttribute("data-project-id");

            // Requête AJAX pour récupérer les tâches du projet
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_tasks.php?project_id=" + projectId, true);
            xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                var tasks = JSON.parse(xhr.responseText);
                for (var j = 0; j < tasks.length; j++) {
                var task = tasks[j];
                var li = document.createElement("li");
                li.innerHTML = task.texte + " " + task.categorie + " " + task.done + " " + task.date;
                taskList.appendChild(li);
                }
            }
            };
            xhr.send();
        }
        }

        // Obtenir l'élément <span> qui ferme la modal
        var span = document.getElementsByClassName("close")[0];

        // Lorsque l'utilisateur clique sur <span> (x), fermer la modal
        span.onclick = function() {
        modal.style.display = "none";
        }

        // Lorsque l'utilisateur clique n'importe où en dehors de la modal, fermer la modal
        window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        }
    } else {
        console.error("La modal n'a pas été trouvée dans le document HTML.");
    }
    }
  </script>
</body>