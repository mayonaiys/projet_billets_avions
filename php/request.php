<?php
session_start();

require("controler.php");

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);


if(!empty($type)){
    $db = connexbdd();
    if($type=="research"){
        $data = filter_input(INPUT_GET, 'data');
        $json_array = json_decode($data,true);


        echo("ok");
        //echo("bien reçu : " . print_r($json_array));
    }


    if($type == "price_range"){

    }
}

//SELECT MAX(fare),MIN(fare) FROM `fares` WHERE 1