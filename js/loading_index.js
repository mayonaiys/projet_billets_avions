
$(document).ready(function () {

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

    if(document.getElementById("departure").value !== ""){
        let departure = document.getElementById("departure").value;
        departure = departure.split(' ');
        tab["depAirport"] = departure[1][1] + departure[1][2] + departure[1][3];
        document.getElementById("departure").classList = "form-control mb-2 mr-sm-2";
    } else {
        document.getElementById("departure").classList = "form-control mb-2 mr-sm-2 is-invalid";
    }

    if(document.getElementById("arrival").value !== ""){
        let arrival = document.getElementById("arrival").value;
        arrival = arrival.split(' ');
        tab["arrivalAirport"] = arrival[1][1] + arrival[1][2] + arrival[1][3];
        document.getElementById("arrival").classList = "form-control mb-2 mr-sm-2";
    } else {
        document.getElementById("arrival").classList = "form-control mb-2 mr-sm-2 is-invalid";
    }

    if(document.getElementById("date").value !== ""){
        tab["depDate"] = document.getElementById("date").value;
        document.getElementById("date").classList = "form-control mb-2 mr-sm-2";
    } else {
        document.getElementById("date").classList = "form-control mb-2 mr-sm-2 is-invalid";
    }

    if(document.getElementById("nbPassengerAdult").value !== "+ de 4 ans"){
        tab["nbrAdults"] = parseInt(document.getElementById("nbPassengerAdult").value);
        document.getElementById("nbPassengerAdult").classList = "custom-select mr-sm-2";
    } else {
        document.getElementById("nbPassengerAdult").classList = "custom-select mr-sm-2 is-invalid"
    }

    if(document.getElementById("nbPassengerChild").value !== "- de 4 ans"){
        tab["nbrChildren"] = parseInt(document.getElementById("nbPassengerAdult").value);
        document.getElementById("nbPassengerChild").classList = "custom-select mr-sm-2";
    } else {
        document.getElementById("nbPassengerChild").classList = "custom-select mr-sm-2 is-invalid"
    }

    tab["minPrice"] = parseInt(document.getElementById("min_value").innerText);
    tab["maxPrice"] = parseInt(document.getElementById("max_value").innerText);

    document.getElementById("reserve").classList = "btn btn-primary disabled";
    ajaxRequest("GET", "php/request.php",displayList,"type=research&data="+JSON.stringify(tab));

}

let oldID ="";

function selectFlight(id){
    if(oldID!==""){
        document.getElementById(oldID).style = null;
    }
    document.getElementById(id).style = "background-color: #39da58;";
    document.getElementById("reserve").classList = "btn btn-primary";
    oldID = id;
    let tab = {id};
    ajaxRequest("GET","php/request.php",  null,"type=saveFlightID&data="+JSON.stringify(tab));
}

function displayList(response){
    document.getElementById('list').innerHTML = response;
    let char = "$";
    let it = 0;
    for(let item of response){
        if(item === char) it++;
    }
    document.getElementById('nbFlights').innerText = it;
}