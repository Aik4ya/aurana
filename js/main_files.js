// Fonction pour gérer le menu déroulant de l'utilisateur
function toggleMenu() {
    const menu = document.querySelector('.menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Fonction pour afficher une boîte de dialogue de confirmation avant de réaliser des actions critiques
function confirmAction(message) {
    return confirm(message);
}

// Écouter les événements sur le formulaire de téléchargement de fichier
document.getElementById('fileToUpload').addEventListener('change', function() {
    const fileSize = this.files[0].size / 1024 / 1024; // taille en Mo
    if (fileSize > 5) {
        alert("Le fichier est trop grand. Taille maximale autorisée : 5 Mo.");
        this.value = ''; // Réinitialise le champ du fichier
    }
});

// Vérification avant l'envoi du formulaire
document.querySelector('form').addEventListener('submit', function(event) {
    const fileInput = document.getElementById('fileToUpload');
    if (!fileInput.value) {
        alert("Veuillez sélectionner un fichier à télécharger.");
        event.preventDefault(); // Empêche le formulaire d'être soumis
    }
});
