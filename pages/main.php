<?php
require_once '../mysql/cookies_uid.php';
require_once '../mysql/connexion_bdd.php';

session_start();
$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

ecriture_log('main');
verif_session();


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
                            <a href="#">
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
            <div class="disconnect">
                <div class="decoBtn">
                    <button>Deconnexion</button>
                </div>
            </div>
        </div>
        <!-- left end -->
        <!-- right start -->
        <div class="right">
            <!-- top start -->
            <div class="top">
                <!-- searchBx start -->
                <div class="searchBx">
                    <h2>Nom du Groupe</h2>
                    <!-- <div class="inputBx">
                        <input type="text" placeholder="Search...">
                        <span class="material-symbols-outlined searchClose">
                            close
                        </span>
                    </div> -->
                </div>
                <!-- searchBx end -->
                <!-- user start -->
                <div class="user">
                    <?php
                    session_start();
                    $conn = connexion_bdd();
                    // Afficher le pseudo et le droit de l'utilisateur
                    echo "<h2>" . $_SESSION['Pseudo'] . "<br>";

                    if ($_SESSION['Droit'] == 0) {
                        echo "<span>User</span></h2>";
                    } elseif ($_SESSION['Droit'] == 1) {
                        echo "<span>Admin</span></h2>";
                    }

                    $user_id = $_SESSION['Utilisateur_ID'];
                    $sql = "SELECT GROUPE.Nom FROM est_membre INNER JOIN GROUPE ON est_membre.GROUPE = GROUPE.Groupe_ID WHERE est_membre.Utilisateur_ID = $user_id";
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
                        // Si l'utilisateur n'est associé à aucun groupe, afficher "No Group"
                        echo "<p>Aucun Groupe</p>";
                    }
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
                        <h2>Nom Projet<br><span>Nom Entreprise</span></h2>
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
                            <h2>En Cours</h2>
                        </div>
                        <div class="priority">
                            <h2>Priorité haute</h2>
                        </div>
                    </div>
                    <!-- projectProgress end -->
                    <!-- task start -->
                    <div class="task">
                        <h2>Tâches faite: <bold>35</bold> / 50</h2>
                        <span class="line"></span>
                    </div>
                    <!-- task end -->
                    <!-- due start -->
                    <div class="due">
                        <h2>Date de rendu: 25 Août</h2>
                    </div>
                    <!-- due end -->
                </div>
                <!-- projectCard end -->
                <!-- projectCard2 start -->
                <div class="projectCard projectCard2">
                    <div class="projectTop">
                        <h2>Nom Projet<br><span>Nom Entreprise</span></h2>
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
                            <h2>Priorité Haute</h2>
                        </div>
                    </div>
                    <!-- <div class="groupImg">
                        <a href="#">
                            <img src="./groupImg/img1.jpg" alt="img1">
                        </a>
                        <a href="#" style="--left: -10px;">
                            <img src="./groupImg/img2.jpg" alt="img2">
                        </a>
                        <a href="#" style="--left: -20px;">
                            <img src="./groupImg/img3.jpg" alt="img3">
                        </a>
                        <a href="#" style="--left: -30px;">
                            <img src="./groupImg/img4.jpg" alt="img4">
                        </a>
                        <a href="#" style="--left: -40px;">
                            <img src="./groupImg/img5.jpg" alt="img5">
                        </a>
                        <a href="#" style="--left: -50px;">
                            <span class="number">+3</span>
                        </a>
                    </div> -->
                    <div class="task">
                        <h2>Tâches faites: <bold>35</bold> / 50</h2>
                        <span class="line"></span>
                    </div>
                    <div class="due">
                        <h2>Date de rendu: 25 Août</h2>
                    </div>
                </div>
                <!-- projectCard2 end -->
                    <!-- myTasks start -->
                    <div class="myTasks">
                        <!-- tasksHead start -->
                        <div class="tasksHead">
                            <h2>Mes tâches</h2>
                            <div class="tasksDots" onclick="toggleCreateTaskMenu()">
                                <span class="material-symbols-outlined">
                                    more_horiz
                                </span>
                                <!-- Menu "Créer une Tâche" -->
                                <div id="createTaskMenu" class="createTaskMenu">
                                    <ul>
                                    <button id="createTaskBtn">Créer une tâche</button>
                                    </ul>
                                </div>
                                <!--<div id="taskModal" class="modal">
                                <h2>Créer une nouvelle tâche</h2>
                                <form id="taskForm">
                                    <label for="taskName">Nom de la tâche:</label><br>
                                    <input type="text" id="taskName" name="taskName" required><br><br>

                                    <label for="assignee">Assignée à:</label><br>
                                    <input type="text" id="assignee" name="assignee"><br><br>

                                    <label for="priority">Priorité:</label><br>
                                    <select id="priority" name="priority">
                                        <option value="low">Basse</option>
                                        <option value="medium">Moyenne</option>
                                        <option value="high">Haute</option>
                                    </select><br><br>

                                    <label for="dueDate">Date de fin:</label><br>
                                    <input type="date" id="dueDate" name="dueDate"><br><br>

                                    <input type="submit" value="Créer">
                                </form>
                            </div>-->

                            </div>
                        </div>
                        <!-- tasksHead end -->
                        <!-- tasks start -->
                        <div class="tasks">
                            <ul>
                                <?php
                               // Recuperer les taches assigner a l'utilisateur -->
                               $stmt_tasks = $conn->prepare("SELECT TACHE.Tache_ID, TACHE.Texte 
                                                             FROM es_assigner 
                                                             INNER JOIN TACHE ON es_assigner.Tache_ID = TACHE.Tache_ID
                                                             WHERE es_assigner.Utilisateur_ID = :user_id");
                               $stmt_tasks->bindParam(':user_id', $user_id);
                               $stmt_tasks->execute();
                               
                               // - Faire une boucle pour chaque resultat -->
                               while ($row_tasks = $stmt_tasks->fetch(PDO::FETCH_ASSOC)) {
                                   $task_id = $row_tasks['Tache_ID'];
                                   $task_text = $row_tasks['Texte'];
                               
                                  // Afficher la tâche -->
                                   echo "<li>";
                                   echo "<span class=\"tasksIconName\">";
                                   echo "<span class=\"tasksIcon notDone\" onclick=\"TaskIcon(this)\">";
                                   echo "<span class=\"material-symbols-outlined\"></span>";
                                   echo "</span>";
                                   echo "<span class=\"tasksName\">" . $task_text . "</span>";
                                   echo "</span>";
                                   echo "<span class=\"tasksStar half\" onclick=\"toggleStarCompletion(this)\">";
                                   echo "<span class=\"material-symbols-outlined\">star</span>";
                                   echo "</span>";
                                   echo "</li>";
                               }
                                ?>
                            </ul>
                        </div>
                        <!-- tasks end -->
                    </div>
                    <!-- myTasks end -->

                <!-- myTasks end -->
                <!-- calendar start -->
                <div class="calendar">
                    <!-- calendarHead start -->
                    <div class="calendarHead">
                        <h2>Octobre 2022</h2>
                        <div class="calendarIcon">
                            <span class="material-symbols-outlined">
                                chevron_left
                            </span>
                            <span class="material-symbols-outlined">
                                chevron_right
                            </span>
                        </div>
                    </div>
                    <!-- calendarHead end -->
                    <!-- calendarData start -->
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
                            <li class="inactive">25</li>
                            <li class="inactive">26</li>
                            <li class="inactive">27</li>
                            <li class="inactive">28</li>
                            <li class="inactive">29</li>
                            <li class="inactive">30</li>
                            <li>1</li>
                            <li>2</li>
                            <li>3</li>
                            <li>4</li>
                            <li>5</li>
                            <li>6</li>
                            <li>7</li>
                            <li>8</li>
                            <li>9</li>
                            <li>10</li>
                            <li>11</li>
                            <li>12</li>
                            <li>13</li>
                            <li>14</li>
                            <li>15</li>
                            <li>16</li>
                            <li>17</li>
                            <li>18</li>
                            <li>19</li>
                            <li>20</li>
                            <li class="active">21</li>
                            <li>22</li>
                            <li>23</li>
                            <li>24</li>
                            <li>25</li>
                            <li>26</li>
                            <li>27</li>
                            <li>28</li>
                            <li>29</li>
                            <li>30</li>
                            <li>31</li>
                            <li class="inactive">1</li>
                            <li class="inactive">2</li>
                            <li class="inactive">3</li>
                            <li class="inactive">4</li>
                            <li class="inactive">5</li>
                        </ul>
                    </div>
                    <!-- calendarData end -->
                </div>
                <!-- calendar end -->
                <!-- messages start -->
                <div class="messages">
                    <!-- messagesHead start -->
                    <div class="messagesHead">
                        <h2>Messages</h2>
                    </div>
                    <!-- messagesHead end -->
                    <!-- messagesUser start -->
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img1.jpg" alt="img1">
                        </div>
                        <h2>Marvin McKinney<br><span>Commodo volutpot noc</span></h2>
                    </div>
                    <!-- messagesUser end -->
                    <!-- messagesUser start -->
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img2.jpg" alt="img2">
                        </div>
                        <h2>Wade Warren<br><span>Commodo volutpot noc</span></h2>
                    </div>
                    <!-- messagesUser end -->
                    <!-- messagesUser start -->
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img3.jpg" alt="img3">
                        </div>
                        <h2>John Cooper<br><span>Commodo volutpot noc</span></h2>
                    </div>
                    <!-- messagesUser end -->
                    <!-- messagesUser start -->
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img4.jpg" alt="img4">
                        </div>
                        <h2>Darlene Robertson<br><span>Commodo volutpot noc</span></h2>
                    </div>
                    <!-- messagesUser end -->
                    <!-- messagesUser start -->
                    <div class="messagesUser">
                        <div class="messagesUserImg">
                            <img src="./groupImg/img5.jpg" alt="img5">
                        </div>
                        <h2>Kristin Watson<br><span>Commodo volutpot noc</span></h2>
                    </div>
                    <!-- messagesUser end -->
                </div>
                <!-- messages end -->
            </main>
            <!-- main end -->
        </div>
        <!-- right end -->
    </div>
    <!-- container end -->

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const taskModal = document.getElementById('taskModal');
        const taskForm = document.getElementById('taskForm');

        taskForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Empêche la soumission du formulaire par défaut

            // Récupérer les valeurs saisies par l'utilisateur
            const taskName = document.getElementById('taskName').value;
            const assignee = document.getElementById('assignee').value;
            const priority = document.getElementById('priority').value;
            const dueDate = document.getElementById('dueDate').value;

            // Vous pouvez ajouter ici le code pour enregistrer les données du formulaire

            // Fermer la fenêtre modale
            taskModal.style.display = 'none';
        });
    });
</script>

</body>

</html>