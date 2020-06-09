
$(document).ready(function () {

    //ajaxRequest("GET", "php/request.php",recieve);
    ajaxRequest("GET", "php/request.php",function(data){
        if(data != ""){
            let json = JSON.parse(data);
            document.getElementById("rangePrice").dataset["min"] = json["min"];
            document.getElementById("rangePrice").dataset["max"] = json["max"];


            document.getElementById("min_value").innerHTML = json["min"];
            document.getElementById("max_value").innerHTML = json["max"];
        }
    },"type=price_range");

    document.getElementById("departure").addEventListener('input', function () {
        let val = this.value;
        ajaxRequest("GET", "php/request.php",function(data){
            $("#departure_list").empty();
            if(data != ""){
                let json = JSON.parse(data);
                for(let i=0; i<json.length; i++){
                    $("#departure_list").append('<option value="'+json[i]["city"]+' ['+json[i]["airportCode"]+']">');
                }
            }
        },"type=completion&data="+val);
    });

    document.getElementById("arrival").addEventListener('input', function () {
        let val = this.value;
        ajaxRequest("GET", "php/request.php",function(data){
            $("#arrival_list").empty();
            if(data != ""){
                let json = JSON.parse(data);
                for(let i=0; i<json.length; i++){
                    $("#arrival_list").append('<option value="'+json[i]["city"]+' ['+json[i]["airportCode"]+']">');
                }
            }
        },"type=completion&data="+val);
    });

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
}

function updatePrice(message) {

    //document.getElementById('nbFlights').innerText = ""+it;
}