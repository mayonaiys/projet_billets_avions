<?php
session_start();

include 'controler.php';

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
        session_start();
        $_SESSION['flightID']=$data['id'];
        echo($_SESSION['flightID']);
    }

    if($type == "reserve"){
        header("Location: ../passengerform.php");
    }
}

