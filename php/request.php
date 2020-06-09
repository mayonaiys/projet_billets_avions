<?php
include 'controler.php';

session_start();

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);


if(!empty($type)){

    if($type=="research"){
        $data = filter_input(INPUT_GET, 'data');
        getAvailableFlights($data);
    }

}