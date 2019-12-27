// function toggleSidebar(){
//     document.getElementById("sidebar").classList.toggle('active');
// }

$(document).ready(function () {

    $("#sidebar").mCustomScrollbar({
        theme: "minimal"
    });

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

});