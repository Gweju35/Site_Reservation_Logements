<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte Interactive</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>
<body>

<div id="map" style="height: 400px;"></div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet-src.js"></script>

<script>
    var mymap = L.map('map').setView([48.733333,  -3.466667], 13);
    let circle;
    let marker;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mymap);

    function addCircle(latlng) {
        if (circle) {
            // Mettre à jour la position du cercle existant
            circle.setLatLng(latlng);
        } else {
            // Ajouter un cercle à l'emplacement spécifié
            circle = L.circle(latlng, {
                color: 'blue',
                fillColor: 'blue',
                fillOpacity: 0.3,
                radius: 600  // Ajustez le rayon selon vos besoins
            }).addTo(mymap);
            
        }
    }

    function addMarker(latlng) {
        if (marker) {
            // Mettre à jour la position du marqueur existant
            marker.setLatLng(latlng);
            var position = marker.getLatLng();
            console.log('Nouvelles coordonnées du marqueur222: ' + position.lat + ', ' + position.lng);
        } else {
            // Ajouter un marqueur à l'emplacement spécifié
            marker = L.marker(latlng, { draggable: false }).addTo(mymap);

            // Récupérer les coordonnées du marqueur
            var position = marker.getLatLng();
            console.log('Coordonnées du marqueur: ' + position.lat + ', ' + position.lng);
        }
    }

    mymap.on('click', function (event) {
        var latlng = event.latlng;
        console.log("Event coordonates :"+latlng.lat+', '+latlng.lng);
        //console.log(circle);
        // Supprimer le cercle ou le marqueur existant en fonction du niveau de zoom
        if (mymap.getZoom() > 12) {
            if (circle instanceof L.Circle) {
                //console.log("Zoom trop grand le cercle existe");
                mymap.removeLayer(circle);
                circle = undefined; // Réinitialiser la variable
            }
            addCircle(latlng);
        } else {
            if (marker instanceof L.Marker) {
                //console.log("Zoom trop petit le marqueur existe");
                mymap.removeLayer(marker);
                marker = undefined; // Réinitialiser la variable
            }
            addMarker(latlng);
        }
    });

    mymap.on('zoomend', function () {
        // Supprimer le cercle ou le marqueur existant en fonction du niveau de zoom
        console.log("Je zoom bouffon");
        console.log(circle);
        console.log(marker);

    // Ajoutez d'autres vérifications pour d'autres types de couches si nécessaire

        
        if (mymap.getZoom() <= 10 && circle instanceof L.Circle) {
            console.log("Zoom trop grand le cercle existe");
            var mycoord = circle.getLatLng();
            mymap.removeLayer(circle);
            circle = undefined; // Réinitialiser la variable
            addMarker(mycoord)
            
        } else if (mymap.getZoom() > 10 && marker instanceof L.Marker) {
            console.log("Zoom trop petit le marqueur existe");
            var mycoord = marker.getLatLng();
            mymap.removeLayer(marker);
            marker = undefined; // Réinitialiser la variable
            addCircle(mycoord);
        }
    });
    
</script>

</body>
</html>
