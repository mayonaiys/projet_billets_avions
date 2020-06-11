function logout() {
    ajaxRequest("GET","php/request.php",function () {
        let path = window.location.pathname;
        let page = path.split("/").pop();

        if(page==="index.html"){
            displayIndex("notconnected");
        } else {
            document.location.href= "index.html";
        }
    },"type=logout");
}

function getLogin(id){
    ajaxRequest("GET","php/request.php",function (response) {
        document.getElementById(id).innerText = " "+response;
    },"type=getLogin");
}