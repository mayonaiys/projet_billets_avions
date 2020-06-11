//Script de la page de confirmation de réservation
function confirm() { //Fonction de confirmation du vol sélectionné
    let tab = createTab();
    let valid = true;
    for(let i=1; i<=passengerNumber; i++){ //Pour chaque formulaire correspondant à un passager on va vérifier si les informations
        //sont bien entrées, si oui, on enregistre ces informations dans un tableau
        let user = {};
        if(document.getElementById("firstname"+i).value !== ""){
            user["firstname"] = document.getElementById("firstname"+i).value;
            document.getElementById("firstname"+i).classList ="form-control mb-2 mr-sm-2";
        } else {
            document.getElementById("firstname"+i).classList ="form-control mb-2 mr-sm-2 is-invalid"
            valid = false;
        }

        if(document.getElementById("name"+i).value !==""){
            user["name"] = document.getElementById("name"+i).value;
            document.getElementById("name"+i).classList ="form-control mb-2 mr-sm-2";
        } else {
            document.getElementById("name"+i).classList ="form-control mb-2 mr-sm-2 is-invalid"
            valid = false;
        }

        if(document.getElementById("mail"+i).value !==""){
            user["mail"] = document.getElementById("mail"+i).value;
            document.getElementById("mail"+i).classList ="form-control mb-2 mr-sm-2";
        } else {
            document.getElementById("mail"+i).classList ="form-control mb-2 mr-sm-2 is-invalid"
            valid = false;
        }

        if(document.getElementById("date"+i).value !==""){
            user["date"] = document.getElementById("date"+i).value;
            document.getElementById("date"+i).classList ="form-control mb-2 mr-sm-2";
        } else {
            document.getElementById("date"+i).classList ="form-control mb-2 mr-sm-2 is-invalid"
            valid = false;
        }

        tab[i-1]=user;
    }

    if(valid) { //Si toute les informations ont bien été entrées
        for(let i=1; i<=passengerNumber; i++){ //Pour chaque formulaire on désactive les champs
            document.getElementById("firstname"+i).disabled = true;
            document.getElementById("name"+i).disabled = true;
            document.getElementById("mail"+i).disabled = true;
            document.getElementById("date"+i).disabled = true;
        }
        document.getElementById("confirm").style="display:none;"; //On cache le bouton de confirmation
        ajaxRequest("GET", "php/request.php", insertionFares, "type=booking&data=" + JSON.stringify(tab)); //Requête ajax pour demander au serveur les tarifs pour chaque passager
    }
}


function insertionFares(json) { //Fonction d'insertion des tarifs pour chaque passager
    console.log(json);
    let data = JSON.parse(json);
    let totalPrice = 0;
    for(let i = 1; i <= passengerNumber; i++){
        document.getElementById('price'+i).style=null;
        let fareWithTaxes = data[i-1][0]+data[i-1][1];
        totalPrice += fareWithTaxes;
        document.getElementById('price'+i).innerText="Prix : "+data[i-1][0]+"€ (HT) + "+data[i-1][1]+"€ (Charges) = "+fareWithTaxes+"€ (TTC)";

        $("#billet"+i).empty();
        $("#billet"+i).append('<button class="btn btn-primary" onclick="getBillet('+i+')" >Récupérer le billet</button>');    }
    document.getElementById('totalprice').innerHTML='<div class="card">\n' +
        '                    <div class="card-body">\n' +
        '                        <h6>Prix total : '+totalPrice+'€ (TTC)</h6>\n' +
        '                    </div>\n' +
        '                </div><br>';
}

function createTab(){ //Fonction de création d'un tableau vide
    let tab = [];
    for(let i = 0; i < passengerNumber; i++){
        tab.push({});
    }
    return tab;
}


function getBillet(id) { //Fonction de récupération du billet
    id = parseInt(id) - 1;

    window.open("billet.php?id="+id);

}