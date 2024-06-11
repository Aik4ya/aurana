<?php

require_once 'connexion_bdd.php';

// affichage projets
function afficherProjet($conn) {
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
        }
        
    } else { // si pas de projet
        echo "<li>";
        echo "<div class=\"projectCard\">";
        echo "<p> Aucun projet </p>";
        echo "</div>";
        echo "</li>";
    }
}

  
afficherProjet(connexion_bdd());    

?>