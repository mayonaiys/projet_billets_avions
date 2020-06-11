function register(){
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
        if(document.getElementById("passwordrepeat").value===(document.getElementById("password").value)){
            document.getElementById("passwordrepeat").classList = "form-control";
            ajaxRequest("GET", "php/request.php",function (response) {
                console.log(response);
                if(response==="alreadyexist"){
                    document.getElementById("error").innerHTML='<section class="container alert alert-danger">L\'utilisateur est déjà enregistré.</section>';
                } else if(response==="registered"){
                    document.location.href="index.html";
                }

            },"type=register&data="+JSON.stringify(tab));
        } else {
            document.getElementById("error").innerHTML='<section class="container alert alert-danger">Les mots de passe ne correspondent pas.</section>';
        }
    } else {
        document.getElementById("passwordrepeat").classList = "form-control is-invalid";
    }
}