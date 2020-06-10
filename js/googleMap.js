var map;
var flightPath = null;
var flightPlanCoordinates;

function initMap() {

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 3,
        center: {lat: 0, lng: -180},
        mapTypeId: 'terrain'
    });

    let Coord1 = {
        latitude:"60.818599700927734",
        longitude:"-78.14859771728516",
    };

    let Coord2 = {
        latitude:"52.358898",
        longitude:"-5.018303",
    };

    setMapFlights(Coord1,Coord2);

}

function setMapFlights(coord1,coord2){

    if(flightPath != null){
        flightPath.setMap(null);
    }

    flightPlanCoordinates = [
        {lat: parseFloat(coord1["latitude"]), lng:  parseFloat(coord1["longitude"])},
        {lat: parseFloat(coord2["latitude"]), lng: parseFloat(coord2["longitude"])}
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

function testChange() {
    let Coord1 = {
        latitude:"60.818599700927734",
        longitude:"-78.14859771728516",
    };

    let Coord2 = {
        latitude:"68.223297",
        longitude:"-135.00599",
    };

    setMapFlights(Coord1,Coord2);
}