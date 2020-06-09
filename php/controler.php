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

    //Requêtes en fonctions des données insérées par l'utilisateur
    $request = $bdd->prepare('SELECT * FROM flights WHERE originCity=:depCity AND destinationCity=:arrivalCity AND dayOfWeek=:depDate');
    $request->bindParam(':depCity', $data['depCity'], PDO::PARAM_STR);
    $request->bindParam(':arrivalCity', $data['arrivalCity'], PDO::PARAM_STR);
    $request->bindParam(':depDate', $data['depDate'], PDO::PARAM_STR);
    $request->execute();
    while(($response = $request->fetch())!=0){
        echo '<p>'.$response['ID']." | ".$response['dayOfWeek']." | ".$response['departureTime']." | ".$response['arrivalTime'].'</p>';
        $requestFares = $bdd->prepare('SELECT * FROM fares WHERE route=:route');
        $requestFares->bindParam(':route', $response['route'], PDO::PARAM_STR);
        echo $response['route'];
        while (($responseFares = $requestFares->fetch()) != 0){
            echo '<p>test</p>';
        }
    }
}

function test(){

}

$json ='{"depCity" : "Edmonton", "arrivalCity" : "Quebec", "nbrPassengers" : 5, "depDate" : "0", "minPrice" : "min", "maxPrice" : "max"}';
getAvailableFlights($json);

?>