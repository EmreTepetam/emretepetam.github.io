$(document).ready(function() {
    $('#welcomeModal').modal('show');

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
});