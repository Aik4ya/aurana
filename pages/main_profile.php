<?php
require '../mysql/cookies_uid.php';

$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

ecriture_log("main_chat");
verif_session();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurana - Dashboard</title>
    <link rel="stylesheet" href="../css/main_profile.css">
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
            </div>
            <main>
                <div class="projectCard">
                    <!-- projectTop start -->
                    <div class="projectTop">
                        <div class="profile">
                            <h2>Profile</h2>
                            <div class="profile-info">
                                <div class="profile-details">
                                    <h3>Username: <?php echo $_SESSION['Pseudo']; ?></h3>
                                    <p>Email: <?php echo $_SESSION['Email']; ?></p>
                                    <p>Role: <?php echo $_SESSION['Droit']; ?></p>
                                </div>
                            </div>
                            <div class="profile-actions">
                                <a href="edit_profile.php">Edit Profile</a>
                                <br>
                                <a href="#" id="changePasswordLink">Change Password</a>
                                <br>
                                <a href="../mysql/logout.php">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="changePasswordModal" style="display: none;">
                    <div>
                        <h2>Change Password</h2>
                        <form id="changePasswordForm" action="../mysql/change_password.php" method="POST">
                            <label for="currentPassword">Current Password:</label><br>
                            <input type="password" id="currentPassword" name="currentPassword"><br>
                            <label for="newPassword">New Password:</label><br>
                            <input type="password" id="newPassword" name="newPassword"><br>
                            <label for="confirmPassword">Confirm New Password:</label><br>
                            <input type="password" id="confirmPassword" name="confirmPassword"><br>
                            <input type="submit" value="Change Password">
                        </form>
                    </div>
                </div>
            </main>
            <script>
                // Get the modal
                var modal = document.getElementById('changePasswordModal');

                // Get the link that opens the modal
                var link = document.getElementById('changePasswordLink');

                // When the user clicks on the link, open the modal 
                link.onclick = function(event) {
                    event.preventDefault();
                    modal.style.display = "block";
                }

                // Get the form
                var form = document.getElementById('changePasswordForm');

                // When the user submits the form, prevent form submission and hide the modal
                form.onsubmit = function(event) {
                    event.preventDefault();
                    // Here you can add your code to change the password
                    modal.style.display = "none";
                }

                // When the user clicks outside the modal, close it
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
        </script>
</body>

</html>