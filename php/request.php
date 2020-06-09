<?php
include 'controler.php';

session_start();

require("controler.php");

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);


if(!empty($type)){
    $db = connexbdd();
    if($type=="research"){
        $data = filter_input(INPUT_GET, 'data');
        getAvailableFlights($data);
    }

<<<<<<< HEAD

    if($type == "price_range"){

    }
}

//SELECT MAX(fare),MIN(fare) FROM `fares` WHERE 1
=======
}
>>>>>>> 5c37212a6ef91f443e3d29501213a218236e5747
