let toggle = document.querySelector('.toggle');
let left = document.querySelector('.left');
let right = document.querySelector('.right');
let close = document.querySelector('.close');
let body = document.querySelector('body');
let searchBx = document.querySelector('.searchBx');
let searchOpen = document.querySelector('.searchOpen');
let searchClose = document.querySelector('.searchClose');
toggle.addEventListener('click', () => {
    toggle.classList.toggle('active');
    left.classList.toggle('active');
    right.classList.toggle('overlay');
    body.style.overflow = 'hidden';
});
close.onclick = () => {
    toggle.classList.remove('active');
    left.classList.remove('active');
    right.classList.remove('overlay');
    body.style.overflow = '';
};
searchOpen.onclick = () => {
    searchBx.classList.add('active');
};
searchClose.onclick = () => {
    searchBx.classList.remove('active');
};
window.onclick = (e) => {
    if (e.target == right) {
        toggle.classList.remove('active');
        left.classList.remove('active');
        right.classList.remove('overlay');
        body.style.overflow = '';
    }
};

function TaskIcon(taskIcon) {
    taskIcon.classList.toggle('done');
    taskIcon.classList.toggle('notDone');
    var taskName = taskIcon.closest('li').querySelector('.tasksName');
    if (taskName) {
        taskName.classList.toggle('tasksLine', taskIcon.classList.contains('done'));
    }
}

// Fonction pour gérer les clics sur les étoiles
function toggleStarCompletion(starIcon, taskId) {
    starIcon.classList.toggle('full');
    starIcon.classList.toggle('half');
}




function toggleCreateTaskMenu() {
    var menu = document.getElementById("createTaskMenu");
    menu.classList.toggle("show");
}

window.onclick = function(event) {
    if (!event.target.matches('.tasksDots')) {
        var menus = document.getElementsByClassName("createTaskMenu");
        for (var i = 0; i < menus.length; i++) {
            var menu = menus[i];
            if (menu.classList.contains('show')) {
                menu.classList.remove('show');
            }
        }
    }
}