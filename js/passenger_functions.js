
function confirm() {
    let tab = [];
    for(let i=1; i<=passengerNumber; i++){
        let user = {};
        user["firstname"] = document.getElementById("firstname"+i).value;
        user["name"] = document.getElementById("name"+i).value;
        user["mail"] = document.getElementById("mail"+i).value;
        user["date"] = document.getElementById("date"+i).value;
        tab.push(user);
    }


    ajaxRequest("GET", "php/request.php",insertionResult,"type=reservation&data="+JSON.stringify(tab));
}


function insertionResult(data) {
    alert(data);
}