<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <title>Aurana - Index</title>
    <!-- Icon  -->
    <link rel="icon" href="img/aurana_logo.png" type="image/x-icon" />
    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap"
      rel="stylesheet"
    />
    <!-- bootstrap cdn -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!--Lien CSS-->
    <link rel="stylesheet" href="css/style.css" />
  </head>

  <body>
    <style>
      
      
    </style>
    <!-- La bulle -->
    <div class="bulle" id="bulle">
      <i class="fa fa-plus"></i>
    </div>

    <!-- Le formulaire -->
    <div id="ticket-form" style="display: none">
      <h2>Créer un nouveau ticket</h2>
      <form action="mysql/submit_ticket.php" method="post">
        <label for="titre">Titre du Ticket:</label><br />
        <input type="text" id="titre" name="titre" required /><br /><br />

        <label for="description">Description:</label><br />
        <textarea
          id="description"
          name="description"
          rows="4"
          required
        ></textarea
        ><br /><br />
        
        <label for="categorie">Catégorie:</label><br />
        <select id="categorie" name="categorie" required>
          <option value="connexion">Connexion</option>
          <option value="commercial">Commercial</option>
          <option value="technique">Technique</option>
          <option value="autre">Autre</option>
        </select><br /><br />

        <label for="priorite">Priorité:</label><br />
        <select id="priorite" name="priorite" required>
          <option value="Urgent">Urgent</option>
          <option value="Pas Urgent">Pas Urgent</option></select
        ><br /><br />

        <input type="submit" value="Soumettre" />
      </form>
    </div>

    <!-- Header -->
    <header id="header" class="fixed-top">
      <div class="container d-flex align-items-center justify-content-between">
        <a href="index.html" class="logo"
          ><img src="img/aurana_logo.png" alt="" class="img-fluid" /> aurana</a
        >
        <!--Nav Bar-->
        <nav id="navbar" class="navbar">
          <a class="getstarted scrollto" href="pages/login.php">CONNEXION</a>
          <i class="bi bi-list mobile-nav-toggle"></i>
        </nav>
      </div>
    </header>

    <!--Hero Section-->
    <section id="hero" class="d-flex align-items-center">
      <div class="waveWrapper waveAnimation">
        <div class="waveWrapperInner bgTop">
          <div
            class="wave waveTop"
            style="background-image: url(img/wave-bot.png)"
          ></div>
        </div>
        <div class="waveWrapperInner bgMiddle">
          <div
            class="wave waveMiddle"
            style="background-image: url(img/wave-bot.png)"
          ></div>
        </div>
        <div class="waveWrapperInner bgBottom">
          <div
            class="wave waveBottom"
            style="background-image: url(img/wave-bot.png)"
          ></div>
        </div>
      </div>
      <div class="container-fluid" data-aos="fade-up">
        <div class="row justify-content-center">
          <div
            class="col-xl-5 col-lg-6 pt-3 pt-lg-0 order-2 order-lg-1 d-flex flex-column justify-content-center"
          >
            <h1>Aurana</h1>
            <h2>
              Aurana est conçue pour transformer la manière dont vous gérez vos
              tâches quotidiennes, en vous offrant une solution complète et
              engageante. Que vous soyez étudiant, professionnel, enseignant, ou
              simplement cherchant à améliorer votre productivité, Aurana est là
              pour vous.
            </h2>
            <div>
              <a href="pages/login.php" class="btn-get-started scrollto">COMMENCER</a>
            </div>
          </div>
          <div
            class="col-xl-4 col-lg-6 order-1 order-lg-2 hero-img"
            data-aos="zoom-in"
            data-aos-delay="150"
          >
            <img src="img/hero-img.png" class="img-fluid animated" />
          </div>
        </div>
      </div>
    </section>

    <!--Seconde Section /// Caroussel -->
    <section id="secondsection">
      <h1>Les fonctionnalités</h1>
      <div class="s-box">
        <div id="slide">
          <div class="card-s">
            <div class="profile">
              <h3>Gestionnaire de projet</h3>
            </div>
            <p>
              Aurana est votre gestionnaire de projet ultime, conçu pour
              optimiser la planification, la coordination et la réalisation de
              vos projets. Que vous cherchiez à organiser des tâches, à gérer
              des ressources, ou à assurer la qualité, Aurana vous offre une
              solution complète. Dirigez la planification, définissez les
              objectifs, et suivez les progrès de vos projets avec facilité.
            </p>
          </div>
          <div class="sidebar"></div>
        </div>
        <div class="arrow-right"></div> <!-- Flèche à droite -->
      </div>
      </div>
    </section>
    

    <!--Troisième Section-->
    <section id="troisimesection">
      <div class="container">
        <div class="categories">
          <div class="category selected">Particulier</div>
          <div class="category">Individuel</div>
          <div class="category">Entreprise</div>
        </div>

        <div
          id="particulier"
          class="category-description"
          style="display: block"
        >
          <div>
            <h2>Particulier</h2>
            <p class="lead">Description de la catégorie particulier.</p>
          </div>
          <div class="test">
            <div class="features">
              <ul class="feature-list">
                <!-- Utilisation d'une liste non ordonnée -->
                <li>
                  <span class="feature" style="background-color: #88a2ff"
                    >Agenda</span
                  >
                </li>
                <li>
                  <span class="feature" style="background-color: #ff9d88"
                    >Gestionnaire de tâches</span
                  >
                </li>
                <li>
                  <span class="feature" style="background-color: #39a958"
                    >Agenda personnel</span
                  >
                </li>
                <li>
                  <span class="feature" style="background-color: #e4afa4"
                    >Liste de taches</span
                  >
                </li>
                <li>
                  <span class="feature" style="background-color: #bc88ff"
                    >Gestion de fichiers</span
                  >
                </li>
                <li>
                  <span class="feature" style="background-color: #ff88d7"
                    >Partage de documents</span
                  >
                </li>
                <li>
                  <span class="feature" style="background-color: #9d9d9d"
                    >Chat</span
                  >
                </li>
              </ul>
            </div>
            <div class="img-container">
              <img src="" alt="Image particulier" class="img-fluid" />
            </div>
          </div>
        </div>

        <div id="individuel" class="category-description" style="display: none">
          <div>
            <h2>Individuel</h2>
            <p class="lead">Description de la catégorie individuel.</p>
            <div class="features">
              <ul class="feature-list">
                <!-- Utilisation d'une liste non ordonnée -->
                <li><span class="feature">Notes</span></li>
                <li><span class="feature">Rappels</span></li>
                <li><span class="feature">Documents</span></li>
              </ul>
            </div>
          </div>
          <div class="img-container">
            <img
              src="https://th.bing.com/th/id/OIP.idN4uEV2ao8MSOlVM2Jm-AHaFM?rs=1&pid=ImgDetMain"
              alt="Image individuel"
              class="img-fluid"
            />
          </div>
        </div>

        <div id="entreprise" class="category-description" style="display: none">
          <div>
            <h2>Entreprise</h2>
            <p class="lead">Description de la catégorie entreprise.</p>
            <div class="features">
              <ul class="feature-list">
                <!-- Utilisation d'une liste non ordonnée -->
                <li><span class="feature">Projet</span></li>
                <li><span class="feature">Collaboration</span></li>
                <li><span class="feature">Analytics</span></li>
              </ul>
            </div>
          </div>
          <div class="img-container">
            <img
              src="https://via.placeholder.com/300x200"
              alt="Image entreprise"
              class="img-fluid"
            />
          </div>
        </div>
      </div>
    </section>

    <!--Quatrième Section-->
    <footer class="footer">
      <div class="footer__addr">
        <h1 class="footer__logo">Aurana</h1>

        <h2>Contact</h2>

        <address>ESGI, Paris 75012<br /></address>
      </div>

      <ul class="footer__nav">
        <li class="nav__item">
          <h2 class="nav__title">Réseaux</h2>

          <ul class="nav__ul">
            <li>
              <a href="#">Facebook</a>
            </li>

            <li>
              <a href="#">Instagram</a>
            </li>

            <li>
              <a href="#">Twitter / X</a>
            </li>
          </ul>
        </li>

        <li class="nav__item nav__item--extra">
          <h2 class="nav__title">Produits</h2>
          <ul class="nav__ul nav__ul--extra">
            <li>
              <a href="#">Gestionnaire</a>
            </li>

            <li>
              <a href="#">Bundle</a>
            </li>

            <li>
              <a href="#">‎ </a>
            </li>

            <li>
              <a href="#">Particulier</a>
            </li>

            <li>
              <a href="#">Education</a>
            </li>

            <li>
              <a href="#">Entreprise</a>
            </li>
          </ul>
        </li>

        <li class="nav__item">
          <h2 class="nav__title">Legal</h2>
          <ul class="nav__ul">
            <li>
              <a href="#">Politique de confidentialité</a>
            </li>

            <li>
              <a href="#">Conditions d'utilisation</a>
            </li>
          </ul>
        </li>
      </ul>

      <div class="legal">
        <p>&copy; 2024. All rights reserved.</p>
      </div>
    </footer>

    <!-- Scripts Bootstrap -->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script>
    <!--JS-->
    <script src="js/index.js"></script>
  </body>
</html>
