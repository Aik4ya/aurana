<?php

Function connexion_bdd() #connexion à la bdd
{
    $bdd_login = 'root';
    $bdd_password = '';
    
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=test', $bdd_login, $bdd_password);
        #Changer nom/adresse/login/mdp avec ce qui correspond pour aurana

    } catch (PDOException $e) {
        var_dump($e);
        return null;
    }

    echo "Connexion réussie";
    Return $dbh ;
}

Function creation_utilisateur($dbh) #création d'un utilisateur
{
    #requête sql pour aller chercher les paramètres par défaut du profil définit dans le back

    $sql = $dbh->prepare('SELECT NL_Abonnement_Defaut, Dark_Mode_Defaut, Notification_Mail_Defaut FROM PARAMETRES_BACK WHERE PBack_ID = :PBackSelected');
    $sql->bindParam(':PBackSelected', $PBackSelected);

    $PBackSelected = 1; #requête post dans le back office

    $sql->execute();
    $resultat = $sql->fetch(PDO::FETCH_ASSOC);

    #requête sql pour créer l'utilisateur

    $sql = $dbh->prepare('INSERT INTO UTILISATEUR (Username, Login, Password, Email, Droits, NL_Abonnement, Dark_Mode, Langage, Notification_Mail, Agenda_ID, Note_ID, Fichier_ID, Message_ID) VALUES
    (   
        :Username,
        :Login,
        :Password,
        :Email,
        :Droits,
        :NL_Abonnement,
        :Dark_Mode,
        :Langage, 
        :Notification_Mail, 
        NULL, 
        NULL, 
        NULL, 
        NULL
    );');
    
    $sql->bindParam(':Username', $Username);
    $sql->bindParam(':Login', $Login);
    $sql->bindParam(':Password', $Password);
    $sql->bindParam(':Email', $Email);
    $sql->bindParam(':Droits', $Droits);
    $sql->bindParam(':NL_Abonnement', $NL_Abonnement);
    $sql->bindParam(':Dark_Mode', $Dark_Mode);
    $sql->bindParam(':Langage', $Langage);
    $sql->bindParam(':Notification_Mail', $Notification_Mail);
    
    $Username = 'Admin'; #requête post à la connexion
    $Login = 'Admin'; #requête post à la connexion
    $Password = 'Admin'; #requête post à la connexion
    $Email = 'Admin'; #requête post à la connexion
    $Langage = 'Français'; #requête post à la connexion

    $NL_Abonnement = $resultat['NL_Abonnement_Defaut'];
    $Dark_Mode = $resultat['Dark_Mode_Defaut'];
    $Notification_Mail = $resultat['Notification_Mail_Defaut'];


    $sql->execute();
    $Utilisateur_ID = $dbh->lastInsertID();
    
    var_dump($Utilisateur_ID);
}

Function creation_group($dbh) #création d'un groupe
{

    #requête sql de création de groupe

    $sql = $dbh->prepare('INSERT INTO GROUPE (Nom, Description, Theme, Autorisation_Fichier, Background, Agenda_ID) VALUES
    (   
        :Nom,
        :Description,
        :Theme,
        :Autorisation_Fichier,
        :Background,
        NULL
    )');
    
    $sql->bindParam(':Nom', $Nom);
    $sql->bindParam(':Description', $Description);
    $sql->bindParam(':Theme', $Theme);
    $sql->bindParam(':Autorisation_Fichier', $Autorisation_Fichier);
    $sql->bindParam(':Background', $Background);
    
    $Nom = 'Admin'; #requête post à la création du groupe
    $Description = 'Admin'; #requête post à la création du groupe
    $Theme = 'Admin'; #requête post à la création du groupe
    $Autorisation_Fichier = TRUE; #requête post à la création du groupe
    $Background = 'img/image.png'; #laisse pour le moment

    $sql->execute();
    $resultat = $sql->fetchAll();
    
    $Group_ID = $dbh->lastInsertID();
}

Function creation_parametres_defaut($dbh) #création des présets de paramètres du site (back office) IGNORER
{

    #requête sql pour créer un préset

    $sql = $dbh->prepare('INSERT INTO PARAMETRES_BACK (Taille_MDP_Min, NL_Abonnement_Defaut, Dark_Mode_Defaut, Notification_Mail_Defaut) VALUES
    (   
        :Taille_MDP_Min,
        :NL_Abonnement_Defaut,
        :Dark_Mode_Defaut,
        :Notification_Mail_Defaut
    );');
    
    $sql->bindParam(':Taille_MDP_Min', $Taille_MDP_Min);
    $sql->bindParam(':NL_Abonnement_Defaut', $NL_Abonnement_Defaut);
    $sql->bindParam(':Dark_Mode_Defaut', $Dark_Mode_Defaut);
    $sql->bindParam(':Notification_Mail_Defaut', $Notification_Mail_Defaut);
    
    $Taille_MDP_Min = 8; #requête post dans le back office
    $NL_Abonnement_Defaut = FALSE; #requête post dans le back office
    $Dark_Mode_Defaut = FALSE; #requête post dans le back office
    $Notification_Mail_Defaut = FALSE; #requête post dans le back office

    $sql->execute();
}

Function connexion_utilisateur($dbh) #vérification du combo login/password bien présent dans la bdd
{
    $sql = $dbh->prepare('SELECT Utilisateur_ID FROM UTILISATEUR WHERE Login = :Login AND Password = :Password;');
    $sql->bindParam(':Login', $Login);
    $sql->bindParam(':Password', $Password);

    $Login = 'Admimn';
    $Password = 'Admin';
    
    $sql->execute();
    $resultat = $sql->fetch(PDO::FETCH_ASSOC);

    if ($resultat == FALSE)
    {
        #afficher message d'erreur
    }

    else
    {
        #autoriser la connexion
    }
}

Function application_back($dbh) #modification du paramètre back
{
    $sql = $dbh->prepare('UPDATE Parametres_Back SET

        Taille_MDP_Min = :Taille_MDP_Min,
        NL_Abonnement_Defaut = :NL_Abonnement_Defaut,
        Dark_Mode_Defaut = :Dark_Mode_Defaut,
        Notification_Mail_Defaut = :Notification_Mail_Defaut

        WHERE Parametre_Back_ID = 1
    ');

    $sql->bindParam(':Taille_MDP_Min',$Taille_MDP_Min);
    $sql->bindParam(':NL_Abonnement_Defaut',$NL_Abonnement_Defaut);
    $sql->bindParam(':Dark_Mode_Defaut',$Dark_Mode_Defaut);
    $sql->bindParam(':Notification_Mail_Defaut',$Notification_Mail_Defaut);

    $Taille_MDP_Min = 1; #requête post dans le back office
    $NL_Abonnement_Defaut = 1; #requête post dans le back office
    $Dark_Mode_Defaut = 1; #requête post dans le back office
    $Notification_Mail_Defaut = 1; #requête post dans le back office

    $sql->execute();
}

Function application_profil($dbh) #modification du profil utilisateur
{
    $sql = $dbh->prepare('UPDATE UTILISATEUR SET

        pseudo = :pseudo,
        Identifiant = :Identifiant,
        Mot_de_passe = :Mot_de_passe,
        Email = :Email,
        Abonnement_NL = :Abonnement_NL,
        Dark_Mode = :Dark_Mode,
        Notification_Mail = :Notification_Mail

        WHERE Utilisateur_ID = :Utilisateur_ID
    ');

    $sql->bindParam(':pseudo',$pseudo);
    $sql->bindParam(':Identifiant',$Identifiant);
    $sql->bindParam(':Mot_de_passe',$Mot_de_passe);
    $sql->bindParam(':Email',$Email);
    $sql->bindParam(':Abonnement_NL',$Abonnement_NL);
    $sql->bindParam('Dark_Mode',$Dark_Mode);
    $sql->bindParam('Notification_Mail',$Notification_Mail);
    $sql->bindParam('Utilisateur_ID',$Utilisateur_ID);

    $pseudo = 1; #requête post dans le profil
    $Identifiant = 1; #requête post dans le profil
    $Mot_de_passe = 1; #requête post dans le profil
    $Email = 1; #requête post dans le profil
    $Abonnement_NL = 1;
    $Dark_Mode = 1; #requête post dans le profil
    $Notification_Mail = 1; #requête post dans le profil
    $Utilisateur_ID = 1; #trouver un moyen de récup l'ID

    $sql->execute();
}



#creation_parametres_defaut(connexion_bdd());
#creation_utilisateur(connexion_bdd());
#creation_group(connexion_bdd());
#connexion_utilisateur(connexion_bdd());
#application_back(connexion_bdd());
#application_profil(connexion_bdd());