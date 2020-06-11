//Script de la page de confirmation de réservation
function confirm() {
    let tab = createTab();
    let valid = true;
    for(let i=1; i<=passengerNumber; i++){
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

    if(valid) {
        for(let i=1; i<=passengerNumber; i++){
            document.getElementById("firstname"+i).disabled = true;
            document.getElementById("name"+i).disabled = true;
            document.getElementById("mail"+i).disabled = true;
            document.getElementById("date"+i).disabled = true;
        }
        document.getElementById("confirm").style="display:none;";
        document.getElementById("returnIndex").style=null;
        ajaxRequest("GET", "php/request.php", insertionFares, "type=booking&data=" + JSON.stringify(tab));
    }
}


function insertionFares(json) {
    let data = JSON.parse(json);
    let totalPrice = 0;
    for(let i = 1; i <= passengerNumber; i++){
        document.getElementById('price'+i).style=null;
        let fareWithTaxes = data[i-1][0]+data[i-1][1];
        totalPrice += fareWithTaxes;
        document.getElementById('price'+i).innerText="Prix : "+data[i-1][0]+"€ (HT) + "+data[i-1][1]+"€ (Charges) = "+fareWithTaxes+"€ (TTC)";

        $("#billet"+i).empty();
        $("#billet"+i).append('<button onclick="getBillet('+i+')" >récupérer le billet</button>');
    }
    document.getElementById('totalprice').innerHTML='<div class="card">\n' +
        '                    <div class="card-body">\n' +
        '                        <h6>Prix total : '+totalPrice+'€ (TTC)</h6>\n' +
        '                    </div>\n' +
        '                </div><br>';
}

function createTab(){
    let tab = [];
    for(let i = 0; i < passengerNumber; i++){
        tab.push({});
    }
    return tab;
}


function getBillet(id) {
    id = parseInt(id) - 1;

    window.open("billet.php?id="+id);

}