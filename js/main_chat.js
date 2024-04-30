document.addEventListener('DOMContentLoaded', function() {
    // Création de la fenêtre modale
    var modal = document.createElement('div');
    modal.setAttribute('id', 'contactModal');
    modal.style.display = 'none';
    modal.style.position = 'fixed';
    modal.style.zIndex = '1';
    modal.style.left = '0';
    modal.style.top = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.overflow = 'auto';
    modal.style.backgroundColor = 'rgba(0,0,0,0.4)';
    document.body.appendChild(modal);

    var modalContent = document.createElement('div');
    modalContent.style.backgroundColor = '#fefefe';
    modalContent.style.margin = '15% auto';
    modalContent.style.padding = '20px';
    modalContent.style.width = '30%';
    modal.appendChild(modalContent);

    var closeBtn = document.createElement('span');
    closeBtn.innerHTML = '&times;';
    closeBtn.style.color = '#aaa';
    closeBtn.style.float = 'right';
    closeBtn.style.fontSize = '28px';
    closeBtn.style.fontWeight = 'bold';
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    modalContent.appendChild(closeBtn);

    var form = document.createElement('form');
    form.setAttribute('id', 'contactForm');
    modalContent.appendChild(form);

    // Ajout des champs du formulaire
    var title = document.createElement('h2');
    title.textContent = 'Ajouter un contact';
    form.appendChild(title);
    
    var label = document.createElement('label');
    label.setAttribute('for', 'contactEmail');
    label.textContent = 'Email du contact:';
    form.appendChild(label);

    var input = document.createElement('input');
    input.setAttribute('type', 'email');
    input.setAttribute('id', 'contactEmail');
    input.setAttribute('name', 'contactEmail');
    form.appendChild(input);

    var submit = document.createElement('input');
    submit.setAttribute('type', 'submit');
    submit.setAttribute('value', 'Ajouter');
    form.appendChild(submit);

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Ajoutez ici votre code pour synchroniser la liste de contacts
    });

    // Affichage de la fenêtre modale lors du clic sur le bouton
    var tasksDots = document.querySelectorAll('.tasksDots');
    tasksDots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            modal.style.display = 'block';
        });
    });
});