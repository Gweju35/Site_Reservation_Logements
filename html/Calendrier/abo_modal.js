function validerFormulaire() {
    // Vérifier si au moins une case à cocher est cochée
    if (!document.getElementById('checkbox1').checked &&
        !document.getElementById('checkbox2').checked &&
        !document.getElementById('checkbox3').checked) {
        alert("Veuillez cocher au moins une option dans les événements.");
        return false; // Empêche l'envoi du formulaire si aucune case n'est cochée
    }

    var dateDebut = new Date(document.getElementById("date_debut").value);
    var dateFin = new Date(document.getElementById("date_fin").value);
    console.log(dateDebut,dateFin)

     // Vérifier si les champs sont vides
     if (dateDebut == "Invalid Date" || dateFin == "Invalid Date") {
        alert("Veuillez sélectionner une date de début et une date de fin.");
        return false;
    }
    
    if (dateDebut > dateFin) {
        alert("La date de fin ne peut pas être antérieure à la date de début.");
        return false;
    }
    return true; // Permet l'envoi du formulaire si au moins une case est cochée
}





$(document).ready(function () {
    $("#envoyer").click(function (event) {
        event.preventDefault(); // Empêche le comportement par défaut du formulaire

        if (!validerFormulaire()) {
            return; // Arrêter l'exécution si la validation échoue
        }
        $.ajax({
            url: "ajout_abo_bbd.php", // L'URL de votre script de traitement
            method: "POST", // Méthode d'envoi des données
            data: $("#formulaire_abo").serialize(), // Sérialiser les données du formulaire
            success: function (response) {
                // Traitement à effectuer en cas de succès
                //console.log(response);
                $.modal.close();
                

                let result = JSON.parse(response);
                console.log('KEY:::',result.res)
                $.ajax({
                    url: '../API/generateCalendar.php', // URL du script de traitement
                    type: 'GET', // Méthode de requête (GET)
                    data: {key : result.res}, // Données à envoyer avec la requête
                    success: function() {
                        alert("Abonnement créer avec succès !! \nLien collé dans le presse papier");
                        // Fonction exécutée en cas de succès
                        

                        
                        var url = window.location.href;
                        // Extraire le début de l'URL
                        var debutUrl = url.split('/').slice(0, 3).join('/')+'/API/generateCalendar.php?key=';
                        
                        

                        // Code à exécuter lorsque le DOM est entièrement chargé
                        var textToCopy = debutUrl+result.res;
                        console.log("VALEUR COPY ::",textToCopy);

                        // Vérifier si l'API navigator.clipboard est disponible
                        if (navigator.clipboard) {
                            // Utiliser l'API navigator.clipboard pour copier le texte
                            navigator.clipboard.writeText(textToCopy)
                                .then(function() {
                                    console.log("Texte copié dans le presse-papiers : " + textToCopy);
                                })
                                .catch(function(error) {
                                    console.error("Erreur lors de la copie du texte : ", error);
                                });
                        } else {
                            // Si l'API navigator.clipboard n'est pas disponible, afficher un message d'erreur
                            console.error("L'API navigator.clipboard n'est pas disponible.");
                        }


                    },
                    error: function(xhr, status, error) {
                        // Fonction exécutée en cas d'erreur
                    }
                });

                //$.modal.close();
                // Afficher l'alerte après un délai de 2 secondes*
                

            },
            error: function (xhr, status, error) {
                // Traitement à effectuer en cas d'erreur
                console.error(error);
                alert("Une erreur s'est produite lors de la soumission du formulaire.");
            }
        });

    });
    

});