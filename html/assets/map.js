var markerCluster = L.markerClusterGroup();

var markerPerso = L.icon({
    iconUrl: '../assets/ressources/icons/markerPerso.svg',
    shadowUrl: '',

    iconSize:     [38, 95], // size of the icon
    shadowSize:   [50, 64], // size of the shadow
    iconAnchor:   [20, 65], // point of the icon which will correspond to marker's location
    shadowAnchor: [4, 62],  // the same for the shadow
    popupAnchor:  [0, -30] // point from which the popup should open relative to the iconAnchor
});


var map = L.map('map', {
    maxZoom: 19, 
    minZoom:7,
    maxBounds: [
        [52, -7], // Limite ouest (gauche)
        [45, 1]     // Limite est (droite)
    ],
    maxBoundsViscosity: 0.2,
}).setView([48.202047, -2.932644 ],7);
let render_c = false;
let render_m = true;


const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);



//var marker = L.marker([48.580559, -3.825931]).addTo(map);
//marker.bindPopup("").openPopup();

let marqueur =undefined;
let result;
function onClick(e) {
    // Récupérer l'ID du logement depuis l'attribut du marqueur
   var logementId = e.target.options.logementId;

   // Vérifier si l'ID existe
   if (logementId) {
       // Obtenez l'élément HTML correspondant à l'ID du logement
       var logementElement = document.getElementById(logementId);

       // Vérifiez si l'élément existe avant de faire défiler
       if (logementElement) {
           // Faites défiler la page jusqu'à l'élément
           logementElement.scrollIntoView({ behavior: 'smooth' });

           // Vous pouvez également mettre en surbrillance ou effectuer d'autres actions ici si nécessaire
       }
   }
}

function generateRandomPoint(center, radius) {
    var angle = Math.random() * Math.PI * 2;
    var distance = Math.sqrt(Math.random()) * radius;
    var newX = center.lat + (distance / 111111) * Math.cos(angle); // Convertir la distance en degrés de latitude
    var newY = center.lng + (distance / (111111 * Math.cos(center.lat * Math.PI / 180))) * Math.sin(angle); // Convertir la distance en degrés de longitude
    return L.latLng(newX, newY);
}



function calculateDistance(latlng1, latlng2) {
    // Convertir les coordonnées en radians
    var lat1 = latlng1.lat * Math.PI / 180;
    var lat2 = latlng2.lat * Math.PI / 180;
    var lon1 = latlng1.lng * Math.PI / 180;
    var lon2 = latlng2.lng * Math.PI / 180;

    // Rayon de la terre en mètres
    var R = 6371000; 
    
    // Calcul des différences de latitude et de longitude
    var dLat = lat2 - lat1;
    var dLon = lon2 - lon1;

    // Calcul de la distance
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1) * Math.cos(lat2) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var distance = R * c;

    return distance;
}






$.ajax({
    url: 'traitement_map.php',
    method: 'POST',
    data: {},
    success: function(response) {
        result = JSON.parse(response);
        for (logement of result.events ){
            // Créer un marqueur
            var marker = L.marker([logement.coGps][0], {icon: markerPerso, logementId: logement.id}).on('click', onClick);
            marker.bindPopup(
                '<div class="l-popup-content"><img class="image-popup" src="' + logement.photo + '">'
                 + '<h3>' + logement.accroche + '</h3>'
                 + '<p class="lieu">' + logement.ville + ', ' + logement.code_postal + '</p><br>'
                 + '<p class="prix">' + logement.prix + '€ par nuit</p>'
                 + '<a href="../Logements/afficher_logement.php?logement_id=' + logement.id + '"><div class="button-goToAnnonce"><p>Acceder à l\'annonce</p></div></a></div>'
            );
            // Ajouter le marqueur au markerCluster
            markerCluster.addLayer(marker);

            // Créer un cercle
            
            
        }
        // Ajouter le markerCluster à la carte une fois que tous les marqueurs sont ajoutés
        map.addLayer(markerCluster);
    },
    error: function(xhr, status, error) {
        console.error('Erreur', error);
    }
});


function updateMapMarkers(listeLogement) {
    
    console.log("Je supprime les marqueur existants");
    console.log("marker CLuster content :");
    
    markerCluster.eachLayer(layer => {
        console.log(layer)  
        markerCluster.removeLayer(layer);
        
      });
      markerCluster.eachLayer(layer => {
        console.log(layer)  
      });
    map.removeLayer(markerCluster)
   
    console.log("J'ai supprimé les marqueur existants");
    console.log(listeLogement)
    
    

    // Ajoutez les marqueurs pour les logements de la liste
    listeLogement.forEach(function(logement) {

        console.log(logement)
        console.log("un logement ! Voici ses coordonnées :");
        console.log(logement.logement_coordonnees_gps)
        if (logement.logement_coordonnees_gps) {
            let coordonnees = logement.logement_coordonnees_gps.split(','); // faut avoir un tableau avec [x,y] donc j'ai split la chaine en deux 
            var marker = L.marker([coordonnees[0],coordonnees[1]], {icon: markerPerso, logementId: logement.logement_id}).on('click', onClick);
            marker.bindPopup(
                '<div class="l-popup-content"><img class="image-popup" src="' + logement.logement_photo + '">' +
                '<h3>' + logement.logement_accroche + '</h3>' +
                '<p class="lieu">' + logement.logement_ville+ ', ' + logement.logement_code_postal+ '</p><br>' +
                '<p class="prix">' + logement.logement_prix_nuit_base + '€ par nuit</p>' +
                '<a href="../Logements/afficher_logement.php?logement_id=' + logement.logement_id + '"><div class="button-goToAnnonce"><p>Accéder à l\'annonce</p></div></a></div>'
            );


            markerCluster.addLayer(marker);  
            }
    });  
    map.addLayer(markerCluster)
}



// Créez une variable pour stocker les cercles actuellement sur la carte
var circles = [];

map.on('zoomend', function () {
    var zoomLevel = map.getZoom();

    if (zoomLevel > 13) {
        
        markerCluster.eachLayer(function(layer) {
            if (layer instanceof L.Marker) {
                layer.setOpacity(0) 
            }
        });
        if (circles.length === 0) {
            result.events.forEach(function(logement) {
                var center = L.latLng(logement.coGps[0], logement.coGps[1]);
                var radius_c = 300;
                var circle = L.circle(center, {
                    logementId: logement.id,
                    color: 'blue',
                    fillOpacity: 0.3,
                    radius: radius_c
                }).on('click', onClick);
                circle.bindPopup(
                    '<div class="l-popup-content"><img class="image-popup" src="' + logement.photo + '">'
                     + '<h3>' + logement.accroche + '</h3>'
                     + '<p class="lieu">' + logement.ville + ', ' + logement.code_postal + '</p><br>'
                     + '<p class="prix">' + logement.prix + '€ par nuit</p>'
                     + '<a href="../Logements/afficher_logement.php?logement_id=' + logement.id + '"><div class="button-goToAnnonce"><p>Acceder à l\'annonce</p></div></a></div>'
                );
                circle.addTo(map);
                circles.push(circle); // Ajoutez le cercle à la liste des cercles
            });
        }
    } else {

        markerCluster.eachLayer(function(layer) {
            if (layer instanceof L.Marker) {
                layer.setOpacity(1) 
            }
        });
        // Si des cercles sont déjà présents sur la carte, retirez-les
        map.eachLayer(function(layer) {
            if (layer instanceof L.Circle) {
                map.removeLayer(layer);
            }
        });
            circles = []; // Réinitialisez la liste des cercles
        }
    }
);