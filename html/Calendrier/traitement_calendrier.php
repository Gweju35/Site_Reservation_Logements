<?php
session_start();
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sélection des réservations
    $sql_reservations = "SELECT r.*, l.*, d.*
    FROM alhaiz_breizh._reservation AS r
    JOIN alhaiz_breizh._logement AS l ON r.reservation_logement_id = l.logement_id
    JOIN alhaiz_breizh._devis AS d ON r.reservation_devis_id = d.devis_id
    WHERE d.devis_statut = 'PPC'
    AND l.logement_compte_id_proprio = :compte_id
    AND l.logement_id = :logement_id;
    ";

    $stmt_reservations = $pdo->prepare($sql_reservations);
    $stmt_reservations->bindParam(':compte_id', $_SESSION['user_id']);
    $stmt_reservations->bindParam(':logement_id', $_POST['logement_id']);
    $stmt_reservations->execute();

    $reservations = $stmt_reservations->fetchAll(PDO::FETCH_ASSOC);

    // Sélection des périodes d'indisponibilité
    $sql_indisponibilites = "SELECT calendrier_date_jour,calendrier_motif_indisponibilite
        FROM alhaiz_breizh._calendrier
        WHERE calendrier_logement_id = :logement_id AND calendrier_motif_indisponibilite = 'Raison personnelle';";

    $stmt_indisponibilites = $pdo->prepare($sql_indisponibilites);
    $stmt_indisponibilites->bindParam(':logement_id', $_POST['logement_id']);
    $stmt_indisponibilites->execute();

    $indisponibilites = $stmt_indisponibilites->fetchAll(PDO::FETCH_ASSOC);

    // Sélection des devis
    $sql_devis = "SELECT calendrier_date_jour,calendrier_motif_indisponibilite
    FROM alhaiz_breizh._calendrier
    WHERE calendrier_logement_id = :logement_id AND calendrier_motif_indisponibilite = 'Devis';";

    $stmt_devis = $pdo->prepare($sql_devis);
    $stmt_devis->bindParam(':logement_id', $_POST['logement_id']);
    $stmt_devis->execute();

    $devis = $stmt_devis->fetchAll(PDO::FETCH_ASSOC);

    // Sélection des périodes de prix
    $sql_pp = "SELECT periode_jour_date,periode_jour_prix 
    FROM alhaiz_breizh._periode_prix 
    WHERE periode_logement_id= :logement_id;";

    $stmt_pp = $pdo->prepare($sql_pp);
    $stmt_pp->bindParam(':logement_id', $_POST['logement_id']);
    $stmt_pp->execute();

    $pp = $stmt_pp->fetchAll(PDO::FETCH_ASSOC);


    $events = array();

    // Ajouter les réservations aux événements
    if(isset($reservation)){
        
        foreach ($reservations as $reservation) {
            $event = array(
                'title' =>  $reservation['logement_description'],
                'start' => $reservation['devis_date_debut'] . 'T16:00:00',
                'end' => $reservation['devis_date_fin'] . 'T11:00:00',
                'backgroundColor' => '#1fa055'

            );
    
            $events[] = $event;
        }

    }

    // Ajouter les devis aux événements
    if(isset($devis)){
        
        foreach ($devis as $devi) {
            // Convertir la chaîne de date en objet DateTime
            $date1 = new DateTime($devi['calendrier_date_jour']);

            // Ajouter un jour à la date
            $date1->modify('+1 day');
            
            // Obtenez la nouvelle date sous forme de chaîne
            $datePlusUn1 = $date1->format('Y-m-d');

            $event = array(
                'title' =>  $devi['calendrier_motif_indisponibilite'],
                'start' => $devi['calendrier_date_jour'],
                'end' => $datePlusUn1,
                'className' => 'unavailable-event',
                'backgroundColor' => '#ff7f00'

            );
    
            $events[] = $event;
        }

    }

    // Ajouter les devis aux événements
    if(isset($pp)){
        
        foreach ($pp as $p) {
            // Convertir la chaîne de date en objet DateTime
            $date2 = new DateTime($p['periode_jour_date']);

            // Ajouter un jour à la date
            $date2->modify('+1 day');
            
            // Obtenez la nouvelle date sous forme de chaîne
            $datePlusUn2 = $date2->format('Y-m-d');

            $event = array(
                'title' =>  $p['periode_jour_prix']." €",
                'start' => $p['periode_jour_date'],
                'end' => $datePlusUn2,
                'className' => 'pp-event',
                'backgroundColor' => '#32CD32'

            );
    
            $events[] = $event;
        }

    }
    

    // Ajouter les périodes d'indisponibilité aux événements
    if(isset($indisponibilites)){
        foreach ($indisponibilites as $indisponibilite) {
            
            // Convertir la chaîne de date en objet DateTime
            $date = new DateTime($indisponibilite['calendrier_date_jour']);

            // Ajouter un jour à la date
            $date->modify('+1 day');

            // Obtenez la nouvelle date sous forme de chaîne
            $datePlusUn = $date->format('Y-m-d');
            $event = array(
                'title' => $indisponibilite['calendrier_motif_indisponibilite'],
                'start' => $indisponibilite['calendrier_date_jour'],
                'end' => $datePlusUn,
                'allDay'=> true,
                'className' => 'unavailable-event',
                'display' => 'background',
                'backgroundColor' => '#FF0000'
            );

            $events[] = $event;
            
        }
    }
    $json_data = json_encode(array('success' => true, 'events' => $events), JSON_PRETTY_PRINT);
    echo $json_data;
} catch (PDOException $e) {
    // En cas d'erreur, retourner une réponse d'échec
    $json_error = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_error;
}
?>