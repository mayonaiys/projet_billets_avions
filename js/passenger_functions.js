
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
        ajaxRequest("GET", "php/request.php", insertionResult, "type=reservation&data=" + JSON.stringify(tab));
    }
}


function insertionResult(data) {
}

function createTab(){
    let tab = [];
    for(let i = 0; i < passengerNumber; i++){
        tab.push({});
    }
    return tab;
}