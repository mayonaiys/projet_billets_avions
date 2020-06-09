
$(document).ready(function () {

    //ajaxRequest("GET", "php/request.php",recieve);


});


function research() {
    let tab = {};

    tab["depCity"] = document.getElementById("departure").value;
    tab["arrivalCity"] = document.getElementById("arrival").value;
    tab["nbrPassengers"] = document.getElementById("nbPassengerTot").value;
    tab["nbrChild"] =document.getElementById("nbPassengerChild").value;
    tab["depDate"] = document.getElementById("date").value;
    tab["minPrice"] = document.getElementById("rangePrice").valueLow;
    tab["maxPrice"] = document.getElementById("rangePrice").valueHigh;

    let string = JSON.stringify(tab);
    //ajaxRequest("GET", "php/request.php",recieve,JSON.stringify(tab));

}

function recieve(message){

}