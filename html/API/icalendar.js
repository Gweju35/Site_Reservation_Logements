$(document).ready(function(){
    $('#calendarForm').submit(function(event){
        // Empêcher le comportement par défaut du formulaire
        event.preventDefault();
        
        // Récupérer les données du formulaire
        var formData = $(this).serialize();
        
        // Envoyer une requête AJAX POST au script PHP
        $.ajax({
            url: 'generateCalendar.php',
            type: 'POST',
            data: formData,
            success: function(response){
                // Afficher l'URL d'abonnement générée
                $('#calendarURL').html(response);
            }
        });
    });
});