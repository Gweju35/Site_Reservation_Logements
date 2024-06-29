function addToLocalStorage(key, value) {
    localStorage.setItem(key, value);
}



window.onload = filtrerLogements();



function filtrerLogementsCP(liste, valeur) {
    return liste.filter(logement => logement.logement_code_postal.toString().slice(0, 2) === valeur.toString());
}

function filtrerLogementsCPV2(liste, valeurs) {
    if (valeurs.length === 0) {
        return liste; // Retourne la liste non filtrée si valeurs est vide
    } else {
        return liste.filter(logement => valeurs.includes(parseInt(logement.logement_code_postal.toString().slice(0, 2))));
    }
}





// Fonction pour filtrer les logements en fonction d'une plage de prix
function filtrerLogementsParPrix(liste, prixMin, prixMax) {
    return liste.filter(logement => parseInt(logement.logement_prix_nuit_base) >= prixMin && parseInt(logement.logement_prix_nuit_base) <= prixMax);
}

// Fonction pour filtrer les logements en fonction du nombre de personnes entré
function filtrerLogementsParNombrePersonnes(liste, nombrePersonnes) {
    return liste.filter(logement => nombrePersonnes <= logement.logement_personne_max);
}

function filtrerLogementsOPtions(liste, options) {
    let filtres = [];

    // Parcourir les options et construire les filtres en fonction des clés et des valeurs
    for (const key in options) {
        if (options[key] === 'true') { // Comparaison avec des chaînes de caractères
            filtres.push(logement => logement[key] === true);
        }
    }

    // Si aucun filtre n'est sélectionné, retourner la liste non filtrée
    if (filtres.length === 0) {
        return liste;
    } else {
        // Appliquer les filtres à la liste de logements
        return liste.filter(logement => filtres.every(filtre => filtre(logement)));
    }
}

function resetFilter() {
    //Supprimer toutes les variables de filtre dans le localStorage
    localStorage.clear()

    
}







