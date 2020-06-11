<?php
//Fichier traitant des requêtes faites en ajax
session_start();

include 'controller.php';

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

if(!empty($type)){
    $db = connexbdd(); //Connexion à la base de données
    if($type=="research"){
        $data = filter_input(INPUT_GET, 'data');
        echo(getAvailableFlights($db,$data)); //Retourne tous les avions disponible en fonction des données entrées par l'utilisateur
    }

    if($type == "price_range"){
        echo(json_encode(getPriceRange($db))); //Récupère la plage de prix des vols
    }

    if($type == "completion"){
        $data = filter_input(INPUT_GET, 'data',FILTER_SANITIZE_STRING);
        $list = getCities($db,$data); //Récupère les villes de la base de donnée
        echo json_encode($list);
    }

    if($type == "saveFlightID"){
        $data = filter_input(INPUT_GET, 'data');
        $data = json_decode($data,true);
        $_SESSION['flightID']=$data['id']; //Sauvegarde l'ID de l'avion sélectionné dans une session
        echo($_SESSION['flightID']);
    }

    if($type == "booking"){
        $data = filter_input(INPUT_GET, 'data');
        editClients($db,$data); //Modifie la base de donnée utilisateurs
        echo(showPrice($data));
    }

    if($type == "login"){
        $data = filter_input(INPUT_GET, 'data');
        echo(login($db,$data)); //Appelle la fonction de connexion
    }

    if($type == "isconnected"){
        if(isset($_SESSION['profile_id'])){ //Vérifié si l'id du profil est enregistré dans la session, si oui
            echo "connected"; //on renvoie connecté
        } else { //sinon
            echo "notconnected"; //on renvoie non connecté
        }
    }

    if($type == "getLogin"){
        echo $_SESSION['firstname']; //Récupère le prénom de l'utilisateur connecté
    }

    if($type == "logout"){
        $_SESSION['firstname']=null; //Supprime le prénom de l'utilisateur de la session
        $_SESSION['profile_id']=null; //Supprime l'id de l'utilisateur de la session
    }

    if($type == "register"){
        $data = filter_input(INPUT_GET, 'data');
        echo(register($db,$data)); //Appelle la fonction d'enregistrement d'un utilisateur
    }

    if($type == "getBooking"){
        echo(getBooking($db)); //Récupère les réservations effectuées par l'utilisateur
    }

    if($type == "getRandomFlights"){
        echo(getRandomFlights($db)); //Appelle la fonction de récupération d'avions aléatoires
    }

    if($type == "saveDiscountFlight"){
        $data = filter_input(INPUT_GET, 'data');
        $_SESSION['nbPassengers']=1; //Sauvegarde le nombre de passagers dans la session
        $_SESSION['flightID']=$_SESSION['flightID'.$data];
        $_SESSION['fare']=$_SESSION['fare'.$data];
        $_SESSION['charges']=$_SESSION['charges'.$data];
        $_SESSION['flight_date']=$_SESSION['flight_date'.$data];
        echo($_SESSION['discount']." ".$_SESSION['flightID']);
        header('Location: ../viewconfirm.php');
    }
}

