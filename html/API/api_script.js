
document.addEventListener('DOMContentLoaded', function() {
    
    const button = document.getElementById("btn_generer_api");
    button.addEventListener('click', async () => {
        try {
            var key
            await $.ajax({
                url: 'generer_cle_api.php',
                success: function(response) {
                    
                    console.log(response); // Affichez le contenu du JSON dans la console
                    // Analyser la réponse JSON
                    var result = JSON.parse(response);
                    if (result.success) {
                        key = result.key;
                        location.reload();
                    } else {
                        console.error('Erreur lors de la création de la clé API', result.error);
                        key="vide";
                    }

                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', error);
                }
            });
            await navigator.clipboard.writeText(key);
            //Un pop UP pour dire que la clé à été copier dans le presse papier
        } catch (err) {
            console.error(err.name, err.message);
        }
    });
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
        if(checkboxes[i].name == 'cles_api_apirator'){
            if(checkboxes[i].id == checkbox.id){
                checkboxes[i].disabled=false;
            }

        }
    }

    if(checkbox.checked){
        var check = true; 
    }else{
        var check = false; 
    }
    $.ajax({
        url: 'traitement_droit_api.php',
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
        url: 'traitement_suppr_api.php',
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
