// Gestion du déroulement de la page de connexion

// Sélection des éléments du DOM pour les boutons d'inscription et de connexion, ainsi que le conteneur principal
const signUpButton = document.getElementById("signUp");
const signInButton = document.getElementById("signIn");
const container = document.getElementById("container");

// Ajout des écouteurs d'événements pour basculer entre les panneaux de connexion et d'inscription
signUpButton.addEventListener("click", () => {
    container.classList.add("right-panel-active");
});

signInButton.addEventListener("click", () => {
    container.classList.remove("right-panel-active");
});

// Gestion du Captcha
const dataDiv = document.getElementById('data');
if (dataDiv) {
    // Parsing des données captcha à partir de l'attribut data
    const questionsReponses = JSON.parse(dataDiv.getAttribute('data-captcha'));
    
    // Affichage des données captcha dans l'élément output pour le débogage
    const outputDiv = document.getElementById('output');
    outputDiv.textContent = JSON.stringify(questionsReponses);

    // Sélection aléatoire d'une question captcha
    const questions = questionsReponses.map(item => item.question); // Utilisation de map pour obtenir les questions
    const indexAleatoire = Math.floor(Math.random() * questions.length);
    const questionAleatoire = questions[indexAleatoire];
    const reponse = questionsReponses.find(item => item.question === questionAleatoire).reponse;

    // Affichage de la question captcha sélectionnée
    const captcha = document.getElementById('captcha-question');
    captcha.textContent = questionAleatoire;

    const boutonConnexion = document.getElementById('submit-captcha');

    // Ajout d'un écouteur d'événements sur le bouton de connexion pour vérifier la réponse du captcha
    boutonConnexion.addEventListener('click', function() {
        const answerInput = document.getElementById('captcha-answer');
        const loginForm = document.getElementById("login-form");

        // Vérification de la réponse du captcha
        if (answerInput.value.trim().toLowerCase() === reponse.toLowerCase()) {
            // Si la réponse est correcte, soumission du formulaire de connexion
            loginForm.submit();
        } else {
            // Si la réponse est incorrecte, affichage d'un message d'alerte et redirection vers la page de connexion
            alert('Réponse incorrecte !');
            window.location.href = "https://myaurana.com/pages/login.php";
        }
    });
}

// Prévention anti-brute force
const paramTemps = document.getElementById('temps');
let seconds = paramTemps ? parseInt(paramTemps.getAttribute('temps')) : 0;

// Fonction de mise à jour du compte à rebours
function updateCountdown() {
    if (seconds > 0) {
        document.getElementById('countdown').textContent = seconds;
        seconds--;
    } else {
        clearInterval(interval);
        document.getElementById('countdown').classList.add('hidden');
        document.getElementById('champs').classList.remove('hidden');
        window.location.href = "https://myaurana.com/pages/login.php?statut=session_expiree";
    }
}

// Récupération des données d'essais à partir de l'élément data1
const dataDiv1 = document.getElementById('data1');
if (dataDiv1) {
    const essais = parseInt(dataDiv1.getAttribute('data-essais'));

    if (essais === 1) {
        document.getElementById('champs').classList.add('hidden');
        var interval = setInterval(updateCountdown, 1000);
    }
}
