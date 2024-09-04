$(document).ready(function() {
    $('#welcomeModal').modal('show');
});

var collapseElement = document.getElementById('toggle-icon');

if (collapseElement) {
    collapseElement.addEventListener('show.bs.collapse', function () {
        document.getElementById('toggle-icon').classList.remove('fa-chevron-up');
        document.getElementById('toggle-icon').classList.add('fa-chevron-down');
    });

    collapseElement.addEventListener('hide.bs.collapse', function () {
        document.getElementById('toggle-icon').classList.remove('fa-chevron-down');
        document.getElementById('toggle-icon').classList.add('fa-chevron-up');
    });
}
function toggleNavbar() {
    var navbar = document.getElementById("message-box-x");
    var messageBox = document.getElementById("message-box");
    var icon = document.getElementById("toggle-icon");

    if (navbar.classList.contains("navbar-expanded")) {
        navbar.classList.remove("navbar-expanded");
        messageBox.classList.remove("show");
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        navbar.classList.add("navbar-expanded");
        messageBox.classList.add("show");
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
