<?php

require_once '../mysql/connexion_bdd.php';
require '../mysql/cookies_uid.php';

session_start();

if (!isset($_SESSION['Utilisateur_ID'])) {
    header('Location: ../pages/login.php');
}

$conn = connexion_bdd();

$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

ecriture_log("main_chat");
verif_session();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurana - Dashboard</title>
    <link rel="stylesheet" href="../css/main_profile.css">
    <link rel="stylesheet" href="../css/button.css">
    <link rel="stylesheet" href="../css/base_main.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .right {
            flex: 1;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top .user {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .top .user h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .top .user .arrow {
            margin-left: 10px;
        }
        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
            grid-column: 1 / 4;
        }
        .profile:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .profile .profile-info {
            text-align: center;
        }
        .profile .profile-info .profileHead {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .profile .profile-info .profile-image img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 3px solid #604caf;
            transition: transform 0.3s, border-color 0.3s;
        }
        .profile .profile-info .profile-image img:hover {
            transform: scale(1.05);
            border-color: #4571a0;
        }
        .profile .profile-details {
            margin-top: 20px;
        }
        .profile .profile-details h3 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }
        .profile .profile-details p {
            margin: 5px 0;
            color: #666;
        }
        .profile .profile-actions a {
            display: block;
            margin: 10px 0;
            text-decoration: none;
            color: #2575fc;
            transition: color 0.3s, background-color 0.3s;
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #2575fc;
        }
        .profile .profile-actions a:hover {
            color: #fff;
            background-color: #2575fc;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-out;
        }
        .modal .modal-content .close {
            float: right;
            font-size: 28px;
            cursor: pointer;
            color: #2575fc;
        }
        .modal .modal-content form {
            display: flex;
            flex-direction: column;
        }
        .modal .modal-content form label {
            margin-top: 10px;
        }
        .modal .modal-content form input[type="text"],
        .modal .modal-content form input[type="email"],
        .modal .modal-content form input[type="password"],
        .modal .modal-content form input[type="file"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: calc(100% - 22px);
            transition: border-color 0.3s;
        }
        .modal .modal-content form input[type="text"]:focus,
        .modal .modal-content form input[type="email"]:focus,
        .modal .modal-content form input[type="password"]:focus,
        .modal .modal-content form input[type="file"]:focus {
            border-color: #2575fc;
        }
        .modal .modal-content form input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            border: none;
            background-color: #2575fc;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .modal .modal-content form input[type="submit"]:hover {
            background-color: #6a11cb;
        }
        .modal .modal-content form input[type="checkbox"] {
            margin-top: 5px;
        }
    </style>
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
                            <a href="../pages/choisir_groupe.php">
                                <span class="material-symbols-outlined full">
                                    dashboard
                                </span>
                                <span class="title">Choisir un groupe</span>
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
                    echo "<span>" . ($_SESSION['Droit'] == 1 ? "Administrateur" : "Utilisateur") . "</span></h2>";
                ?>
                </div>
            </div>
            <main>
                <div class="profile">
                    <div class="profile-info">
                        <div class="profileHead">
                            <h2>Profil</h2>
                            <div class="profile-image">
                                <?php 
                    $sql = "SELECT Avatar FROM UTILISATEUR WHERE Utilisateur_ID = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $_SESSION['Utilisateur_ID']);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $avatar = $row['Avatar'];

                    if ($avatar) {
                        echo "<div class=\"avatarImg\">";
                        echo "<a href=\"../pages/main_profile.php\">";
                        echo "<img src=\"../uploads/avatars/$avatar\" alt=\"Avatar\">";
                        echo "</a>";
                        echo "</div>";
                    } else {
                        echo "<div class=\"avatarImg\">";
                        echo "<img src=\"../img/aurana_logo.png\" alt=\"Avatar\">";
                        echo "</div>";
                    }
                                ?>

                            </div>
                        </div>
                        <div class="profile-details">
                            <h3>Nom d'utilisateur: <?php echo $_SESSION['Pseudo']; ?></h3>
                            <p>Email: <?php echo $_SESSION['Email']; ?></p>
                            <p>Rôle: <?php echo $_SESSION['Droit'] == 1 ? "Administrateur" : "Utilisateur"; ?></p>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <a href="#" id="editProfileLink">Éditer les informations</a>
                        <a href="#" id="changePasswordLink">Changer le mot de passe</a>
                        <a href="../pages/logout.php">Se déconnecter</a>
                        <a href="../mysql/generatePDF.php">Options de confidentialité</a>
                        <a href="#" id="personnalisation">Personnalisation</a>
                    </div>
                </div>
                <!-- Edit Profile Modal -->
                <div id="editProfileModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeEditProfileModal">&times;</span>
                        <h2>Éditer les informations</h2>
                        <form id="editProfileForm" action="../mysql/edit_profile.php" method="POST" enctype="multipart/form-data">
                            <label for="username">Nom d'utilisateur:</label><br>
                            <input type="text" id="username" name="username" value="<?php echo $_SESSION['Pseudo']; ?>" required><br>
                            <label for="email">Email:</label><br>
                            <input type="email" id="email" name="email" value="<?php echo $_SESSION['Email']; ?>" required><br>
                            <label for="avatar">Avatar:</label><br>
                            <input type="file" id="avatar" name="avatar"><br>
                            <input type="submit" value="Sauvegarder les modifications">
                        </form>
                    </div>
                </div>
                <!-- Change Password Modal -->
                <div id="changePasswordModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeChangePasswordModal">&times;</span>
                        <h2>Changer le mot de passe</h2>
                        <form id="changePasswordForm" action="../mysql/change_password.php" method="POST">
                            <label for="currentPassword">Mot de passe actuel:</label><br>
                            <input type="password" id="currentPassword" name="currentPassword" required><br>
                            <label for="newPassword">Nouveau mot de passe:</label><br>
                            <input type="password" id="newPassword" name="newPassword" required><br>
                            <label for="confirmPassword">Confirmer le nouveau mot de passe:</label><br>
                            <input type="password" id="confirmPassword" name="confirmPassword" required><br>
                            <input type="submit" value="Changer le mot de passe">
                        </form>
                    </div>
                </div>
                <!-- Personalization Modal -->
                <div id="PersonnalisationModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closePersonnalisationModal">&times;</span>
                        <h2>Personnalisation</h2>
                        <form id="PersonnalisationForm" action="../mysql/set_parametres.php" method="POST">
                            <label for="darkMode">Mode sombre:</label><br>
                            <input type="checkbox" id="darkMode" name="darkMode"><br>
                            <label for="notifications">Notifications par email:</label><br>
                            <input type="checkbox" id="notifications" name="notifications"><br>
                            <input type="submit" value="Sauvegarder les paramètres">
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modals
            var editProfileModal = document.getElementById('editProfileModal');
            var changePasswordModal = document.getElementById('changePasswordModal');
            var personnalisationModal = document.getElementById('PersonnalisationModal');
            
            // Links
            var editProfileLink = document.getElementById('editProfileLink');
            var changePasswordLink = document.getElementById('changePasswordLink');
            var personnalisationLink = document.getElementById('personnalisation');
            
            // Close buttons
            var closeEditProfileModal = document.getElementById('closeEditProfileModal');
            var closeChangePasswordModal = document.getElementById('closeChangePasswordModal');
            var closePersonnalisationModal = document.getElementById('closePersonnalisationModal');
            
            // Event listeners for opening modals
            editProfileLink.onclick = function(event) {
                event.preventDefault();
                editProfileModal.style.display = "flex";
            }
            
            changePasswordLink.onclick = function(event) {
                event.preventDefault();
                changePasswordModal.style.display = "flex";
            }
            
            personnalisationLink.onclick = function(event) {
                event.preventDefault();
                personnalisationModal.style.display = "flex";
            }
            
            // Event listeners for closing modals
            closeEditProfileModal.onclick = function() {
                editProfileModal.style.display = "none";
            }
            
            closeChangePasswordModal.onclick = function() {
                changePasswordModal.style.display = "none";
            }
            
            closePersonnalisationModal.onclick = function() {
                personnalisationModal.style.display = "none";
            }
            
            // Close modals when clicking outside of them
            window.onclick = function(event) {
                if (event.target == editProfileModal) {
                    editProfileModal.style.display = "none";
                } else if (event.target == changePasswordModal) {
                    changePasswordModal.style.display = "none";
                } else if (event.target == personnalisationModal) {
                    personnalisationModal.style.display = "none";
                }
            }
        });

        setInterval(function () {
            fetch('../mysql/fetch_session.php')
        }, 5000);
    </script>
</body>
</html>
