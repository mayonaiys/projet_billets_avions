<?php
session_start();

include 'php/controller.php';

//var_dump($_SESSION);

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT); // on récupère l'id du passager;

$profiles = explode(" ",$_SESSION["profile_list"]);

$db = connexbdd();

$client = getClientInfo($db,intval($profiles[$id])); // on récupère les infos du passager.


$flightInfo = getFlightInfo($db,$_SESSION["flightID"]); // on récupère les infos du vol

//var_dump($flightInfo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billet</title>

    <script>
        window.onload = window.print();
    </script>
</head>
<body>

<div style="display: flex;flex-direction: row;justify-content: space-between;background-color: rgba(0,0,0,0.2)">
    <h2>Billet Electronique</h2>
    <h2>Canada Airline</h2>
</div>
<hr>
<div style="margin-left: 10%">
    <h3> Référence de votre Réservation : <?php echo($_SESSION["booking_id"]); ?></h3>
    <p> À l'aéroport, vous devez présenter une pièce d'identité.</p>
    <h3> Passager :</h3>
    <p> <?php echo($client["name"]." ".$client["firstname"]." (".$client["birth"].")"); ?> </p>
</div>
<hr>
<table style="width: 100%;text-align: center">
    <tr>
        <td>
            <strong>Vol</strong>
        </td>
        <td>
            <strong>Date</strong>
        </td>
        <td>
            <strong>Départ</strong>
        </td>
        <td>
            <strong>Arrivée</strong>
        </td>
        <td>
            <strong>Fin d'enregistrement</strong>
        </td>
        <td>
            <strong>Total bagages</strong>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo($_SESSION["flightID"]) ?>
        </td>
        <td>
            <?php echo($_SESSION["flight_date"]) ?>
        </td>
        <td>
            <?php echo($flightInfo["departureTime"]."<br/>".$flightInfo["city"]." [".$flightInfo["originAirport"]."]<br/>") ?>
        </td>
        <td>
            <?php echo($flightInfo["arrivalTime"]."<br/>".$flightInfo["city2"]." [".$flightInfo["destinationAirport"]."]<br/>") ?>
        </td>
        <td>
            <?php

            echo date("H:i", strtotime($flightInfo["departureTime"]) - strtotime("00:30")); // on retire 30 min à l'heure de départ

            ?>
        </td>
        <td>
            1 x 23 Kg
        </td>
    </tr>
</table>
</body>
</html>