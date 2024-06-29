<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include_once '../mysql/cookies_uid.php';
include_once '../mysql/connexion_bdd.php';
require_once '../mysql/verif_groupe.php';
$conn = connexion_bdd();
session_start();

$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

ecriture_log("main_chat");
verif_session();

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
    <link rel="stylesheet" href="../css/base_main.css">
    <link rel="stylesheet" href="../css/button.css">
    <script src="../js/main_chat.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
</head>
<style>
    .search-container {
    margin: 10px;
}

#searchInput {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
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
                            <a href="main.php">
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
            <main>
                <div class="projectCard">
                    <div class="projectTop">
                        <div class="user-info">
                            <?php echo "<h2>{$_SESSION['Pseudo']}<br><span>{$_SESSION['Groupe']}</span></h2>"; ?>
                            <div class="search-container">
                                <input type="text" id="searchInput" placeholder="Rechercher des messages..." onkeyup="searchMessages()">
                            </div>
                        </div>
                        <div id="chat_messages"></div>
                        <div class="buttonMess">
                            <form id="newMessageForm">
                                <textarea id="newMessageInput" name="nouveau_message" placeholder="Ajouter un nouveau message"></textarea>
                                <button type="submit">Envoyer</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="myfriends">
                    <div class="friendsHead">
                        <h2>Messages</h2>
                        <div class="friendsDots">
                            <span class="material-symbols-outlined">more_horiz</span>
                        </div>
                    </div>
                    <div class="friends">
                        <ul>
                        <?php
                            $dbh = connexion_bdd();
                            $sql = "SELECT UTILISATEUR.Pseudo, UTILISATEUR.derniere_connexion, UTILISATEUR.Utilisateur_ID 
                                    FROM UTILISATEUR
                                    JOIN est_membre ON UTILISATEUR.Utilisateur_ID = est_membre.Utilisateur_ID 
                                    WHERE est_membre.GROUPE = '{$_SESSION['Groupe_ID']}';";
                            $result = $dbh->query($sql);

                            $html = "";
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $userName = htmlspecialchars($row['Pseudo']);
                                $lastConnection = $row['derniere_connexion'];
                                $userId = $row['Utilisateur_ID'];

                                if (time() - strtotime($lastConnection) < 30){
                                    $lastConnection = "En ligne";
                                    $css_status = "Online";
                                } else {
                                    $css_status = "Offline";
                                    $currentTime = time();
                                    $lastConnectionTime = strtotime($lastConnection);
                                    $duration = $currentTime - $lastConnectionTime;
                                    $minutes = floor($duration / 60);
                                    $hours = floor($minutes / 60);
                                    $days = floor($hours / 24);

                                    if ($days > 0) {
                                        $lastConnection = "Dernière connexion : Il y a " . $days . " jours";
                                    } elseif ($hours > 0) {
                                        $lastConnection = "Dernière connexion : Il y a " . $hours . " heures";
                                    } elseif ($minutes > 0) {
                                        $lastConnection = "Dernière connexion : Il y a " . $minutes . " minutes";
                                    } else {
                                        $lastConnection = "Dernière connexion : Il y a quelques secondes";
                                    }
                                }

                                $html .= "
                                    <li>
                                        <span class=\"friendsIconName\" onclick=\"openPrivateChat($userId, '$userName')\">
                                            <span class=$css_status></span>
                                            <span class=\"friendsName\">$userName
                                                <br>
                                                $lastConnection 
                                            </span>
                                        </span>
                                    </li>
                                ";
                            }
                            echo $html;
                            ?>
                        </ul>
                    </div>
                </div>
            </main>
            <script>
                var interval = setInterval(function () {
                    fetch('../mysql/fetch_session.php')
                    }, 5000);

                    // Menu déroulant
                    function toggleMenu() {
                        var menu = document.querySelector('.menu');
                        if (menu.style.display === 'none') {
                            menu.style.display = 'block';
                        } else {
                            menu.style.display = 'none';
                        }
                    }

                    // Modal
                    var modal = document.querySelector('.modal');
                    var openCreateGroupModal = document.getElementById('openCreateGroupModal');
                    var openJoinGroupModal = document.getElementById('openJoinGroupModal');
                    var openManageGroupModalBtn = document.getElementById('openManageGroupModalBtn');
                    var close = document.querySelector('.close');

                    openCreateGroupModal.addEventListener('click', function () {
                        modal.style.display = 'block';
                    });

                    openJoinGroupModal.addEventListener('click', function () {
                        modal.style.display = 'block';
                    });

                    openManageGroupModalBtn.addEventListener('click', function () {
                        modal.style.display = 'block';
                    });

                    close.addEventListener('click', function () {
                        modal.style.display = 'none';
                    });

                    window.onclick = function (event) {
                        if (event.target == modal) {
                            modal.style.display = 'none';
                        }
                    };
                    function searchMessages() {
                        const query = document.getElementById('searchInput').value;

                        fetch(`../mysql/search_messages.php?query=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                const chatMessages = document.getElementById('chat_messages');
                                chatMessages.innerHTML = '';

                                data.forEach(message => {
                                    const messageDiv = document.createElement('div');
                                    messageDiv.classList.add('message');
                                 messageDiv.innerHTML = `<strong>${message.Pseudo}:</strong> ${message.Texte} <small>${message.Date_Envoi}</small>`;
                                    chatMessages.appendChild(messageDiv);
                                });
                            })
                            .catch(error => console.error('Error fetching messages:', error));
                    }              
          </script>
        </div>
    </div>
</body>
</html>