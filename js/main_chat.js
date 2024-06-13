document.addEventListener('DOMContentLoaded', function() {
    // Création de la fenêtre modale (inchangé)
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

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Ajoutez ici votre code pour synchroniser la liste de contacts
    });

    var tasksDots = document.querySelectorAll('.tasksDots');
    tasksDots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            modal.style.display = 'block';
        });
    });
});

function fetchMessages() {
    fetch('../mysql/get_message.php')
        .then(response => response.json())
        .then(data => {
            const chat = document.getElementById('chat_messages');
            chat.innerHTML = '';
            data.forEach(msg => {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message');
                messageElement.innerHTML = `
                    <p><strong>${msg.Auteur_Nom || 'Anonyme'}</strong>: ${msg.Texte}</p>
                    <p><em>${new Date(msg.Date_Envoi).toLocaleString()}</em></p>
                `;
                chat.appendChild(messageElement);
            });
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
        });
}

document.getElementById('newMessageForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch('../mysql/submit_message.php', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.ok) {
            document.getElementById('newMessageInput').value = '';
            fetchMessages();
        } else {
            console.error('Error submitting message:', response.statusText);
        }
    }).catch(error => {
        console.error('Error submitting message:', error);
    });
});

let intervalId;

function startFetchingMessages() {
    intervalId = setInterval(fetchMessages, 5000); // Fetch new messages every 5 seconds
}

function stopFetchingMessages() {
    clearInterval(intervalId);
}

// Start fetching messages when the page is visible
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        startFetchingMessages();
    } else {
        stopFetchingMessages();
    }
});

// Initially start fetching messages
fetchMessages();
startFetchingMessages();
