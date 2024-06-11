<?php 

session_start();

if ($_POST['verification'] == $_SESSION["otp"])
{
    header("Location: ../pages/main.php?status=success");
    exit();
}

else 
{
    header("Location: ../pages/verif.php");
    exit();
}