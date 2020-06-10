<?php
session_start();

include 'controler.php';

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);


if(!empty($type)){
    $db = connexbdd();
    if($type=="research"){
        $data = filter_input(INPUT_GET, 'data');
        getAvailableFlights($db,$data);
    }

    if($type == "price_range"){
        echo(json_encode(getPriceRange($db)));
    }

    if($type == "completion"){
        $data = filter_input(INPUT_GET, 'data',FILTER_SANITIZE_STRING);
        $list = getCities($db,$data);
        echo json_encode($list);
    }
}

//SELECT MAX(fare),MIN(fare) FROM `fares` WHERE 1

