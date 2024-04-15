<?php 

function creation_cookie_uid($uid,$expiration)
{
    setcookie('uid',$uid,$expiration,'/');
}

function lecture_cookie_uid()
{
    $uid = $_COOKIE['uid'];
    return $uid;
}

function ecriture_log($uid,$page)
{
    $entree_log = date('Y-m-d H:i:s') . " - Utilisateur $uid a visité la page $page\n";
    $nom_fichier = "log-" . date('Y-m-d') . '.txt';
    $path = "../mysql/log/" . $nom_fichier;
    
    if (file_exists($path))
    {   
        file_put_contents($path, $entree_log, FILE_APPEND | LOCK_EX);
    }

    else
    {   
        touch($path);
        file_put_contents($path, $entree_log, FILE_APPEND | LOCK_EX);
    }
}