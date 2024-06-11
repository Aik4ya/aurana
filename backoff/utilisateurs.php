<?php
	require_once '../mysql/cookies_uid.php';
	require_once '../mysql/connexion_bdd.php';
	
	session_start();
	$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

	verif_session();
	
	if ($_SESSION['Droit'] == 0) {
        header('Location: ../pages/403.html');
        exit();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateurs</title>
    <link rel="stylesheet" href="../css/backoff_user.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap");
    </style>
</head>
<body>
    <div id="users">
        <h2>Utilisateurs</h2>
        <div>
            <h3>Créer un Utilisateur</h3>
            <form method="post" action="../mysql/traitement_signup_backoff.php">
                <input type="text" name="Pseudo" placeholder="Pseudo">
                <input type="text" name="Identifiant" placeholder="Identifiant">
                <input type="password" name="Mot_de_passe" placeholder="Mot de passe">
                <input type="email" name="Email" placeholder="Email">
                <input type="checkbox" name="Droit" value="1"> Administrateur
                <button type="submit">Soumettre</button>
            </form>
        </div>

        <div>
            <h3>Liste des Utilisateurs</h3>
            <div id="userListContainer">
                <?php
                $dbh = connexion_bdd();
                echo "<h4>Utilisateurs</h4>";
                $sql = $dbh->prepare("SELECT * FROM UTILISATEUR WHERE Désactivé = 0;");
                $sql->execute();
                $result = $sql->fetchAll();

                if ($result) {
                    echo "<table border='1'>";
                    echo "<tr>";
                    echo "<th>ID</th>";
                    echo "<th>Pseudo</th>";
                    echo "<th>Identifiant</th>";
                    echo "<th>Droit</th>";
                    echo "<th>Email</th>";
                    echo "<th>Dernière Connexion</th>";
                    echo "<th>Date Inscription</th>";
                    echo "<th>Action</th>";
                    echo "<th>Suspendre</th>";
                    echo "</tr>";               
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . $row["Utilisateur_ID"] . "</td>";
                        echo "<td id='username-" . $row["Utilisateur_ID"] . "'>" . $row["Pseudo"] . "</td>";
                        echo "<td>" . $row["Identifiant"] . "</td>";
                        echo "<td id='droit-" . $row["Utilisateur_ID"] . "'>" . $row["Droit"] . "</td>";
                        echo "<td id='email-" . $row["Utilisateur_ID"] . "'>" . $row["Email"] . "</td>";
                        echo "<td>" . $row["derniere_connexion"] . "</td>";
                        echo "<td>" . $row["date_inscription"] . "</td>";
                        echo "<td><button onclick='editUser(" . $row["Utilisateur_ID"] . ")'>Modifier</button></td>";
                        echo "<td><button onclick='suspendUser(" . $row["Utilisateur_ID"] . ")'>Suspendre</button></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Aucun utilisateur trouvé";
                }
                ?>
            </div>
        </div>

        <div id="suspendedUsers">
            <h3>Comptes Suspendus</h3>
            <div id="suspendedUserListContainer">
                <?php
                echo "<h4>Utilisateurs Suspendus</h4>";
                $sql_suspended = $dbh->prepare("SELECT * FROM UTILISATEUR WHERE Désactivé = 1;");
                $sql_suspended->execute();
                $result_suspended = $sql_suspended->fetchAll();

                if ($result_suspended) {
                    echo "<table border='1'>";
                    echo "<tr>";
                    echo "<th>ID</th>";
                    echo "<th>Pseudo</th>";
                    echo "<th>Identifiant</th>";
                    echo "<th>Droit</th>";
                    echo "<th>Email</th>";
                    echo "<th>Dernière Connexion</th>";
                    echo "<th>Date Inscription</th>";
                    echo "<th>Action</th>";
                    echo "</tr>";               
                    foreach ($result_suspended as $row) {
                        echo "<tr>";
                        echo "<td>" . $row["Utilisateur_ID"] . "</td>";
                        echo "<td>" . $row["Pseudo"] . "</td>";
                        echo "<td>" . $row["Identifiant"] . "</td>";
                        echo "<td>" . $row["Droit"] . "</td>";
                        echo "<td>" . $row["Email"] . "</td>";
                        echo "<td>" . $row["derniere_connexion"] . "</td>";
                        echo "<td>" . $row["date_inscription"] . "</td>";
                        echo "<td><button onclick='reactivateUser(" . $row["Utilisateur_ID"] . ")'>Réactiver</button></td>"; // Bouton pour réactiver l'utilisateur
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Aucun utilisateur suspendu";
                }
                ?>
            </div>
        </div>
    </div>

    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditUserModal()">&times;</span>
            <h3>Modifier Utilisateur</h3>
            <form id="editUserForm">
                <input type="hidden" id="userId" name="userId">
                <label for="editUsername">Pseudo:</label>
                <?php
                echo "<td><button onclick='resetPassword(" . $row["Utilisateur_ID"] . ")'>Réinitialiser mot de passe</button></td>";
                ?>
                <br>
                <input type="text" id="editUsername" name="editUsername" required>
                <br>
                <label for="editEmail">Email:</label>
                <br>
                <input type="email" id="editEmail" name="editEmail" required>
                <?php
                echo "<td><button onclick='deleteUser(" . $row["Utilisateur_ID"] . ")'>Supprimer</button></td>";
                ?>
                <br>
                <label for="editDroit">Droit:</label>
                <br>
                <input type="text" id="editDroit" name="editDroit" required>
                <br>
                <input type="submit" value="Valider" onclick="updateUser()">
            </form>
        </div>
    </div>

    <script>
        function editUser(userId) {
            var username = document.getElementById("username-" + userId).innerText;
            var email = document.getElementById("email-" + userId).innerText;
            var droit = document.getElementById("droit-" + userId).innerText;

            document.getElementById("userId").value = userId;
            document.getElementById("editUsername").value = username;
            document.getElementById("editEmail").value = email;
            document.getElementById("editDroit").value = droit;

            document.getElementById("editUserModal").style.display = "block";
        }

        function closeEditUserModal() {
            document.getElementById("editUserModal").style.display = "none";
        }

        function updateUser() {
            var userId = document.getElementById("userId").value;
            var username = document.getElementById("editUsername").value;
            var email = document.getElementById("editEmail").value;
            var droit = document.getElementById("editDroit").value;

            fetch('../mysql/update_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    userId: userId,
                    username: username,
                    email: email,
                    droit: droit
                })
            })
            .then(response => {
                if (response.ok) {
                    alert('Utilisateur mis à jour avec succès');
                    closeEditUserModal();
                } else {
                    throw new Error('Erreur lors de la mise à jour de l\'utilisateur');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour de l\'utilisateur');
            });
        }

        function resetPassword(userId) {
            if (confirm("Voulez-vous vraiment réinitialiser le mot de passe pour cet utilisateur?")) {
                fetch('../mysql/reset_password.php?userId=' + userId, { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userId: userId
                    })
                })
                .then(response => {
                    if (response.ok) {
                        alert('Mot de passe réinitialisé avec succès');
                        location.reload();
                    } else {
                        throw new Error('Erreur lors de la réinitialisation du mot de passe');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la réinitialisation du mot de passe');
                });
            }
        }

        function suspendUser(userId) {
            if (confirm("Voulez-vous vraiment suspendre cet utilisateur?")) {
                fetch(`../mysql/suspend_user.php?userId=${userId}`, { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: null
                })
                .then(response => {
                    if (response.ok) {
                        alert('Utilisateur suspendu avec succès');
                        location.reload();
                    } else {
                        throw new Error('Erreur lors de la suspension de l\'utilisateur');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suspension de l\'utilisateur');
                });
            }
        }

        function reactivateUser(userId) {
            if (confirm("Voulez-vous vraiment réactiver cet utilisateur?")) {
                fetch(`../mysql/reactivate_user.php?userId=${userId}`, { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userId: userId
                    })
                })
                .then(response => {
                    if (response.ok) {
                        alert('Utilisateur réactivé avec succès');
                        location.reload();
                    } else {
                        throw new Error('Erreur lors de la réactivation de l\'utilisateur');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la réactivation de l\'utilisateur');
                });
            }
        }

        function deleteUser(userId) {
            if (confirm("Voulez-vous vraiment supprimer cet utilisateur?")) {
                fetch('../mysql/delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userId: userId
                    })
                })
                .then(response => {
                    if (response.ok) {
                        alert('Utilisateur supprimé avec succès');
                        location.reload(); 
                    } else {
                        throw new Error('Erreur lors de la suppression de l\'utilisateur');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression de l\'utilisateur');
                });
            }
        }
    </script>
</body>
</html>

