
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

// Code Captcha 

const questionsReponses = {
  "1":"1",
  "2":"2"
};

const questions = Object.keys(questionsReponses);
const questionAleatoire = questions[Math.floor(Math.random() * questions.length)];
const reponse = questionsReponses[questionAleatoire];

const captcha = document.getElementById('captcha-question');
captcha.textContent = questionAleatoire;

const boutonConnexion = document.getElementById('submit-captcha');

// Ajout d'un écouteur d'événements sur le bouton
boutonConnexion.addEventListener('click', function() {
  const answerInput = document.getElementById('captcha-answer');
  const loginForm = document.getElementById("login-form");

  if (answerInput.value == reponse)
  {
    loginForm.submit();
  }

  else 
  {
    alert('Réponse incorrecte !');
    window.location.href = "https://myaurana.com/pages/login.php";
  }
  });