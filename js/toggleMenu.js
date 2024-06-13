function toggleMenu() {
    var menu = document.querySelector('.menu');
    menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
    }
    
    var createGroupModal = document.getElementById("CreateGroupModal");
var joinGroupModal = document.getElementById("JoinGroupModal");
var openCreateGroupModal = document.getElementById("openCreateGroupModal");
var openJoinGroupModal = document.getElementById("openJoinGroupModal");
var closeCreateGroupModal = document.getElementById("closeCreateGroupModal");
var closeJoinGroupModal = document.getElementById("closeJoinGroupModal");


openCreateGroupModal.onclick = function(event) {
event.preventDefault();
createGroupModal.classList.add("show");
}

closeCreateGroupModal.onclick = function() {
createGroupModal.classList.remove("show");
}

openJoinGroupModal.onclick = function(event) {
event.preventDefault();
joinGroupModal.classList.add("show");
}

closeJoinGroupModal.onclick = function() {
joinGroupModal.classList.remove("show");
}


document.addEventListener('DOMContentLoaded', function() {
var manageGroupModal = document.getElementById("ManageGroupModal");
var closeManageGroupModal = document.getElementById("closeManageGroupModal");

function openManageGroupModal(groupID, groupName, groupDescription, groupCode) {
document.getElementById("manageGroupName").value = groupName;
document.getElementById("manageGroupDescription").value = groupDescription;
document.getElementById("manageGroupCode").value = groupCode;
manageGroupModal.classList.add("show");
}

closeManageGroupModal.onclick = function() {
manageGroupModal.classList.remove("show");
}

window.onclick = function(event) {
if (event.target == manageGroupModal) {
    manageGroupModal.classList.remove("show");
}
};

document.getElementById("openManageGroupModalBtn").onclick = function() {
var groupID = 1;
var groupName = "Nom du Groupe";
var groupDescription = "Description du Groupe";
var groupCode = "Code du Groupe";
openManageGroupModal(groupID, groupName, groupDescription, groupCode);
};
});