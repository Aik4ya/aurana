document.addEventListener('DOMContentLoaded', function() {
    // Déclarations des éléments du DOM
    let toggle = document.querySelector('.toggle');
    let left = document.querySelector('.left');
    let right = document.querySelector('.right');
    let close = document.querySelector('.close');
    let body = document.querySelector('body');
    let searchBx = document.querySelector('.searchBx');
    let searchOpen = document.querySelector('.searchOpen');
    let searchClose = document.querySelector('.searchClose');

    // Fonction pour basculer les classes 'active' et 'overlay'
    if (toggle && left && right && body) {
        toggle.addEventListener('click', function() {
            this.classList.toggle('active');
            left.classList.toggle('active');
            right.classList.toggle('overlay');
            body.style.overflow = body.style.overflow === 'hidden' ? '' : 'hidden';
        });
    }

    // Gestion des boutons pour ouvrir et fermer le champ de recherche
    if (searchOpen && searchClose && searchBx) {
        searchOpen.onclick = function() {
            searchBx.classList.add('active');
        };
        searchClose.onclick = function() {
            searchBx.classList.remove('active');
        };
    }

    // Fermeture des éléments interactifs lorsque l'on clique à l'extérieur
    window.addEventListener('click', function(e) {
        if (e.target === right) {
            toggle.classList.remove('active');
            left.classList.remove('active');
            right.classList.remove('overlay');
            body.style.overflow = '';
        }
        if (e.target !== document.getElementById("createTaskMenu") && !e.target.matches(".tasksDots")) {
            var createTaskMenu = document.getElementById("createTaskMenu");
            if (createTaskMenu) {
                createTaskMenu.classList.remove("show");
            }
        }
    });
});

// Code pour gérer le calendrier
(function() {
    let date = new Date();
    let month = date.getMonth();
    let year = date.getFullYear();
    let monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
    document.querySelector('.calendarHead h2').textContent = monthNames[month] + ' ' + year;

    let daysInMonth = new Date(year, month + 1, 0).getDate();
    let startDay = new Date(year, month, 1).getDay();
    let daysList = document.querySelector('.days');
    while (daysList.firstChild) {
        daysList.removeChild(daysList.firstChild);
    }

    for (let i = 0; i < startDay - 1; i++) {
        let li = document.createElement('li');
        li.classList.add('inactive');
        daysList.appendChild(li);
    }

    for (let i = 1; i <= daysInMonth; i++) {
        let li = document.createElement('li');
        if (i === date.getDate()) {
            li.classList.add('active');
        }
        li.textContent = i;
        daysList.appendChild(li);
    }

    let endDay = new Date(year, month, daysInMonth).getDay();
    for (let i = endDay; i < 7; i++) {
        let li = document.createElement('li');
        li.classList.add('inactive');
        daysList.appendChild(li);
    }
})();
