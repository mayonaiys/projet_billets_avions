<?php

//Définition des constantes
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_PATH', 'mysql:dbname=projetcir2;host=localhost;');


//Connexion base de donnée
function connexbdd(){
    try {
        $bdd = new PDO(DB_PATH, DB_USER, DB_PASSWORD);
        return $bdd;
    } catch (PDOException $e) {
        echo 'Connexion échouée : ' . $e->getMessage();
        return 0;
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

    //Requêtes en fonctions des données insérées par l'utilisateur
    $request = $bdd->prepare('SELECT * FROM flights WHERE originCity=:depCity AND destinationCity=:arrivalCity AND dayOfWeek=:dayOfWeek');
    $request->bindParam(':depCity', $data['depCity'], PDO::PARAM_STR);
    $request->bindParam(':arrivalCity', $data['arrivalCity'], PDO::PARAM_STR);
    $request->bindParam(':dayOfWeek', $day, PDO::PARAM_INT);
    $request->execute();

    $newJson = array();
    $newResponse = "";
    while(($response = $request->fetch())!=0){
        $fareRequest = $bdd->prepare('SELECT fare FROM fares WHERE route=:route AND dateToDeparture=:dateDep AND weFlights=:weFlights AND fare > :minPrice AND fare < :maxPrice');
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
        $fareRequest->bindParam(':minPrice', $data['minPrice'], PDO::PARAM_INT);
        $fareRequest->bindParam(':maxPrice', $data['maxPrice'], PDO::PARAM_INT);
        if($day >= 0 && $day <= 4){
            $weFlights = 0;
        } else {
            $weFlights = 1;
        }
        $fareRequest->bindParam(':weFlights',$weFlights, PDO::PARAM_INT);
        $fareRequest->execute();

        if(($fare = $fareRequest->fetch()['fare'])!=0){
            $temp = '<p style="display: none;">$</p>
                     <tr>
                         <td>'.$response['ID'].'</td>
                         <td>'.$response['route'].'</td>
                         <td>'.$response['dayOfWeek'].'</td>
                         <td>'.$response['departureTime'].'</td>
                         <td>'.$response['arrivalTime'].'</td>
                         <td>'.($fare/2).'</td>
                         <td>'.$fare.'</td>
                         <td>'.($data['nbrAdults']*$fare+$data['nbrChildren']*$fare/2).'</td>
                     </tr>
                     ';
            $newResponse .= $temp;
        }
    }
    echo $newResponse;
}

function resolveDay($day){
    switch ($day) {
        case 0:
            return "Lundi";
        case 1:
            return "Mardi";
        case 2:
            return "Mercredi";
        case 3:
            return "Jeudi";
        case 4:
            return "Vendredi";
        case 5:
            return "Samedi";
        case 6:
            return "dimanche";
    }
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


function getCities($db,$data){

    $data .= "%";

    $request = $db->prepare('SELECT airportCode,city FROM airport WHERE city like :city or airportCode like :aiportCode limit 10');
    $request->bindParam(':city', $data, PDO::PARAM_STR);
    $request->bindParam(':aiportCode', $data, PDO::PARAM_STR);
    $request->execute();


    return $request->fetchAll(PDO::FETCH_ASSOC);
}
/*$json ='{"depCity" : "Edmonton", "arrivalCity" : "Quebec", "nbrAdults" : 5, "nbrChildren" : 2, "depDate" : "2020-06-15", "minPrice" : 100, "maxPrice" : 2000}';
$json2 = '{"ID":"CA184"}';
getAvailableFlights($json);
getChosenFlight($json2);*/

?>