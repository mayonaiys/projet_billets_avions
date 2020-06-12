
// fonction permettant de faire des requêtes ajax à nos fichiers php
function ajaxRequest(type, url, callback, data = null){

    let xhr = new XMLHttpRequest(); // on crée la requête

    if(type !== "POST"){ // si le type de requête n'est pas du post, on rajoute les data à la fin de l'url
        if(data != null){
            url+="?"+data;
        }
    }

    xhr.open(type, url);

    if(type === "POST"){ // si c'est du post on rajoute le content-Type correspondant
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    xhr.onload = () => {
        httpErrors(xhr.status); // quand on récupère la réponse, on vérifie le status.
        if(callback!=null){
            callback(xhr.responseText); // on appelle la fonction de callback avec la réponse.
        }
    };

    if(type === "POST"){ // si c'est du POST, on envoie les data
        xhr.send(data);
    }else{
        xhr.send();
    }

}


// actions suivant les codes d'erreurs ajax
function httpErrors(errorCode){
    switch (errorCode) {
        case 200:
            break;
        case 201:
            break;
        case 400: alert("Bad Request");
            break;
        case 401: alert("Unauthorized");
            break;
        case 403: alert("Forbidden");
            break;
        case 404: alert("Not Found");
            break;
        case 500: alert("Server Error");
            break;
        case 503: alert("Server Unavailable");
             break;
             default:
             }
}