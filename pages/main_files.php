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
    <title>Aurana - File Upload</title>
    <link rel="stylesheet" href="../css/main_files.css">
    <link rel="stylesheet" href="../css/button.css">
    <link rel="stylesheet" href="../css/base_main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
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

        /* Drop Area */
        .drop-area {
            border: 2px dashed #ccc;
            border-radius: 20px;
            width: 100%;
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            transition: background-color 0.3s;
        }

        .drop-area.dragover {
            background-color: #e9e9e9;
        }

        .drop-area p {
            font-size: 1.2em;
            color: #666;
        }

        .file-list {
            list-style: none;
            padding: 0;
        }

        .file-list li {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .file-list a {
            text-decoration: none;
            color: #007BFF;
        }

        .file-list a:hover {
            text-decoration: underline;
        }

        .file-list button {
            margin-left: 10px;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: #dc3545;
            color: white;
            cursor: pointer;
        }

        .file-list button:hover {
            background-color: #c82333;
        }

        
    </style>
</head>
<body>
<div class="container">
        <div class="left">
            <header>
                <div class="logo">
                    <h2>aurana</h2>
                    <div class="close">
                        <span class="material-symbols-outlined">close</span>
                    </div>
                </div>
                <nav>
                    <ul>
                        <li><?php echo "<a href='main.php?groupe=" . htmlspecialchars($_GET['groupe']) . "'>" ?>
                            <span class="material-symbols-outlined full">dashboard</span>
                            <span class="title">Dashboard</span>
                        </a></li>
                        <li><?php echo "<a href='main_task.php?groupe=" . htmlspecialchars($_GET['groupe']) . "'>" ?>
                            <span class="material-symbols-outlined">check_box</span>
                            <span class="title">Tâches</span>
                        </a></li>
                        <li><?php echo "<a href='main_chat.php?groupe=" . htmlspecialchars($_GET['groupe']) . "'>" ?>
                            <span class="material-symbols-outlined">chat_bubble</span>
                            <span class="title">Messages</span>
                        </a></li>
                        <li>
                            <?php echo "<a href='main_files.php?groupe=" . $_GET['groupe'] . "'>" ?>
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

                            <?php
                            $sql = "SELECT GROUPE.Nom FROM est_membre INNER JOIN GROUPE ON est_membre.GROUPE = GROUPE.Groupe_ID WHERE est_membre.Utilisateur_ID = {$_SESSION['Utilisateur_ID']}";
                            $result = $conn->query($sql);
                            if ($result->rowCount() > 0) {
                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<li><a href='main_chat.php?groupe=" . htmlspecialchars($row['Nom']) . "'>" . htmlspecialchars($row['Nom']) . "</a></li>";
                                }
                            }
                            ?>
                            <li><a href="#" id="openCreateGroupModal">Créer un groupe</a></li>
                            <li><a href="#" id="openJoinGroupModal">Rejoindre un groupe</a></li>
                            <li><a href="#" id="openManageGroupModalBtn"
                                   onclick="openManageGroupModal(<?php echo $_SESSION['Groupe_ID']; ?>, '<?php echo htmlspecialchars($nom_groupe); ?>', 'Description du groupe', 'Code du groupe')">Gérer le groupe</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <main>
            <div class="projectCard">
                    <div class="projectTop">
                        <h2>Fichiers</h2>
                        <button id="openModalBtn"><img src="../img/plus.png" alt="Upload File"></button>
                    </div>
                    <div class="search-container">
                        <input type="text" id="searchInput" placeholder="Rechercher des fichiers..." onkeyup="searchFiles()">
                    </div>
                    <ul class="file-list" id="fileList"></ul>
                </div>

            </main>
            <script>
            </script>
        </div>
    </div>
    <!-- Modal -->
    <div id="fileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Téléverser vos fichiers</h2>
            <div class="drop-area" id="drop-area">
                <p>Glissez et déposez vos fichiers</p>
            </div>
        </div>
        <script>
        const modal = document.getElementById('fileModal');
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModal = document.getElementsByClassName('close')[0];
        const dropArea = document.getElementById('drop-area');
        const fileList = document.getElementById('fileList');
        const userId = <?php echo json_encode($_SESSION['Utilisateur_ID']); ?>;

        openModalBtn.onclick = function() {
            modal.style.display = 'block';
        }

        closeModal.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        dropArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropArea.classList.add('dragover');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('dragover');
        });

        dropArea.addEventListener('drop', (event) => {
            event.preventDefault();
            dropArea.classList.remove('dragover');

            const files = event.dataTransfer.files;
            handleFiles(files);
        });

        dropArea.addEventListener('click', () => {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.multiple = true;
            fileInput.click();
            fileInput.onchange = () => {
                const files = fileInput.files;
                handleFiles(files);
            };
        });

        function handleFiles(files) {
            for (const file of files) {
                uploadFile(file);
            }
        }

        function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);

            fetch('../mysql/upload_files.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(result => {
                console.log(result);
                alert(result.message);
                if (result.success) {
                    fetchFiles();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function fetchFiles() {
            fetch('../mysql/fetch_files.php')
            .then(response => response.json())
            .then(files => {
                fileList.innerHTML = '';
                files.forEach(file => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        ${file.Adresse} - ${file.Date_Stock}
                        <div class="file-info"> 
                            ${file.Utilisateur_id == userId ? `<button onclick="deleteFile(${file.Fichier_ID})">Delete</button>` : ''}
                            <a href="../mysql/download_file.php?file_id=${file.Fichier_ID}">Télécharger</a>
                        </div>
                    `;
                    fileList.appendChild(li);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function deleteFile(fileId) {
            if (!confirm('Voulez-vous vraiment supprimer ce fichier ?')) {
                return;
            }

            fetch('../mysql/delete_file.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ file_id: fileId }),
            })
            .then(response => response.json())
            .then(result => {
                alert(result.message);
                if (result.success) {
                    fetchFiles();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        function searchFiles() {
                    const query = document.getElementById('searchInput').value;

                    fetch(`../mysql/search_files.php?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            const fileList = document.getElementById('fileList');
                            fileList.innerHTML = '';

                            data.forEach(file => {
                                const li = document.createElement('li');
                                li.innerHTML = `
                                    ${file.Adresse} - ${file.Date_Stock}
                                    <div class="file-info"> 
                                        ${file.Utilisateur_id == userId ? `<button onclick="deleteFile(${file.Fichier_ID})">Supprimer</button>` : ''}
                                        <a href="../mysql/download_file.php?file_id=${file.Fichier_ID}">Download</a>
                                    </div>
                                `;
                                fileList.appendChild(li);
                            });
                        })
                        .catch(error => console.error('Error fetching files:', error));
                }

        // Fetch files on page load
        fetchFiles();

        setInterval(function () {
            fetch('../mysql/fetch_session.php')
        }, 5000);

    </script>
    </div>
</body>
</html>
