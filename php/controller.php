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

    //Sauvegarde du namebre de passagers via les sessions
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
    $day = date('w', $timeStamp);

    //Requêtes en fonctions des données insérées par l'utilisateur
    $request = $bdd->prepare('SELECT ID, route, departureTime, arrivalTime FROM flights WHERE originAirport=:depAirport AND destinationAirport=:arrivalAirport AND dayOfWeek=:dayOfWeek');
    $request->bindParam(':depAirport', $data['depAirport'], PDO::PARAM_STR);
    $request->bindParam(':arrivalAirport', $data['arrivalAirport'], PDO::PARAM_STR);
    $request->bindParam(':dayOfWeek', $day, PDO::PARAM_INT);
    $request->execute();

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
        if($day >= 1 && $day <= 5){
            $weFlights = 0;
        } else if($day == 0 || $day == 6){
            $weFlights = 1;
        }
        $fareRequest->bindParam(':weFlights',$weFlights, PDO::PARAM_INT);
        $fareRequest->bindParam(':minPrice', $data['minPrice'], PDO::PARAM_INT);
        $fareRequest->bindParam(':maxPrice', $data['maxPrice'], PDO::PARAM_INT);
        $fareRequest->execute();

        if(($fareResponse = $fareRequest->fetch())!=0){
            //Sauvegarde du tarif et des charges via les sessions
            $_SESSION['fare']=$fareResponse['fare'];
            $_SESSION['charges']=0;

            //Récupération des surcharges s'il y en a
            $surchargesDep=$bdd->prepare('SELECT surcharge FROM airportsurcharges WHERE airportCode=:code');
            $surchargesDep->bindParam(':code',$data['depAirport'],PDO::PARAM_STR);
            $surchargesDep->execute();
            if(!empty(($surchargesDep = $surchargesDep->fetch()))){
                $_SESSION['charges']= $_SESSION['charges'] +$surchargesDep['surcharge'];
            }

            $surchargesArrival=$bdd->prepare('SELECT surcharge FROM airportsurcharges WHERE airportCode=:code');
            $surchargesArrival->bindParam(':code',$data['arrivalAirport'],PDO::PARAM_STR);
            $surchargesArrival->execute();
            if(!empty(($surchargesArrival = $surchargesArrival->fetch()))){
                $_SESSION['charges']= $_SESSION['charges']+$surchargesArrival['surcharge'];
            }

            $fareWithTaxes = $_SESSION['fare'] + $_SESSION['charges'];
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
                                <label style="margin-right: 10px;">nom</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="name'.$i.'" placeholder="Entrez un nom">
                                <label style="margin-right: 10px;">Email</label>
                                <input type="email" class="form-control mb-2 mr-sm-2" id="mail'.$i.'" placeholder="Entrez un email">
                                <label style="margin-right: 10px;">Date de naissance</label>
                                <input type="date" class="form-control mb-2 mr-sm-2" id="date'.$i.'" value="" min="1920-01-01" max="">
                                <label id="price'.$i.'" style="display: none"></label>
                            </form>
                            <div id="billet'.$i.'"></div>
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
        $name=$data[$i]["name"];
        $firstname=$data[$i]["firstname"];
        $mail=$data[$i]["mail"];
        $date=$data[$i]["date"];

        $request=$bdd->prepare("SELECT profile_id FROM profile WHERE firstname=:firstname AND name=:name AND mail=:mail");
        $request->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $request->bindParam(':name', $name, PDO::PARAM_STR);
        $request->bindParam(':mail', $mail, PDO::PARAM_STR);
        $request->execute();
        $result=$request->fetch();

        if(empty($result))
        {
            $add=$bdd->prepare("INSERT INTO profile (firstname, name, mail, birth) VALUES (:firstname,:name,:mail,:birth)");
            $add->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $add->bindParam(':name', $name, PDO::PARAM_STR);
            $add->bindParam(':mail', $mail, PDO::PARAM_STR);
            $add->bindParam(':birth', $date, PDO::PARAM_STR);

            $add->execute();

            //On récupère le dernier id ajouté à la table profile et on l'enregistre dans le tableau d'id
            $id = $bdd->lastInsertId();
            $profile_list .= $id." ";
        }
        else
        {
            //Si la personne est déjà existante on push dans le tab d'id, l'id trouvé par la requête
            $profile_list .= $result['profile_id']." ";
        }
    }

    $_SESSION['profile_list']=$profile_list;

    saveBooking($bdd);
}

