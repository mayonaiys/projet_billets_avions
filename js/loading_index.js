
$(document).ready(function () {

    //ajaxRequest("GET", "php/request.php",recieve);
<<<<<<< HEAD
    ajaxRequest("GET", "php/request.php",updatePrice);

=======
>>>>>>> 5c37212a6ef91f443e3d29501213a218236e5747
});


function research() {

    let tab = {};
    tab["depCity"] = document.getElementById("departure").value;
    tab["arrivalCity"] = document.getElementById("arrival").value;
    tab["nbrAdults"] = parseInt(document.getElementById("nbPassengerAdult").value);
    tab["nbrChild"] = parseInt(document.getElementById("nbPassengerChild").value);
    tab["depDate"] = document.getElementById("date").value;
    tab["minPrice"] = 0;
    tab["maxPrice"] = 3000;

    ajaxRequest("GET", "php/request.php",displayList,"type=research&data="+JSON.stringify(tab));

}

function displayList(response){
    document.getElementById('list').innerHTML = response;
    let char = "$";
    let it = 0;
    for(let item of response){
        if(item === char) it++;
    }
<<<<<<< HEAD
}

function updatePrice(message) {

=======
    document.getElementById('nbFlights').innerText = ""+it;
>>>>>>> 5c37212a6ef91f443e3d29501213a218236e5747
}