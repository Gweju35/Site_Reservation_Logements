document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    let isAddingIndispo = false;
    let isDeletingIndispo = false;
    let isAddingPeriode = false;
    let isDeletingPeriode = false;
    let id_logement;


    // Récupérer la valeur sélectionnée par défaut
    id_logement = $("#choixLogement").val();
    console.log("Valeur par défaut sélectionnée : " + id_logement);
    $.ajax({
        url: 'traitement_calendrier.php',
        method: 'POST',
        data: { logement_id: id_logement },
        success: function(response) {
            console.log(response); // Affichez le contenu du JSON dans la console
            // Analyser la réponse JSON
            var result = JSON.parse(response);
            
            
            
            if (result.success) {
                // Initialisez FullCalendar avec les données récupérées
                $('#btnAddIndispo').on('click', function() {
                    // Mettez à jour la variable globale lorsque le bouton est cliqué
                    isAddingIndispo = true;
                });

                $('#btnSupprimer').on('click', function() {
                    // Mettez à jour la variable globale lorsque le bouton est cliqué
                    isDeletingIndispo = true;
                });

                $('#btnAddPeriode').on('click', function() {
                    // Mettez à jour la variable globale lorsque le bouton est cliqué
                    isAddingPeriode = true;
                });

                $('#btnSupprimerPeriode').on('click', function() {
                    // Mettez à jour la variable globale lorsque le bouton est cliqué
                    isDeletingPeriode = true;
                });

    
                var calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem : 'lumen',
                initialView: 'dayGridMonth',
                events: result.events,
                locale: 'fr',
                defaultView: 'month',
                selectable: true,
                height: 650,

                eventClick: function(event) {
                    if(event.className !='unavailable-event' && event.className !='pp-event'){
                        console.log('Cliquez sur un événement:', event);
                        // Rediriger vers une autre page en utilisant window.location.href
                        window.location.href = "../Devis/devis_du_proprio.php";
                        
                    } else {
                        if(isDeletingIndispo){
                            isDeletingIndispo = false;
                            console.log(event);
                            event.remove();
                        }
                        
                    }
                },
                select: function (info) {
                    var test = calendar.getEvents()
                        console.log(test)
                    
                    // Vérifiez si le bouton "Ajouter Indisponibilité" a été cliqué
                    if (isAddingIndispo) {
                        // Réinitialisez la variable globale
                        isAddingIndispo = false
                        
                        // Votre logique pour ajouter la période d'indisponibilité au calendrier
                        console.log(calendar.getEvents())
                        var overlap = calendar.getEvents().some(function(event) {
                            return (info.start < event.end && info.end > event.start);
                        });

                        //console.log(overlap);
                        
                        
                        if (!overlap) {
                            var startString = info.startStr;
                            var endString = info.endStr;
                            
                            $.ajax({
                                url: 'ajouter_evenement.php',
                                method: 'POST',
                                data: { start: startString, end: endString, logement_id: id_logement },
                                success: function(response) {
                                    // La réponse peut contenir des informations supplémentaires depuis le serveur
                                    console.log('Période d\'indisponibilité ajoutée avec succès à la base de données.', response);
                                    var startDate = new Date(startString);
                                    var endDate = new Date(endString);

                                    var dateArray = [];
                                    var currentDate = startDate;

                                    while (currentDate < endDate) {
                                        var dateString = currentDate.toISOString();
                                        currentDate.setDate(currentDate.getDate() + 1);
                                        var datePlusString = currentDate.toISOString();
                                        
                                        //console.log(dateString,datePlusString);
                                        
                                        calendar.addEvent({
                                            title: 'Raison personnelle',
                                            start: dateString,
                                            end: datePlusString, // Même date pour le début et la fin, car il s'agit d'une journée entière
                                            allDay: true, // Si vous souhaitez que l'événement soit toute la journée
                                            className: 'unavailable-event',
                                            display: 'background',
                                            backgroundColor: '#FF0000'
                                        });

                                        //currentDate.setDate(currentDate.getDate() + 1); // Passer à la prochaine journée
                                    }
                                    // Ensuite, ajoutez la période d'indisponibilité au calendrier côté client

                                },
                                error: function(xhr, status, error) {
                                    console.error('Erreur lors de l\'ajout de la période d\'indisponibilité à la base de données:', error);
                                }
                            });
                               
                        } else {
                            // La période chevauche un événement existant, affichez un message d'erreur par exemple
                            alert('La période sélectionnée chevauche un événement existant. Veuillez sélectionner une autre période.');
                        }
                        
                        // Effacez la sélection
                        calendar.unselect();
                        calendar.refetchEvents();
                        

                    } else if (isDeletingIndispo) {
                        isDeletingIndispo = false
                        /*
                        
                        var test = calendar.getEvents()
                        console.log(test)
                        */
                        
                         
                        var startString= info.startStr;
                        var endString = info.endStr;
                        console.log(startString,endString);

                        var eventsInRange = calendar.getEvents().filter(function(event) {
                            return (event.start >= info.start && event.end <= info.end);
                        });
                        
                        console.log(eventsInRange);
                        
                        $.ajax({
                            url: 'supprimer_evenement.php',
                            method: 'POST',
                            data: { start: startString, end: endString, logement_id: id_logement },
                            success: function(response) {
                                // La réponse peut contenir des informations supplémentaires depuis le serveur
                                console.log('Événement supprimé avec succès de la base de données.', response);
                                
                                // Ensuite, supprimez l'événement du calendrier côté client
                                eventsInRange.forEach(function(event) {
                                    if(event.title=='Raison personnelle'){
                                        event.remove();
                                    }
                                    
                                });
                                calendar.refetchEvents();

                            },
                            error: function(xhr, status, error) {
                                console.error('Erreur lors de la suppression de l\'événement de la base de données:', error);
                            }
                        });
                    }else if(isAddingPeriode){
                        isAddingPeriode = false;

                        var startString= info.startStr;
                        var endString = info.endStr;

                        // Votre logique pour ajouter la période de prix au calendrier
                        var overlap = calendar.getEvents().some(function(event) {
                            return (info.start < event.end && info.end > event.start);
                        });
                        if (!overlap) {
                            while(isNaN(numberInput)){
                                // Création de la boîte de dialogue
                                var userInput = prompt("Veuillez entrer un chiffre :");
                                
                                // Conversion de la saisie en nombre
                                var numberInput = parseFloat(userInput);

                                // Vérification si la saisie est un nombre
                                if (isNaN(numberInput)) {
                                    alert("Saisie invalide. Veuillez entrer un nombre.");
                                }
                            }
                        
                            $.ajax({
                                url: 'ajouter_periode_prix.php',
                                method: 'POST',
                                data: { start: startString, end: endString, logement_id: id_logement ,prix: numberInput},
                                success: function(response) {
                                    // La réponse peut contenir des informations supplémentaires depuis le serveur
                                    console.log('Période de prix avec succès à la base de données.', response);
                                    var startDate = new Date(startString);
                                    var endDate = new Date(endString);

                                    var dateArray = [];
                                    var currentDate = startDate;

                                    while (currentDate < endDate) {
                                        var dateString = currentDate.toISOString();
                                        currentDate.setDate(currentDate.getDate() + 1);
                                        var datePlusString = currentDate.toISOString();
                                        
                                        //console.log(dateString,datePlusString);
                                        
                                        calendar.addEvent({
                                            title: numberInput+' €',
                                            start: dateString,
                                            end: datePlusString, // Même date pour le début et la fin, car il s'agit d'une journée entière
                                            allDay: true, // Si vous souhaitez que l'événement soit toute la journée
                                            className: 'pp-event',
                                            backgroundColor: '#32CD32'
                                        });

                                        //currentDate.setDate(currentDate.getDate() + 1); // Passer à la prochaine journée
                                    }
                                    // Ensuite, ajoutez la période d'indisponibilité au calendrier côté client

                                },
                                error: function(xhr, status, error) {
                                    console.error('Erreur lors de l\'ajout de la période d\'indisponibilité à la base de données:', error);
                                }
                            });
                        } else {
                            // La période chevauche un événement existant, affichez un message d'erreur par exemple
                            alert('La période de prix sélectionnée chevauche une période de prix existant.');
                        }
                        

                    }else if (isDeletingPeriode) {
                        isDeletingPeriode = false
                        /*
                        
                        var test = calendar.getEvents()
                        console.log(test)
                        */
                        
                         
                        var startString= info.startStr;
                        var endString = info.endStr;
                        console.log(startString,endString);

                        var eventsInRange = calendar.getEvents().filter(function(event) {
                            return (event.start >= info.start && event.end <= info.end);
                        });
                        
                        console.log(eventsInRange);
                        
                        $.ajax({
                            url: 'supprimer_periode_prix.php',
                            method: 'POST',
                            data: { start: startString, end: endString, logement_id: id_logement },
                            success: function(response) {
                                // La réponse peut contenir des informations supplémentaires depuis le serveur
                                console.log('Période de prix supprimé avec succès de la base de données.', response);
                                
                                // Ensuite, supprimez l'événement du calendrier côté client
                                eventsInRange.forEach(function(event) {
                                    console.log(event.classNames[0]);
                                    if(event.classNames[0] == 'pp-event'){
                                        event.remove();
                                    }
                                    
                                });
                                calendar.refetchEvents();

                            },
                            error: function(xhr, status, error) {
                                console.error('Erreur lors de la suppression de la période prix de la base de données:', error);
                            }
                        });

                    }
                        
                        
                        
                    }
                    
                

                });
                calendar.render();
            } else {
                console.error('Erreur lors de la récupération des événements:', result.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX:', error);
        }
    });

    $("#choixLogement").on("click", (e) => {
        //$.ajax("/")
        //console.log(e.target.selectedOptions[0].label,e.target.selectedOptions[0].value, e)
        id_logement=e.target.selectedOptions[0].value;
        $.ajax({
            url: 'traitement_calendrier.php',
            method: 'POST',
            data: { logement_id: id_logement },
            success: function(response) {
                console.log(response); // Affichez le contenu du JSON dans la console
                // Analyser la réponse JSON
                var result = JSON.parse(response);
                
                
                
                if (result.success) {
                    // Initialisez FullCalendar avec les données récupérées
                    $('#btnAddIndispo').on('click', function() {
                        // Mettez à jour la variable globale lorsque le bouton est cliqué
                        isAddingIndispo = true;
                    });
    
                    $('#btnSupprimer').on('click', function() {
                        // Mettez à jour la variable globale lorsque le bouton est cliqué
                        isDeletingIndispo = true;
                    });

                    $('#btnAddPeriode').on('click', function() {
                        // Mettez à jour la variable globale lorsque le bouton est cliqué
                        isAddingPeriode = true;
                    });
    
                    $('#btnSupprimerPeriode').on('click', function() {
                        // Mettez à jour la variable globale lorsque le bouton est cliqué
                        isDeletingPeriode = true;
                    });
    
        
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                    themeSystem : 'lumen',
                    initialView: 'dayGridMonth',
                    events: result.events,
                    locale: 'fr',
                    defaultView: 'month',
                    selectable: true,
                    height: 650,
    
                    eventClick: function(event) {
                        if(event.className !='unavailable-event' && event.className !='pp-event'){
                            console.log('Cliquez sur un événement:', event);
                            // Rediriger vers une autre page en utilisant window.location.href
                            window.location.href = "../Devis/devis_du_proprio.php";
                            
                        } else {
                            if(isDeletingIndispo){
                                isDeletingIndispo = false;
                                console.log(event);
                                event.remove();
                            }
                            
                        }
                    },
                    select: function (info) {
                        var test = calendar.getEvents()
                            console.log(test)
                        
                        // Vérifiez si le bouton "Ajouter Indisponibilité" a été cliqué
                        if (isAddingIndispo) {
                            // Réinitialisez la variable globale
                            isAddingIndispo = false
                            
                            // Votre logique pour ajouter la période d'indisponibilité au calendrier
                            console.log(calendar.getEvents())
                            var overlap = calendar.getEvents().some(function(event) {
                                return (info.start < event.end && info.end > event.start);
                            });

                            //console.log(overlap);
                            
                            
                            if (!overlap) {
                                var startString = info.startStr;
                                var endString = info.endStr;
                                
                                $.ajax({
                                    url: 'ajouter_evenement.php',
                                    method: 'POST',
                                    data: { start: startString, end: endString, logement_id: id_logement },
                                    success: function(response) {
                                        // La réponse peut contenir des informations supplémentaires depuis le serveur
                                        console.log('Période d\'indisponibilité ajoutée avec succès à la base de données.', response);
                                        var startDate = new Date(startString);
                                        var endDate = new Date(endString);

                                        var dateArray = [];
                                        var currentDate = startDate;

                                        while (currentDate < endDate) {
                                            var dateString = currentDate.toISOString();
                                            currentDate.setDate(currentDate.getDate() + 1);
                                            var datePlusString = currentDate.toISOString();
                                            
                                            //console.log(dateString,datePlusString);
                                            
                                            calendar.addEvent({
                                                title: 'Raison personnelle',
                                                start: dateString,
                                                end: datePlusString, // Même date pour le début et la fin, car il s'agit d'une journée entière
                                                allDay: true, // Si vous souhaitez que l'événement soit toute la journée
                                                className: 'unavailable-event',
                                                display: 'background',
                                                backgroundColor: '#FF0000'
                                            });

                                            //currentDate.setDate(currentDate.getDate() + 1); // Passer à la prochaine journée
                                        }
                                        // Ensuite, ajoutez la période d'indisponibilité au calendrier côté client
    
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Erreur lors de l\'ajout de la période d\'indisponibilité à la base de données:', error);
                                    }
                                });
                                   
                            } else {
                                // La période chevauche un événement existant, affichez un message d'erreur par exemple
                                alert('La période sélectionnée chevauche un événement existant. Veuillez sélectionner une autre période.');
                            }
                            
                            // Effacez la sélection
                            calendar.unselect();
                            calendar.refetchEvents();
                            
    
                        } else if (isDeletingIndispo) {
                            isDeletingIndispo = false
                            /*
                            
                            var test = calendar.getEvents()
                            console.log(test)
                            */
                            
                             
                            var startString= info.startStr;
                            var endString = info.endStr;
                            console.log(startString,endString);

                            var eventsInRange = calendar.getEvents().filter(function(event) {
                                return (event.start >= info.start && event.end <= info.end);
                            });
                            
                            console.log(eventsInRange);
                            
                            $.ajax({
                                url: 'supprimer_evenement.php',
                                method: 'POST',
                                data: { start: startString, end: endString, logement_id: id_logement },
                                success: function(response) {
                                    // La réponse peut contenir des informations supplémentaires depuis le serveur
                                    console.log('Événement supprimé avec succès de la base de données.', response);
                                    
                                    // Ensuite, supprimez l'événement du calendrier côté client
                                    eventsInRange.forEach(function(event) {
                                        if(event.title=='Raison personnelle'){
                                            event.remove();
                                        }
                                        
                                    });
                                    calendar.refetchEvents();
    
                                },
                                error: function(xhr, status, error) {
                                    console.error('Erreur lors de la suppression de l\'événement de la base de données:', error);
                                }
                            });
                        }else if(isAddingPeriode){
                            isAddingPeriode = false;

                            var startString= info.startStr;
                            var endString = info.endStr;

                            // Votre logique pour ajouter la période de prix au calendrier
                            var overlap = calendar.getEvents().some(function(event) {
                                return (info.start < event.end && info.end > event.start);
                            });
                            if (!overlap) {
                                while(isNaN(numberInput)){
                                    // Création de la boîte de dialogue
                                    var userInput = prompt("Veuillez entrer un chiffre :");
                                    
                                    // Conversion de la saisie en nombre
                                    var numberInput = parseFloat(userInput);

                                    // Vérification si la saisie est un nombre
                                    if (isNaN(numberInput)) {
                                        alert("Saisie invalide. Veuillez entrer un nombre.");
                                    }
                                }
                            
                                $.ajax({
                                    url: 'ajouter_periode_prix.php',
                                    method: 'POST',
                                    data: { start: startString, end: endString, logement_id: id_logement ,prix: numberInput},
                                    success: function(response) {
                                        // La réponse peut contenir des informations supplémentaires depuis le serveur
                                        console.log('Période de prix avec succès à la base de données.', response);
                                        var startDate = new Date(startString);
                                        var endDate = new Date(endString);
    
                                        var dateArray = [];
                                        var currentDate = startDate;
    
                                        while (currentDate < endDate) {
                                            var dateString = currentDate.toISOString();
                                            currentDate.setDate(currentDate.getDate() + 1);
                                            var datePlusString = currentDate.toISOString();
                                            
                                            //console.log(dateString,datePlusString);
                                            
                                            calendar.addEvent({
                                                title: numberInput+' €',
                                                start: dateString,
                                                end: datePlusString, // Même date pour le début et la fin, car il s'agit d'une journée entière
                                                allDay: true, // Si vous souhaitez que l'événement soit toute la journée
                                                className: 'pp-event',
                                                backgroundColor: '#32CD32'
                                            });
    
                                            //currentDate.setDate(currentDate.getDate() + 1); // Passer à la prochaine journée
                                        }
                                        // Ensuite, ajoutez la période d'indisponibilité au calendrier côté client
    
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Erreur lors de l\'ajout de la période d\'indisponibilité à la base de données:', error);
                                    }
                                });
                            } else {
                                // La période chevauche un événement existant, affichez un message d'erreur par exemple
                                alert('La période de prix sélectionnée chevauche une période de prix existant.');
                            }
                            

                        }else if (isDeletingPeriode) {
                            isDeletingPeriode = false
                            /*
                            
                            var test = calendar.getEvents()
                            console.log(test)
                            */
                            
                             
                            var startString= info.startStr;
                            var endString = info.endStr;
                            console.log(startString,endString);

                            var eventsInRange = calendar.getEvents().filter(function(event) {
                                return (event.start >= info.start && event.end <= info.end);
                            });
                            
                            console.log(eventsInRange);
                            
                            $.ajax({
                                url: 'supprimer_periode_prix.php',
                                method: 'POST',
                                data: { start: startString, end: endString, logement_id: id_logement },
                                success: function(response) {
                                    // La réponse peut contenir des informations supplémentaires depuis le serveur
                                    console.log('Période de prix supprimé avec succès de la base de données.', response);
                                    
                                    // Ensuite, supprimez l'événement du calendrier côté client
                                    eventsInRange.forEach(function(event) {
                                        console.log(event.classNames[0]);
                                        if(event.classNames[0] == 'pp-event'){
                                            event.remove();
                                        }
                                        
                                    });
                                    calendar.refetchEvents();
    
                                },
                                error: function(xhr, status, error) {
                                    console.error('Erreur lors de la suppression de la période prix de la base de données:', error);
                                }
                            });

                        }
                            
                            
                            
                        }
                        
                    
    
                    });
                    calendar.render();
                } else {
                    console.error('Erreur lors de la récupération des événements:', result.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', error);
            }
        });
        
    })
    
    
});