<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

	require_once '../mysql/cookies_uid.php';
	
  session_start();
	ecriture_log('BackOffice');
	verif_session();
	$_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];

	if ($_SESSION['Droit'] == 0) {
        header('Location: ../pages/403.html');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="fr" class="dv">

<head>
	<meta charset="UTF-8">
	<title>Aurana - BackOffice</title>
	<link rel="stylesheet" href="../css/backoff.css">
</head>

<body>
  <div class="area"></div>
  <nav class="main-menu">
    <ul>
      <li>
        <a href="#">
          <i class="fa fa-home fa-2x"></i>
          <span class="nav-text">
            Dashboard
          </span>
        </a>

      </li>
      <li class="has-subnav">
        <a href="../backoff/utilisateurs.php">
          <i class="fa fa-laptop fa-2x"></i>
          <span class="nav-text">
            Utilisateurs
          </span>
        </a>

      </li>
      <li class="has-subnav">
        <a href="#">
          <i class="fa fa-list fa-2x"></i>
          <span class="nav-text">
            Ticket
          </span>
        </a>

      </li>
      <li class="has-subnav">
        <a href="#">
          <i class="fa fa-folder-open fa-2x"></i>
          <span class="nav-text">
            Newsletter
          </span>
        </a>

      </li>
      <li>
        <a href="#">
          <i class="fa fa-bar-chart-o fa-2x"></i>
          <span class="nav-text">
            Groupes
          </span>
        </a>
      </li>
      <li>
        <a href="../backoff/logs.php">
          <i class="fa fa-font fa-2x"></i>
          <span class="nav-text">
            Logs
          </span>
        </a>
      </li>
      <li class="has-subnav">
        <a href="../backoff/qr_captcha.php">
          <i class="fa fa-folder-open fa-2x"></i>
          <span class="nav-text">
            Captcha
          </span>
        </a>

      </li>
    </ul>
  </nav>

  <h2>
    Dashboard
  </h2>
</body>
</html>
