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

    //Sauvegarde du nombre de passagers via les sessions
    $_SESSION['nbPassengers'] = $data['nbrAdults']+$data['nbrChildren'];

    //Sauvegarde de la date de départ via les sessions
    $_SESSION['flight_date'] = $data['depDate'];


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
            //Sauvegarde du tarif et des charges via les sessions
            $_SESSION['fare']=$fareResponse['fare'];
            $_SESSION['charges']=$fareResponse['sDep'] + $fareResponse['sArrival'];

            $fareWithTaxes = $fareResponse['fare'] + $fareResponse['sDep'] + $fareResponse['sArrival'];
            $temp = '<p style="display: none;">$</p>
                     <tr id="'.$response['ID'].'" onclick="selectFlight(\''.$response['ID'].'\')">
                         <td style="text-align: center;">' .$response['ID'].'</td>
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

//Fonction de récupération des informations de l'avion choisi
function displayForms($bdd){
    //Récupération des informations du vol
    $request = $bdd->prepare('SELECT f.originAirport, f.destinationAirport, f.departureTime, f.arrivalTime, a1.city as depCity, a2.city as arrivalCity FROM flights f, airport a1, airport a2 WHERE f.ID=:flightID AND a1.airportCode=f.originAirport AND a2.airportCode=f.destinationAirport');
    $request->bindParam(':flightID',$_SESSION['flightID'], PDO::PARAM_STR);
    $request->execute();
    $flight = $request->fetch();

    setlocale (LC_TIME, 'fr_FR');
    $aff_date = strftime("%A %d %B %Y",strtotime($_SESSION['flight_date']));

    $newReponse = "";
    for($i = 1; $i < $_SESSION['nbPassengers']+1; $i++){
        $temp = '<div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Passager n°'.$i.' - VOL '.$_SESSION['flightID'].' : '.$flight['depCity'].' ['.$flight['originAirport'].'] ->  '.$flight['arrivalCity'].' ['.$flight['destinationAirport'].'], départ à '.$flight['departureTime'].', arrivée à '.$flight['arrivalTime'].' le '.$aff_date.'. </h5>
                            <form class="form-inline">
                                <label style="margin-right: 10px;">Prénom</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="firstname'.$i.'" placeholder="Entrez un prénom">
                                <label style="margin-right: 10px;">Nom</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="name'.$i.'" placeholder="Entrez un nom">
                                <label style="margin-right: 10px;">Email</label>
                                <input type="email" class="form-control mb-2 mr-sm-2" id="mail'.$i.'" placeholder="Entrez un email">
                                <label style="margin-right: 10px;">Date de naissance</label>
                                <input type="date" class="form-control mb-2 mr-sm-2" id="date'.$i.'" value="" min="1920-01-01" max="">
                                <label id="price'.$i.'" style="display: none"></label>
                            </form>
                    </div>
                </div>
                <br>';
        $newReponse .= $temp;
    }

    echo $newReponse;
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

//Fonction d'édition de la table client
function editClients($bdd,$json){
    //Décodage du fichier json
    $data = json_decode($json,true);

    //Tableau pour récupérer les ids et les return à la fin de la fonction
    $profile_list = "";

    //Pour les N passagers on va tester si leurs infos sont déjà dans la BDD, si c'est pas le cas on les ajoute
    for($i=0; $i< sizeof($data); $i++){
        $nom=$data[$i]["name"];
        $prenom=$data[$i]["firstname"];
        $mail=$data[$i]["mail"];
        $date=$data[$i]["date"];

        $request=$bdd->prepare("SELECT profile_id FROM profile WHERE prenom=:prenom AND nom=:nom AND mail=:mail AND birth=:date");
        $request->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $request->bindParam(':nom', $nom, PDO::PARAM_STR);
        $request->bindParam(':mail', $mail, PDO::PARAM_STR);
        $request->bindParam(':date', $date, PDO::PARAM_STR);
        $request->execute();

        $result=$request->fetch();

        if(empty($result))
        {
            $add=$bdd->prepare("INSERT INTO profile (prenom, nom, mail, birth) VALUES (:prenom,:nom,:mail,:birth)");
            $add->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $add->bindParam(':nom', $nom, PDO::PARAM_STR);
            $add->bindParam(':mail', $mail, PDO::PARAM_STR);
            $add->bindParam(':birth', $date, PDO::PARAM_STR);

            $add->execute();

            //On récupère le dernier id ajouté à la table profile et on l'enregistre dans le tableau d'id
            $id = $bdd->lastInsertId();
            $profile_list .= "|".$id."|";
        }
        else
        {
            //Si la personne est déjà existante on push dans le tab d'id, l'id trouvé par la requête
            $profile_list .= "|".$result['profile_id']."|";
        }
    }

    $_SESSION['profile_list']=$profile_list;

    saveBooking($bdd);
}

function saveBooking($bdd){
    $add = $bdd->prepare("INSERT INTO reservation (date, flight_id, profile_list) VALUES (:date,:flight_id,:profile_list)");

    $add->bindParam(':date', $_SESSION['flight_date'], PDO::PARAM_STR);
    $add->bindParam(':flight_id', $_SESSION['flightID'], PDO::PARAM_STR);
    $add->bindParam(':profile_list', $_SESSION['profile_list'], PDO::PARAM_STR);

    $add->execute();

}

function showPrice($bdd,$json){
    //Décodage du fichier json
    $data = json_decode($json,true);
    $faresArray = array();
    for($i=0; $i< sizeof($data); $i++){
        $date = getdate();
        $date = "".$date['year']."-".$date['mon']."-".$date['mday'];
        $date = new DateTime($date);
        $birthDate = $data[$i]['date'];
        $birthDate = new DateTime($birthDate);
        $interval = $birthDate->diff($date);
        $interval =  $interval->days;
        $temp = array();
        $fare = $_SESSION['fare'];
        $charges = $_SESSION['charges'];
        if($interval<1460){
            $fare = $fare/2;
        }
        array_push($temp,$fare,$charges);
        array_push($faresArray,$temp);
    }
    $json = json_encode($faresArray);
    return $json;
}

?>