function saveBooking($bdd){
    $add = $bdd->prepare("INSERT INTO booking (date, flight_id, profile_list) VALUES (:date,:flight_id,:profile_list)");

    $add->bindParam(':date', $_SESSION['flight_date'], PDO::PARAM_STR);
    $add->bindParam(':flight_id', $_SESSION['flightID'], PDO::PARAM_STR);
    $add->bindParam(':profile_list', $_SESSION['profile_list'], PDO::PARAM_STR);

    $add->execute();

    $_SESSION['booking_id'] = $bdd->lastInsertId();
}

function showPrice($json){
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
        $fare = (float)$_SESSION['fare'];
        $charges = (float)$_SESSION['charges'];
        if($interval<1460){
            $fare = $fare/2;
            $charges = $charges/2;
        }
        if(isset($_SESSION['discount'])){
            $fare=$fare - (($_SESSION['discount']/100)*$fare);
            $charges=$charges - (($_SESSION['discount']/100)*$charges);
            $_SESSION['discount']=null;
        }
        array_push($temp,$fare,$charges);
        array_push($faresArray,$temp);
    }
    $json = json_encode($faresArray);
    return $json;
}

function login($bdd,$json){
    //Décodage du fichier json
    $data = json_decode($json,true);

    $user = $bdd->prepare('SELECT * FROM profile WHERE mail=:mail');
    $user->bindParam(':mail', $data['mail'], PDO::PARAM_STR);
    $user->execute();
    $infos = $user->fetch();
    if(password_verify($data['password'],$infos['password'])){
        $_SESSION['profile_id']=$infos['profile_id'];
        $_SESSION['firstname']=$infos['firstname'];
        return "connected";
    } else {
        return "error";
    }
}

function register($bdd,$json){
    //Décodage du fichier json
    $data = json_decode($json,true);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $addUser = $bdd->prepare('SELECT * FROM profile WHERE mail=:mail AND firstname=:firstname AND name=:name');
    $addUser->bindParam(':mail',$data['mail'], PDO::PARAM_STR);
    $addUser->bindParam(':firstname',$data['firstname'], PDO::PARAM_STR);
    $addUser->bindParam(':name',$data['name'], PDO::PARAM_STR);
    $addUser->execute();

    if(($user = $addUser->fetch())!=0){
        if($user['password']==null){
            $addPassword = $bdd->prepare('UPDATE profile SET password=:password WHERE mail=:mail AND firstname=:firstname AND name=:name');
            $addPassword->bindParam(':password',$password, PDO::PARAM_STR);
            $addPassword->bindParam(':mail',$data['mail'], PDO::PARAM_STR);
            $addPassword->bindParam(':firstname',$data['firstname'], PDO::PARAM_STR);
            $addPassword->bindParam(':name',$data['name'], PDO::PARAM_STR);
            $addPassword->execute();
            $_SESSION['profile_id']=$user['profile_id'];
            $_SESSION['firstname']=$user['firstname'];
            $response = "registered";
        } else {
            $response = "alreadyexist";
        }
    } else {
        $add=$bdd->prepare("INSERT INTO profile (firstname, name, mail, birth, password) VALUES (:firstname,:name,:mail,:birth,:password)");
        $add->bindParam(':firstname',  $data['firstname'], PDO::PARAM_STR);
        $add->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $add->bindParam(':mail', $data['mail'], PDO::PARAM_STR);
        $add->bindParam(':birth', $data['birthdate'], PDO::PARAM_STR);
        $add->bindParam(':password', $password, PDO::PARAM_STR);
        $add->execute();

        $_SESSION['profile_id']=$user['profile_id'];
        $_SESSION['firstname']=$user['firstname'];
        $response = "registered";
    }
    return $response;
}

function getBooking($bdd){
    $booking=$bdd->prepare("SELECT * FROM booking");
    $booking->execute();

    $response = "";
    while(($temp = $booking->fetch())!=0){
        $ids=explode(" ",$temp['profile_list']);
        for($i = 0; $i < count($ids); $i++){
            if($_SESSION['profile_id']==(int)$ids[$i]){
                $flight = $bdd->prepare('SELECT route,departureTime,arrivalTime FROM flights WHERE ID=:flightID');
                $flight->bindParam(':flightID',$temp['flight_id'], PDO::PARAM_STR);
                $flight->execute();
                $flight = $flight->fetch();

                $tempRep = '<tr>
                                <td style="text-align: center;">' .$temp['flight_id'].'</td>
                                <td style="text-align: center;">'.$flight['route'].'</td>
                                <td style="text-align: center;">'.$temp['date'].'</td>
                                <td style="text-align: center;">'.$flight['departureTime'].'</td>
                                <td style="text-align: center;">'.$flight['arrivalTime'].'</td>
                                <td style="text-align: center;">test</td>
                            </tr>';
                $response .= $tempRep;
            }
        }
    }
    return $response;
}

