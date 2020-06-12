var map;
var flightPath = null;
var flightPlanCoordinates;

function initMap() {

    ajaxRequest("GET", "php/request.php", setMapFlights, "type=coordinates"); //Requête ajax pour demander au serveur les coordonnées des aéroports

}

function setMapFlights(data){

    let json = JSON.parse(data);

    let midLng = (parseFloat(json["longitude"]) + parseFloat(json["longitude1"])) / 2;
    let midLat = (parseFloat(json["latitude"]) + parseFloat(json["latitude1"])) / 2;


    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 3.2,
        center: {lat: midLat, lng: midLng},
        mapTypeId: 'terrain'
    });

    if(flightPath != null){
        flightPath.setMap(null);
    }

    flightPlanCoordinates = [
        {lat: parseFloat(json["latitude"]), lng:  parseFloat(json["longitude"])},
        {lat: parseFloat(json["latitude1"]), lng: parseFloat(json["longitude1"])}
    ];
    flightPath = new google.maps.Polyline({
        path: flightPlanCoordinates,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });

    flightPath.setMap(map);
}
