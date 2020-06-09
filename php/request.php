<?php
session_start();

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);


if(!empty($type)){

    if($type=="research"){
        $data = filter_input(INPUT_GET, 'data');
        $json_array = json_decode($data,true);


        echo("ok");
        //echo("bien reçu : " . print_r($json_array));
    }
}