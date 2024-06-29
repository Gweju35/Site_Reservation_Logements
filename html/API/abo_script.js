
document.addEventListener('DOMContentLoaded', function() {
    
    
});

function checkboxClicked(name, id) {

    var checkboxes = document.getElementsByName(name);

    for (var i = 0; i < checkboxes.length; i++) {

        console.log(checkboxes[i].id);

        // Vérifier si l'ID de la case à cocher correspond à la valeur passée en paramètre
        if (checkboxes[i].id === id) {
            // Vous avez trouvé la case à cocher avec l'ID recherché
            var checkbox = checkboxes[i];
        }
    }

    if(checkbox.checked){
        var check = true; 
    }else{
        var check = false; 
    }

    console.log('Checkbox val:',check)
    $.ajax({
        url: 'traitement_droit_cle_abonnement.php',
        method: 'POST',
        data: {droit: name , cle_id: id, val: check}, 
        success: function(response) {
            console.log(response); // Affichez le contenu du JSON dans la console
            // Analyser la réponse JSON
            /*var result = JSON.parse(response);
            if (result.success) {
                key = result.key;
            } else {
                console.error('Erreur lors de la modif des droit de la clé API', result.error);
            }*/
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX:', error);
        }
    });

};

function suppression(id) {
    $.ajax({
        url: 'traitement_suppr_abo.php',
        method: 'POST',
        data: {cle_id: id}, 
        success: function(response) {
            console.log(response);
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX:', error);
        }
    });

};





function dateDebutChanged(name,id,value) {
    $.ajax({
        url: 'traitement_modif_date_deb_abo.php',
        method: 'POST',
        data: {cle_id: id,date: value}, 
        success: function(response) {
            console.log(response);
            let = date_val_deb = document.getElementsByName(name);
            date_val_deb.value = response.event;

            
            
            //location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX:', error);
        }
    });


}

function dateFinChanged(name,id,value) {
    $.ajax({
        url: 'traitement_modif_date_fin_abo.php',
        method: 'POST',
        data: {cle_id: id,date: value}, 
        success: function(response) {
            console.log(response);
            let = date_val_deb = document.getElementsByName(name);
            date_val_deb.value = response.event;

            
            
            //location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX:', error);
        }
    });


}

// Nettoyer complètement le localStorage
console.log(localStorage.getItem('localUrl'));
// localStorage.clear();

function mettreAJourLiens() {
    // Récupérer le début de l'URL depuis le localStorage
    //let debutUrl = localStorage.getItem('startURLCalendar');
    // Récupérer l'URL locale
    var url = window.location.href;
    // Extraire le début de l'URL
    var debutUrl = url.split('/').slice(0, 4).join('/')+'/';
    
    // Sélectionner toutes les balises <td> ayant un attribut id
    let tdAvecId = document.querySelectorAll('td[id]');
    
    // Parcourir les balises <td> sélectionnées
    tdAvecId.forEach(td => {
        // Récupérer la clé d'abonnement depuis l'attribut id
        let cleAbonnement = td.id;
        
        // Construire le lien en concaténant le début de l'URL et la clé d'abonnement
        let lien = debutUrl + 'generateCalendar.php?key=' + cleAbonnement;
        
        // Mettre à jour le contenu de la balise <td> avec le lien
        td.innerHTML = '<a href="' + lien + '">' + cleAbonnement + '</a>';
        
        // Ajouter un gestionnaire d'événements click pour copier le lien dans le presse-papiers
        td.addEventListener('click', function() {
            // Copier le lien dans le presse-papiers
            navigator.clipboard.writeText(lien).then(function() {
                // Réaction après la copie réussie (par exemple, afficher un message de confirmation)
                alert('Le lien a été copié dans le presse-papiers.');
            }, function(err) {
                // Gérer les erreurs éventuelles lors de la copie
                console.error('Erreur lors de la copie du lien : ', err);
            });
        });
    });
}

// Appeler la fonction pour mettre à jour les liens lors du chargement de la page
window.onload = mettreAJourLiens;
