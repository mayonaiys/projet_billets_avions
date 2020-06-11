//Script de la page d'accueil
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
    ajaxRequest("GET","php/request.php",displayIndex,"type=isconnected");
});

function displayIndex(response){
    if(response==="notconnected"){
        document.getElementById('connectedmenu').style="display: none;";
        document.getElementById('notconnectedmenu').style=null;
        document.getElementById('notconnectedmenu').innerHTML = '<div class="dropdown show" id="logmenu">\n' +
            '            <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\n' +
            '                Se connecter\n' +
            '            </a>\n' +
            '            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">\n' +
            '                <form method="post" class="px-4 py-3">\n' +
            '                    <div class="form-group">\n' +
            '                        <label>Adresse mail</label>\n' +
            '                        <input type="email" class="form-control" name="mail" id="mail" placeholder="Entrez votre adresse mail">\n' +
            '                    </div>\n' +
            '                    <div class="form-group">\n' +
            '                        <label>Mot de passe</label>\n' +
            '                        <input type="password" class="form-control" name="password" id="password" placeholder="Entrez votre mot de passe">\n' +
            '                    </div>\n' +
            '                    <input class="btn btn-success" type="button" value="Se connecter" onclick="signin()">\n' +
            '                    <div id="error"></div>\n' +
            '                </form>\n' +
            '                <div class="dropdown-divider"></div>\n' +
            '                <a class="dropdown-item" href="viewregistration.html">Vous êtes nouveau ? Créez un compte !</a>\n' +
            '            </div>\n' +
            '        </div>';
    } else {
        document.getElementById('notconnectedmenu').style="display: none;";
        document.getElementById('connectedmenu').style=null;
        document.getElementById('connectedmenu').innerHTML= '<div class="btn-group">' +
            '<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ' +
            '<i class="material-icons">person</i>' +
            '<span id="login"></span> ' +
            '</button> ' +
            '<div class="dropdown-menu dropdown-menu-right"> ' +
            '<a class="dropdown-item" href="viewuserpanel.html">Mes réservation</a> ' +
            '<a class="dropdown-item" onclick="logout()">Deconnexion</a> ' +
            '</div> ' +
            '</div>';
        getLogin("login");
    }
}

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
        tab["nbrChildren"] = parseInt(document.getElementById("nbPassengerChild").value);
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

function signin(){
    let tab = {};
    if(document.getElementById('mail').value !== ""){
        tab["mail"]=document.getElementById('mail').value;
    } else {
        document.getElementById('mail').classList = "form-control is-invalid";
    }

    if(document.getElementById('password').value !== ""){
        tab["password"]=document.getElementById('password').value;
    } else {
        document.getElementById('password').classList = "form-control is-invalid";
    }
    ajaxRequest("GET","php/request.php",  function (response) {
        if(response==="connected"){
            ajaxRequest("GET","php/request.php",displayIndex,"type=isconnected");
        } else if(response==="error"){
            document.getElementById('error').innerHTML='<br><section class="container alert alert-danger">Email ou mot de passe incorrect.</section>';
        }
    },"type=login&data="+JSON.stringify(tab));
}