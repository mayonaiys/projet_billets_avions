<?php

//Définition des constantes
define('DB_USER', 'cairline');
define('DB_PASSWORD', 'mdp');
define('DB_PATH', 'mysql:dbname=cairline;host=localhost;');


//Connexion base de donnée
function connexbdd($base,$user,$password){
    try {
        $bdd = new PDO($base, $user, $password);
        return $bdd;
    } catch (PDOException $e) {
        echo 'Connexion échouée : ' . $e->getMessage();
        return 0;
    }
}

function getAvailableFlights($json){

    //Décodage du fichier json
    $data = json_decode($json,true);

    //Connexion à la base de données
    $bdd = connexbdd(DB_PATH,DB_USER,DB_PASSWORD);

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
    echo $day;


    //Requêtes en fonctions des données insérées par l'utilisateur
    $request = $bdd->prepare('SELECT * FROM flights WHERE originCity=:depCity AND destinationCity=:arrivalCity AND dayOfWeek=:dayOfWeek');
    $request->bindParam(':depCity', $data['depCity'], PDO::PARAM_STR);
    $request->bindParam(':arrivalCity', $data['arrivalCity'], PDO::PARAM_STR);
    $request->bindParam(':dayOfWeek', $day, PDO::PARAM_INT);
    $request->execute();
    echo " ".$interval;

    $newJson = array();
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
            echo '<p>'.$response['ID']." | ".$response['route']." | ".resolveDay($response['dayOfWeek'])." | ".$response['departureTime']." | ".$response['arrivalTime']." | ".($fare/2)." | ".$fare." | ".($data['nbrAdults']*$fare+$data['nbrChildren']*$fare/2).'</p>';
            $array = array("ID"=>$response['ID'],"route"=>$response['route'],"date"=>resolveDay($response['dayOfWeek']),"depTime"=>$response['departureTime'],"arrivalTime"=>$response['arrivalTime'],"childrenFare"=>($fare/2),"adultsFare"=>$fare,"totalFare"=>($data['nbrAdults']*$fare+$data['nbrChildren']*$fare/2));
            array_push($newJson,$array);
        }
    }
    $newData = json_encode($newJson); //Encodage de la réponse
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

$json ='{"depCity" : "Edmonton", "arrivalCity" : "Quebec", "nbrAdults" : 5, "nbrChildren" : 2, "depDate" : "2020-06-15", "minPrice" : 100, "maxPrice" : 2000}';
getAvailableFlights($json);

?>