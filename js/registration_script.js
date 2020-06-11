//Script de la page d'enregistrement
function register(){ //Pour chaque champs on va vérifier si les informations sont bient rentrées, si oui on les enregistre dans un tableau
    let tab = {};
    if(document.getElementById("firstname").value !== ""){
        tab["firstname"] = document.getElementById("firstname").value;
        document.getElementById("firstname").classList = "form-control";
    } else {
        document.getElementById("firstname").classList = "form-control is-invalid";
    }

    if(document.getElementById("name").value !== ""){
        tab["name"] = document.getElementById("name").value;
        document.getElementById("name").classList = "form-control";
    } else {
        document.getElementById("name").classList = "form-control is-invalid";
    }

    if(document.getElementById("mail").value !== ""){
        tab["mail"] = document.getElementById("mail").value;
        document.getElementById("mail").classList = "form-control";
    } else {
        document.getElementById("mail").classList = "form-control is-invalid";
    }

    if(document.getElementById("birthdate").value !== ""){
        tab["birthdate"] = document.getElementById("birthdate").value;
        document.getElementById("birthdate").classList = "form-control";
    } else {
        document.getElementById("birthdate").classList = "form-control is-invalid";
    }

    if(document.getElementById("password").value !== ""){
        tab["password"] = document.getElementById("password").value;
        document.getElementById("password").classList = "form-control";
    } else {
        document.getElementById("password").classList = "form-control is-invalid";
    }

    if(document.getElementById("passwordrepeat").value !== ""){
        if(document.getElementById("passwordrepeat").value===(document.getElementById("password").value)){ //On vérifie que les mots de passes correspondent
            document.getElementById("passwordrepeat").classList = "form-control";
            ajaxRequest("GET", "php/request.php",function (response) { //Requête ajax pour demander au serveur d'enregistrer l'utilisateur ou, s'il existe déjà, le faire savoir
                if(response==="alreadyexist"){ //Si l'utilisateur existe déjà
                    document.getElementById("error").innerHTML='<section class="container alert alert-danger">L\'utilisateur est déjà enregistré.</section>'; //On l'indique
                } else if(response==="registered"){ //Sinon il est enregistré
                    document.location.href="index.html"; //On revient sur la page d'accueil
                }

            },"type=register&data="+JSON.stringify(tab));
        } else { //Si les mots de passe ne correspondent pas
            document.getElementById("error").innerHTML='<section class="container alert alert-danger">Les mots de passe ne correspondent pas.</section>'; //On l'indique
        }
    } else {
        document.getElementById("passwordrepeat").classList = "form-control is-invalid";
    }
}