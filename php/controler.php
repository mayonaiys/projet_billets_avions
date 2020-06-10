<?php

//Définition des constantes
define('DB_USER', 'cairline');
define('DB_PASSWORD', 'mdp');
define('DB_PATH', 'mysql:dbname=cairline;host=localhost;');

//Fonction de connexion à la base de données
function connexbdd(){
    try {
        $bdd = new PDO(DB_PATH, DB_USER, DB_PASSWORD); //Établissement de la connexion à la base de données
        return $bdd; //On retourne la base de données
    } catch (PDOException $e) { //Si la connexion a échoué
        echo 'Connexion échouée : ' . $e->getMessage(); //Un message d'erreur est affiché
        return 0; //On ne retourne rien
    }
}

function getAvailableFlights($bdd,$json){

    //Décodage du fichier json
    $data = json_decode($json,true);

    //Récupération de la date du jour
    $date = getdate();
    $date = "".$date['year']."-".$date['mon']."-".$date['mday'];
    $date = new DateTime($date);
    $dateDeparture = $data['depDate'];
    $dateDeparture = new DateTime($dateDeparture);
    $interval = $dateDeparture->diff($date);
    $interval =  $interval->days;

    //On cherche à quel jour de la semaine correspond la date demandée par le client
    $dateDeparture = $data['depDate'];
    list($yDep,$mDep,$dDep) = explode("-", $dateDeparture);
    $timeStamp = mktime(0,0,0,$mDep,$dDep,$yDep);
    $day = date('w', $timeStamp)-1;
    $dateDeparture = convertDate($day,$dDep,$mDep,$yDep);

    //Requêtes en fonctions des données insérées par l'utilisateur
    $request = $bdd->prepare('SELECT ID, route, departureTime, arrivalTime FROM flights WHERE originAirport=:depAirport AND destinationAirport=:arrivalAirport AND dayOfWeek=:dayOfWeek');
    $request->bindParam(':depAirport', $data['depAirport'], PDO::PARAM_STR);
    $request->bindParam(':arrivalAirport', $data['arrivalAirport'], PDO::PARAM_STR);
    $request->bindParam(':dayOfWeek', $day, PDO::PARAM_INT);
    $request->execute();

    $newResponse = "";
    while(($response = $request->fetch())!=0){
        $fareRequest = $bdd->prepare('SELECT f.fare, a1.surcharge as sDep, a2.surcharge as sArrival FROM fares f, airportsurcharges a1, airportsurcharges a2 WHERE f.route=:route AND f.dateToDeparture=:dateDep AND f.weFlights=:weFlights AND f.fare > :minPrice AND f.fare < :maxPrice AND a1.airportCode=:depAirport AND a2.airportCode=:arrivalAirport');
        $fareRequest->bindParam(':route', $response['route'], PDO::PARAM_STR);
        if($interval == 0 ){
            $interval = 0;
        } else if($interval <= 3) {
            $interval = 3;
        } else if($interval <= 10) {
            $interval = 10;
        } else if($interval <= 21) {
            $interval = 21;
        }

        $fareRequest->bindParam(':dateDep', $interval, PDO::PARAM_INT);
        if($day >= 0 && $day <= 4){
            $weFlights = 0;
        } else {
            $weFlights = 1;
        }
        $fareRequest->bindParam(':weFlights',$weFlights, PDO::PARAM_INT);
        $fareRequest->bindParam(':minPrice', $data['minPrice'], PDO::PARAM_INT);
        $fareRequest->bindParam(':maxPrice', $data['maxPrice'], PDO::PARAM_INT);
        $fareRequest->bindParam(':depAirport', $data['depAirport'], PDO::PARAM_STR);
        $fareRequest->bindParam(':arrivalAirport', $data['arrivalAirport'], PDO::PARAM_STR);
        $fareRequest->execute();

        if(($fareResponse = $fareRequest->fetch())!=0){
            $fareWithTaxes = $fareResponse['fare'] + $fareResponse['sDep'] + $fareResponse['sArrival'];
            $temp = '<p style="display: none;">$</p>
                     <tr>
                         <td style="text-align: center;">'.$response['ID'].'</td>
                         <td style="text-align: center;">'.$response['route'].'</td>
                         <td style="text-align: center;">'.$dateDeparture.'</td>
                         <td style="text-align: center;">'.$response['departureTime'].'</td>
                         <td style="text-align: center;">'.$response['arrivalTime'].'</td>
                         <td style="text-align: center;">'.($fareWithTaxes/2)."€".'</td>
                         <td style="text-align: center;">'.$fareWithTaxes."€".'</td>
                         <td style="text-align: center;">'.($data['nbrAdults']*$fareWithTaxes+$data['nbrChildren']*$fareWithTaxes/2)."€".'</td>
                     </tr>';
            $newResponse .= $temp;
        }
    }
    return $newResponse;
}

