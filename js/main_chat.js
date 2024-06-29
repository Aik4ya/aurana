document.addEventListener('DOMContentLoaded', () => {
    const chatMessages = document.getElementById('chat_messages');
    const newMessageForm = document.getElementById('newMessageForm');
    const newMessageInput = document.getElementById('newMessageInput');
    let intervalId;
    let currentChatType = 'group';  // 'group' or 'private'
    let currentRecipientId = null;

    function fetchMessages() {
        let url = currentChatType === 'group' ? '../mysql/fetch_messages.php' : '../mysql/fetch_private_messages.php';
        let params = currentChatType === 'private' ? `?recipient_id=${currentRecipientId}` : '';

        fetch(url + params)
            .then(response => response.json())
            .then(data => {
                chatMessages.innerHTML = '';  // Clear previous messages
                data.forEach(message => {
                    const messageElement = document.createElement('div');
                    messageElement.classList.add('message');
                    messageElement.innerHTML = `
                        <p><strong>${message.Pseudo}</strong>: ${message.Texte}</p>
                        <p><em>${new Date(message.Date_Envoi).toLocaleString()}</em></p>
                    `;
                    chatMessages.appendChild(messageElement);
                });
                // Scroll to the bottom of the chat container
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    newMessageForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const formData = new FormData(newMessageForm);
        formData.append('type', currentChatType);
        if (currentChatType === 'private') {
            formData.append('recipient_id', currentRecipientId);
        }

        fetch('../mysql/submit_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                newMessageInput.value = '';
                fetchMessages();
            }
        })
        .catch(error => console.error('Error submitting message:', error));
    });

    function startFetchingMessages() {
        intervalId = setInterval(fetchMessages, 5000);  // Fetch new messages every 5 seconds
    }

    function stopFetchingMessages() {
        clearInterval(intervalId);
    }

    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            fetchMessages();  // Fetch messages immediately when the tab becomes visible
            startFetchingMessages();
        } else {
            stopFetchingMessages();
        }
    });

    window.openPrivateChat = function(userId, userName) {
        currentChatType = 'private';
        currentRecipientId = userId;
        document.getElementById('chatTitle').innerHTML = `${userName}<br><span>Conversation privée</span>`;
        document.getElementById('switchToGroupChatBtn').style.display = 'block';
        fetchMessages();
    };

    window.openGroupChat = function() {
        currentChatType = 'group';
        currentRecipientId = null;
        document.getElementById('chatTitle').innerHTML = 'Groupe<br><span>Messages de groupe</span>';
        document.getElementById('switchToGroupChatBtn').style.display = 'none';
        fetchMessages();
    };

    // Initially fetch messages and start the interval
    fetchMessages();
    startFetchingMessages();
});




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