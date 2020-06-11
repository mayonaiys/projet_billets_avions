//Script commun à plusieurs pages
function logout() { //Fonction de déconnexion
    ajaxRequest("GET","php/request.php",function () { //Requête ajax demandant au serveur la deconnexion de l'utilisateur
        let path = window.location.pathname; //Récupération du chemin de la page actuelle
        let page = path.split("/").pop();

        if(page==="index.html"){ //Si c'est la page d'accueil
            displayIndex("notconnected"); //On appelle la fonction displayindex en indiquant que l'utilisateur n'est pas connecté
        } else {
            document.location.href= "index.html"; //On retourne sur la page d'index
        }
    },"type=logout");
}

function getLogin(id){ //Fonction de récupération du prénom de l'utilisateur en fonction de son id
    ajaxRequest("GET","php/request.php",function (response) { //Requête ajax demandant au serveur le prénom de l'utilisateur
        document.getElementById(id).innerText = " "+response; //Écriture du prénom de l'utilisateur dans la page courante
    },"type=getLogin");
}