//Fonction de conversion de la date
function convertDate($dayWeek,$dayDep,$monthDep,$yearDep){

    $newDate = "";
    switch ($dayWeek) {
        case 0:
            $newDate .= "Lundi ";
            break;
        case 1:
            $newDate .= "Mardi ";
            break;
        case 2:
            $newDate .= "Mercredi ";
            break;
        case 3:
            $newDate .= "Jeudi ";
            break;
        case 4:
            $newDate .= "Vendredi ";
            break;
        case 5:
            $newDate .= "Samedi ";
            break;
        case 6:
            $newDate .= "Dimanche ";
            break;
    }
    $newDate .= $dayDep." ";

    switch ((int)$monthDep) {
        case 1:
            $newDate .= "Janvier ";
            break;
        case 2:
            $newDate .= "Février ";
            break;
        case 3:
            $newDate .= "Mars ";
            break;
        case 4:
            $newDate .= "Avril ";
            break;
        case 5:
            $newDate .= "Mai ";
            break;
        case 6:
            $newDate .= "Juin ";
            break;
        case 7:
            $newDate .= "Juillet ";
            break;
        case 8:
            $newDate .= "Aout ";
            break;
        case 9:
            $newDate .= "Septembre ";
            break;
        case 10:
            $newDate .= "Octobre ";
            break;
        case 11:
            $newDate .= "Novembre ";
            break;
        case 12:
            $newDate .= "Décembre ";
            break;
    }
    $newDate .= $yearDep;
    return $newDate;
}

//Fonction de récupération des informations de l'avion choisi
function getChosenFlight($json){

    //Décodage du fichier json
    $data = json_decode($json,true);

    //Connexion à la base de données
    $bdd = connexbdd(DB_PATH,DB_USER,DB_PASSWORD);

    //Récupération des informations du vol
    $request = $bdd->prepare('SELECT * FROM flights WHERE ID=:id');
    $request->bindParam(':id',$data['ID'], PDO::PARAM_INT);
    $request->execute();
    $flight = $request->fetch();
    $newJson = array("ID"=>$flight['ID'],"originAirport"=>$flight['originAirport'],"destinationAirport"=>$flight['destinationAirport'],"originCity"=>$flight['originCity'],"destinationCity"=>$flight['destinationCity'],"date"=>"date","departureTime"=>$flight['departureTime'],"arrivalTime"=>$flight['arrivalTime']);
}

//Fonction de récupération des villes pour la prédiction
function getCities($db,$data){

    $data .= "%";

    $request = $db->prepare('SELECT airportCode,city FROM airport WHERE city like :city or airportCode like :aiportCode limit 10');
    $request->bindParam(':city', $data, PDO::PARAM_STR);
    $request->bindParam(':aiportCode', $data, PDO::PARAM_STR);
    $request->execute();


    return $request->fetchAll(PDO::FETCH_ASSOC);
}

//Fonction de récupération du prix minimum et maximum des billets
function getPriceRange($db){
    $request = $db->prepare('SELECT MIN(fare) as min,MAX(fare) as max FROM fares');
    $request->execute();

    return $request->fetch(PDO::FETCH_ASSOC);
}

?>