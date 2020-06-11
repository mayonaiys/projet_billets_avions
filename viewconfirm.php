<?php
session_start();

include 'php/controller.php';
?>

<!doctype html>
<html lang='fr'>
     <head>
         <meta charset='utf-8'>
         <title>Passager</title>
         <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

         <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
         <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

         <link rel="stylesheet" href="css/style.css">
         <script src="js/multirange.js"></script>
         <script src="js/confirm_script.js"></script>
         <script src="js/ajax.js"></script>

         <script src="js/googleMap.js"></script>
         <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRRsa8PNbjYmn63HD1H7usOAmfBNh-DhA&callback=initMap" type="text/javascript"></script>


         <script>
            var passengerNumber = <?php echo($_SESSION['nbPassengers']); ?>
        </script>

    </head>
<body style="background-image:url('data/background.jpg'); background-size: cover;">

<br>

<div class="container-fluid">
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Canada Airline</a>
        <span class="navbar-text">
            La meilleure compagnie aérienne du monde !
        </span>
    </nav>

    <br>

    <?php
        $db = connexbdd();
        displayForms($db);
    ?>
    <div id="totalprice"></div>
    <button type="button" class="btn btn-primary" id="confirm" onclick="confirm()">Confirmation</button>
    <a class="btn btn-primary" href="index.html" id="returnIndex" role="button">Retour à l'accueil</a>
</div>

<br>

<div id="map"></div>

</body>

</html>