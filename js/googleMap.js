// fonctions permettant d'afficher une map personnalisée avec le trajet du vol sélectionné

var map;
var flightPath = null;
var flightPlanCoordinates;

function initMap() {

    //récupération des coordonnées des aéroports
    ajaxRequest("GET", "php/request.php", setMapFlights, "type=coordinates"); //Requête ajax pour demander au serveur les coordonnées des aéroports

}

function setMapFlights(data){

    let json = JSON.parse(data);

    // on calcul la latitude et la longitude moyenne pour centrer la map sur le vol
    let midLng = (parseFloat(json["longitude"]) + parseFloat(json["longitude1"])) / 2;
    let midLat = (parseFloat(json["latitude"]) + parseFloat(json["latitude1"])) / 2;

    // on crée la map grâce à l'api de google map.
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 3.2,
        center: {lat: midLat, lng: midLng},
        mapTypeId: 'terrain'
    });

    if(flightPath != null){
        flightPath.setMap(null);
    }

    // on indique nos coordonnées (aéropor de départ, et aéroport d'arrivée)
    flightPlanCoordinates = [
        {lat: parseFloat(json["latitude"]), lng:  parseFloat(json["longitude"])},
        {lat: parseFloat(json["latitude1"]), lng: parseFloat(json["longitude1"])}
    ];

    // on trace la ligne reliant les points.
    flightPath = new google.maps.Polyline({
        path: flightPlanCoordinates,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });

    // on affiche la map.
    flightPath.setMap(map);
}