function getClientInfo($db,$id){
    $request= $db->prepare("SELECT * FROM profile WHERE profile_id=:profile_id");
    $request->bindParam(':profile_id', $id, PDO::PARAM_INT);

    $request->execute();

    return $request->fetch();
}

function getFlightInfo($db,$id){

    $request= $db->prepare("SELECT flights.*,air1.*,air2.airportCode as airportCode2,air2.city as city2,air2.latitude as latitude2,air2.longitude as longitude2 FROM flights INNER JOIN airport air1 ON flights.originAirport = air1.airportCode INNER JOIN airport air2 ON flights.destinationAirport=air2.airportCode WHERE flights.ID=:ID");
    $request->bindParam(':ID', $id, PDO::PARAM_INT);

    $request->execute();

    return $request->fetch();
}

function getRandomFlights($bdd){
    $randomFlights = $bdd->prepare('SELECT * FROM flights ORDER BY RAND() LIMIT 3');
    $randomFlights->execute();
    $response = [];
    while(($flight = $randomFlights->fetch())!=0){

        $dateToDeparture = getDateToDeparture();
        $today = date('Y-m-d');
        $tempDate = date('w',strtotime("$today +$dateToDeparture day")); //Jour de la semaine

        $difference = $flight['dayOfWeek']-$tempDate;
        $newDateToDeparture = $dateToDeparture+$difference;
        $dateDep = date('Y-m-d',strtotime("$today +$newDateToDeparture day"));

        $cities = $bdd->prepare('SELECT a1.city as dep, a2.city as arrival FROM airport a1, airport a2 WHERE a1.airportcode=:depCode AND a2.airportcode=:arrCode');
        $cities->bindParam(':depCode',$flight['originAirport'],PDO::PARAM_STR);
        $cities->bindParam(':arrCode',$flight['destinationAirport'],PDO::PARAM_STR);
        $cities->execute();
        $cities = $cities->fetch();

        if($newDateToDeparture<$dateToDeparture){
            if($dateToDeparture==10){
                $dateToDeparture=21;
            }
        }

        $fare = $bdd->prepare('SELECT fare FROM fares WHERE route=:route AND dateToDeparture=:dateToDeparture');
        $fare->bindParam(':route',$flight['route'],PDO::PARAM_STR);
        $fare->bindParam(':dateToDeparture',$dateToDeparture,PDO::PARAM_STR);
        $fare->execute();
        $fare = $fare->fetch()['fare'];

        $discount = random_int(10,50);
        $discountedFare = $fare - (($discount/100)*$fare);
        $_SESSION['fare'] = $discountedFare;
        $_SESSION['charges']=0;

        //Récupération des surcharges s'il y en a
        $surchargesDep=$bdd->prepare('SELECT surcharge FROM airportsurcharges WHERE airportCode=:code');
        $surchargesDep->bindParam(':code',$flight['originAirport'],PDO::PARAM_STR);
        $surchargesDep->execute();
        if(!empty(($surchargesDep = $surchargesDep->fetch()))){
            $_SESSION['charges']= $_SESSION['charges'] +$surchargesDep['surcharge'];
        }

        $surchargesArrival=$bdd->prepare('SELECT surcharge FROM airportsurcharges WHERE airportCode=:code');
        $surchargesArrival->bindParam(':code',$flight['destinationAirport'],PDO::PARAM_STR);
        $surchargesArrival->execute();
        if(!empty(($surchargesArrival = $surchargesArrival->fetch()))){
            $_SESSION['charges']= $_SESSION['charges']+$surchargesArrival['surcharge'];
        }

        $temp = '<div class="card-body">
                     <h5 class="card-title">Bon plan !</h5>
                     <p class="card-text">'.(string)$discount." ".$flight['ID'].'% de réduction sur un vol '.$cities['dep']." [".$flight['originAirport']."] -> ".$cities['arrival']." [".$flight['destinationAirport'].'] le '.$dateDep.': <strong>'.$discountedFare.'€ (TCC)</strong> au lieu de <strong>'.$fare.'€ (TTC)</strong></p>
                     <input class="btn btn-success" type="button" value="J\'en profite!" onclick="selectDiscountFlight(\''.$flight['ID'].'\','.$discount.')">
                 </div>';
        array_push($response,$temp);
    }
    $response=json_encode($response);
    return $response;
}

function getDateToDeparture(){
    $tab = [10,21];
    $rdm = random_int(0,1);
    return $tab[$rdm];
}

?>