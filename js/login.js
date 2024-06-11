
// Déroulent de la page de connexion

const signUpButton = document.getElementById("signUp");
const signInButton = document.getElementById("signIn");
const container = document.getElementById("container");

signUpButton.addEventListener("click", () => {
  container.classList.add("right-panel-active");
});

signInButton.addEventListener("click", () => {
  container.classList.remove("right-panel-active");
});

// Code Captcha W²

var dataDiv = document.getElementById('data');
var questionsReponses = JSON.parse(dataDiv.getAttribute('data-captcha'));
var outputDiv = document.getElementById('output');
outputDiv.textContent = JSON.stringify(questionsReponses);

const questions = Object.keys(questionsReponses);
const indexAleatoire = Math.floor(Math.random() * questionsReponses.length);
const questionAleatoire = questionsReponses[indexAleatoire].question;
const reponse = questionsReponses[indexAleatoire].reponse;

const captcha = document.getElementById('captcha-question');
captcha.textContent = questionAleatoire;

const boutonConnexion = document.getElementById('submit-captcha');

// Ajout d'un écouteur d'événements sur le bouton
boutonConnexion.addEventListener('click', function() {
  const answerInput = document.getElementById('captcha-answer');
  const loginForm = document.getElementById("login-form");

  if ((answerInput.value).toLowerCase() == reponse.toLowerCase())
  {
    loginForm.submit();
  }

  else 
  {
    alert('Réponse incorrecte !');
    window.location.href = "https://myaurana.com/pages/login.php";
  }
  });


  // prévention anti brute force


var paramTemps = document.getElementById('temps');
var seconds = paramTemps.getAttribute('temps');


function updateCountdown() {

    document.getElementById('countdown').textContent = seconds;

    seconds--;

    if (seconds < 0) {
        clearInterval(interval);
        document.getElementById('countdown').classList.add('hidden');
        document.getElementById('champs').classList.remove('hidden'); 
    }
}

var dataDiv1 = document.getElementById('data1');
var essais = dataDiv1.getAttribute('data-essais');

if (essais == 1) {
  document.getElementById('champs').classList.add('hidden');
  var interval = setInterval(updateCountdown, 1000);
}