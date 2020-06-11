<?php
session_start();

include 'controller.php';

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);


if(!empty($type)){
    $db = connexbdd();
    if($type=="research"){
        $data = filter_input(INPUT_GET, 'data');
        echo(getAvailableFlights($db,$data));
    }

    if($type == "price_range"){
        echo(json_encode(getPriceRange($db)));
    }

    if($type == "completion"){
        $data = filter_input(INPUT_GET, 'data',FILTER_SANITIZE_STRING);
        $list = getCities($db,$data);
        echo json_encode($list);
    }

    if($type == "saveFlightID"){
        $data = filter_input(INPUT_GET, 'data');
        $data = json_decode($data,true);
        $_SESSION['flightID']=$data['id'];
        echo($_SESSION['flightID']);
    }

    if($type == "booking"){
        $data = filter_input(INPUT_GET, 'data');
        editClients($db,$data);
        echo(showPrice($data));
    }

    if($type == "login"){
        $data = filter_input(INPUT_GET, 'data');
        echo(login($db,$data));
    }

    if($type == "isconnected"){
        if(isset($_SESSION['profile_id'])){
            echo "connected";
        } else {
            echo "notconnected";
        }
    }

    if($type == "getLogin"){
        echo $_SESSION['firstname'];
    }

    if($type == "logout"){
        $_SESSION['firstname']=null;
        $_SESSION['profile_id']=null;
    }

    if($type == "register"){
        $data = filter_input(INPUT_GET, 'data');
        echo(register($db,$data));
    }

    if($type == "getBooking"){
        echo(getBooking($db));
    }

    if($type == "getCheapestFlights"){
        echo(getRandomFlights($db));
    }
}