function filtrerLogements() {
    console.time("filtre");
    var listeLogement = [];
    $.ajax({
        url: 'traitementFiltres.php',
        method: 'POST',
        data: {},
        success: function (response) {
            var result = JSON.parse(response);

            document.getElementById('listeLogements').innerHTML = '';
            listeLogement = [];



            if (isNaN(localStorage.getItem('prixMin'))) {
                addToLocalStorage('prixMin', 0) // Valeur par défaut ou une valeur appropriée
            }
            if (isNaN(localStorage.getItem('prixMax'))) {
                addToLocalStorage('prixMax', Infinity) // Valeur par défaut ou une valeur appropriée
            }


            // Récupérer les valeurs des input prix
            //var prixMin = parseFloat(document.querySelector('input[name="prix_min"]').value);
            //var prixMax = parseFloat(document.querySelector('input[name="prix_max"]').value);

            addToLocalStorage('prixMin', parseFloat(document.querySelector('input[name="prix_min"]').value))
            addToLocalStorage('prixMax', parseFloat(document.querySelector('input[name="prix_max"]').value))


            // Récupérer les valeurs des input nbr de personnes 
            //var pers = parseFloat(document.querySelector('input[name="pers_min"]').value);
            addToLocalStorage('pers', document.querySelector('input[name="pers_min"]').value)

            // Récupérer les valeurs des checkboxes departements
            //var choixCoteArmor = document.querySelector('input[name="choix_cotedarmor"]').checked;
            //var choixFinistere = document.querySelector('input[name="choix_finistere"]').checked;
            //var choixIlleEtVillaine = document.querySelector('input[name="choix_illeetvilaine"]').checked;
            //var choixMorbihan = document.querySelector('input[name="choix_morbihan"]').checked;
            addToLocalStorage('choixCoteArmor', document.querySelector('input[name="choix_cotedarmor"]').checked);
            addToLocalStorage('choixFinistere', document.querySelector('input[name="choix_finistere"]').checked);
            addToLocalStorage('choixIlleEtVillaine', document.querySelector('input[name="choix_illeetvilaine"]').checked);
            addToLocalStorage('choixMorbihan', document.querySelector('input[name="choix_morbihan"]').checked);
            // Récupérer les valeurs des checkboxes equipements
            // var choixTv = document.querySelector('input[name="choix_tv"]').checked;
            //var choixMachineALaver = document.querySelector('input[name="choix_machine_a_laver"]').checked;
            //var choixLaveVaisselle = document.querySelector('input[name="choix_lave_vaiselle"]').checked;
            //var choixWifi = document.querySelector('input[name="choix_wifi"]').checked;
            addToLocalStorage('choixTv', document.querySelector('input[name="choix_tv"]').checked);
            addToLocalStorage('choixMachineALaver', document.querySelector('input[name="choix_machine_a_laver"]').checked);
            addToLocalStorage('choixLaveVaisselle', document.querySelector('input[name="choix_lave_vaiselle"]').checked);
            addToLocalStorage('choixWifi', document.querySelector('input[name="choix_wifi"]').checked);


            // Récupérer les valeurs des checkboxes services
            // var choixLinge = document.querySelector('input[name="choix_linge"]').checked;
            // var choixMenage = document.querySelector('input[name="choix_menage"]').checked;
            // var choixTransport = document.querySelector('input[name="choix_transport"]').checked;
            // var choixAnimaux = document.querySelector('input[name="choix_animaux"]').checked;
            addToLocalStorage('choixLinge', document.querySelector('input[name="choix_linge"]').checked);
            addToLocalStorage('choixMenage', document.querySelector('input[name="choix_menage"]').checked);
            addToLocalStorage('choixTransport', document.querySelector('input[name="choix_transport"]').checked);
            addToLocalStorage('choixAnimaux', document.querySelector('input[name="choix_animaux"]').checked);


            // Récupérer les valeurs des checkboxes installations
            //var choixClimatisation = document.querySelector('input[name="choix_climatisation"]').checked;
            // var choixPiscine = document.querySelector('input[name="choix_piscine"]').checked;
            // var choixJacuzzi = document.querySelector('input[name="choix_jacuzzi"]').checked;
            // var choixHammam = document.querySelector('input[name="choix_hammam"]').checked;
            // var choixSauna = document.querySelector('input[name="choix_sauna"]').checked;
            addToLocalStorage('choixClimatisation', document.querySelector('input[name="choix_climatisation"]').checked);
            addToLocalStorage('choixPiscine', document.querySelector('input[name="choix_piscine"]').checked);
            addToLocalStorage('choixJacuzzi', document.querySelector('input[name="choix_jacuzzi"]').checked);
            addToLocalStorage('choixSauna', document.querySelector('input[name="choix_sauna"]').checked);
            addToLocalStorage('choixHammam', document.querySelector('input[name="choix_hammam"]').checked);



            // Récupérer les valeurs des checkboxes amenagements
            //var choixJardin = document.querySelector('input[name="choix_jardin"]').checked;
            //var choixBalcon = document.querySelector('input[name="choix_balcon"]').checked;
            //var choixParkingPublic = document.querySelector('input[name="choix_parking_public"]').checked;
            //var choixParkingPrive = document.querySelector('input[name="choix_parking_prive"]').checked;
            //var choixTerrasse = document.querySelector('input[name="choix_terrasse"]').checked;
            addToLocalStorage('choixJardin', document.querySelector('input[name="choix_jardin"]').checked);
            addToLocalStorage('choixBalcon', document.querySelector('input[name="choix_balcon"]').checked);
            addToLocalStorage('choixParkingPublic', document.querySelector('input[name="choix_parking_public"]').checked);
            addToLocalStorage('choixParkingPrive', document.querySelector('input[name="choix_parking_prive"]').checked);
            addToLocalStorage('choixTerrasse', document.querySelector('input[name="choix_terrasse"]').checked);


            const options = {
                equipement_tv: localStorage.getItem('choixTv'),
                equipement_machine_a_laver: localStorage.getItem('choixMachineALaver'),
                equipement_lave_vaisselle: localStorage.getItem('choixLaveVaisselle'),
                equipement_wifi: localStorage.getItem('choixWifi'),
                service_linge: localStorage.getItem('choixLinge'),
                service_menage: localStorage.getItem('choixMenage'),
                service_transport: localStorage.getItem('choixTransport'),
                service_animaux_domestique: localStorage.getItem('choixAnimaux'),
                installation_climatisation: localStorage.getItem('choixClimatisation'),
                installation_piscine: localStorage.getItem('choixPiscine'),
                installation_jacuzzi: localStorage.getItem('choixJacuzzi'),
                installation_hammam: localStorage.getItem('choixHammam'),
                installation_sauna: localStorage.getItem('choixSauna'),
                amenagement_jardin: localStorage.getItem('choixJardin'),
                amenagement_balcon: localStorage.getItem('choixBalcon'),
                amenagement_parking_public: localStorage.getItem('choixParkingPublic'),
                amenagement_parking_prive: localStorage.getItem('choixParkingPrive'),
                amenagement_terrasse: localStorage.getItem('choixTerrasse')
            }

            /*
            for (var i = 0; i < result.avis.length; i++) {
                var note_tot = 0;
                if (result.avis[i].length === 0) {
                    // Si aucun avis n'est présent, la note moyenne est 0
                    result.events[i] = result.events[i] || {}; // Initialisation de result.events[i] comme un objet s'il n'existe pas
                    result.events[i]["note_moy"] = 0;
                } else {
                    var note = 0;
                    for (var j = 0; j < result.avis[i].length; j++) {
                        var avis_note_str = result.avis[i][j].avis_note; 
                        var avis_note_int = parseInt(avis_note_str);
                        note += avis_note_int;
                    }
                    note_tot = note / result.avis[i].length;
                    result.events[i] = result.events[i] || {}; // Initialisation de result.events[i] comme un objet s'il n'existe pas
                    result.events[i]["note_moy"] = note_tot;
                }
            }*/




            // Parcourir les logements
            for (logement of result.events) {

                listeLogement.push(logement);
            }
            

            var listDep = []
            if (localStorage.getItem('choixCoteArmor') === 'true') {
                listDep.push(22);



                //listeLogement=filtrerLogementsCP(result.events, 22);

            }

            if (localStorage.getItem('choixFinistere') === 'true') {
                listDep.push(29);


                //listeLogement=filtrerLogementsCP(result.events, 22);
            }

            if (localStorage.getItem('choixIlleEtVillaine') === 'true') {
                listDep.push(35);



                //listeLogement=filtrerLogementsCP(result.events,35)
            }

            if (localStorage.getItem('choixMorbihan') === 'true') {
                listDep.push(56);

                //listeLogement=filtrerLogementsCP(result.events,56)

            }



            listeLogement = filtrerLogementsCPV2(result.events, listDep)



            /*
            // Filtres equipements
            if (localStorage.getItem('choixTv') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.equipement_tv === true);

            }
            if (localStorage.getItem('choixMachineALaver') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.equipement_machine_a_laver === true);

            }
            if (localStorage.getItem('choixLaveVaisselle') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.equipement_lave_vaisselle === true);

            }
            if (localStorage.getItem('choixWifi') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.equipement_wifi === true);

            }


            // Filtres Services
            if (localStorage.getItem('choixLinge') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.service_linge === true);

            }
            if (localStorage.getItem('choixMenage') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.service_menage === true);

            }
            if (localStorage.getItem('choixTransport') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.service_transport === true);
            }
            if (localStorage.getItem('choixAnimaux') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.service_animaux_domestique === true);
            }


            // Filtres installations
            if (localStorage.getItem('choixClimatisation') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.installation_climatisation === true);
            }
            if (localStorage.getItem('choixPiscine') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.installation_piscine === true);
            }
            if (localStorage.getItem('choixJacuzzi') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.installation_jacuzzi === true);
            }
            if (localStorage.getItem('choixHammam') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.installation_hammam === true);
            }
            if (localStorage.getItem('choixSauna') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.installation_sauna === true);
            }


            // Filtres amenagements
            if (localStorage.getItem('choixJardin') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.amenagement_jardin === true);
            }
            if (localStorage.getItem('choixBalcon') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.amenagement_balcon === true);
            }
            if (localStorage.getItem('choixParkingPublic') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.amenagement_parking_prive === true);
            }
            if (localStorage.getItem('choixParkingPrive') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.amenagement_parking_prive === true);
            }
            if (localStorage.getItem('choixTerrasse') === 'true') {
                listeLogement = listeLogement.filter(logement => logement.amenagement_terrasse === true);
            }*/


            // Exemple d'utilisation avec les options d'équipements, de services, d'installations et d'aménagements



            for (const key in options) {
                options[key] = options[key].toString();
            }
            
            listeLogement = filtrerLogementsOPtions(listeLogement, options);
            //console.log("TEST FILTRE EEE:",logementsFiltres);

            listeLogement = filtrerLogementsParPrix(listeLogement, localStorage.getItem('prixMin'), localStorage.getItem('prixMax'))

            //Filtres pers
            if (localStorage.getItem('pers')) {
                listeLogement = filtrerLogementsParNombrePersonnes(listeLogement, parseInt(localStorage.getItem('pers')))


            }






            updateMapMarkers(listeLogement);





            // Tri selon différents critères
            var critereTriElement = document.querySelector('input[type="radio"][name="tri"]:checked');
            var critereTri = critereTriElement ? critereTriElement.value : '';

            var moy_notes = moy_note_logement()

            listeLogement.forEach(function (logement) {
                logement.note_moy = parseFloat(moy_notes[logement.logement_id]);
                if (moy_notes[logement.logement_id] === undefined) {
                    logement.note_moy = 0;
                }



            });

            if (critereTri === 'croissant') {
                listeLogement.sort((a, b) => a.logement_prix_nuit_base - b.logement_prix_nuit_base);
            } else if (critereTri === 'decroissant') {
                listeLogement.sort((a, b) => b.logement_prix_nuit_base - a.logement_prix_nuit_base);
            } else if (critereTri === 'note-') {
                listeLogement.sort((a, b) => a.note_moy - b.note_moy);
            } else if (critereTri === 'note+') {
                listeLogement.sort((a, b) => b.note_moy - a.note_moy);
            }








            listeLogement.forEach(function (logement) {
                // Création d'éléments HTML en JavaScript

                var li = document.createElement('li');
                li.className = 'logement';

                li.id = logement.logement_id;
                var a = document.createElement('a');
                a.className = 'un_logement';
                a.href = 'Logements/afficher_logement.php?logement_id=' + logement.logement_id;

                var img = document.createElement('img');
                img.className = 'photo_accueil';
                img.src = logement.logement_photo;
                img.alt = '';

                var divTextLogement = document.createElement('div');
                divTextLogement.className = 'text_logement';

                var h3 = document.createElement('h3');
                h3.textContent = logement.logement_accroche;

                var pLieu = document.createElement('p');
                pLieu.className = 'lieu';
                pLieu.textContent = logement.logement_ville + ', ' + logement.logement_code_postal;

                var pPrix = document.createElement('p');
                pPrix.className = 'prix';
                pPrix.innerHTML = '<strong>' + logement.logement_prix_nuit_base + ' €</strong> par nuit';

                // la fonction

                var note = moy_notes[logement.logement_id];

                var pNote = document.createElement('p')
                if (note != null) {
                    pNote.innerHTML = note + ' / 5' + " <i class='fas fa-star'></i>"
                }

                // Ajout des éléments au DOM
                divTextLogement.appendChild(h3);
                divTextLogement.appendChild(pLieu);
                divTextLogement.appendChild(pPrix);
                divTextLogement.appendChild(pNote);


                a.appendChild(img);
                a.appendChild(divTextLogement);

                li.appendChild(a);

                // Supposons que #listeLogements soit l'élément parent où vous souhaitez ajouter ces éléments
                document.getElementById('listeLogements').appendChild(li);

            });


        },
        error: function (xhr, status, error) {
            console.error('Erreur', error);
        }

    });

    console.timeEnd("filtre");
}

