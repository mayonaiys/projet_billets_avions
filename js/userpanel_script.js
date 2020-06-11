$(document).ready(function () { //A chaque fois que la page est chargée
    getLogin("brand"); //On récupère le prénom de l'utilisateur
    ajaxRequest("GET","php/request.php",displayBooking,"type=getBooking"); //Requête ajax pour récupérer les réservations faites par l'utilisateur
});

function displayBooking(response) {
    document.getElementById('list').innerHTML = response;
}
