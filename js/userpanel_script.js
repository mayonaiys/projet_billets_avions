$(document).ready(function () {
    getLogin("brand");
    ajaxRequest("GET","php/request.php",displayBooking,"type=getBooking");
});

function displayBooking(response) {
    document.getElementById('list').innerHTML = response;
}
