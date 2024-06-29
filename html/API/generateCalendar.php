<?php
include ('../../libs/connect_params.php');
//print_r($_GET);
try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $get_id_proprio = "SELECT abonnement_id_compte FROM alhaiz_breizh._abonnement WHERE abonnement_cle = :key ;";

    $stmt0 = $pdo->prepare($get_id_proprio);
    $stmt0->bindParam(':key', $_GET['key']);
    $stmt0->execute();

    
    if ($id_proprio_row = $stmt0->fetch(PDO::FETCH_ASSOC)) {
        $id_proprio = $id_proprio_row['abonnement_id_compte'];
    }


    // Récupérer les logements du propriétaire
    $sql = "SELECT calendrier_date_jour,calendrier_logement_id,calendrier_motif_indisponibilite,logement_adresse,logement_ville,logement_code_postal,logement_accroche FROM alhaiz_breizh._calendrier as calendrier INNER JOIN alhaiz_breizh._logement as logement ON calendrier.calendrier_logement_id = logement.logement_id WHERE logement.logement_compte_id_proprio = :id_proprio ORDER BY calendrier_logement_id, calendrier_date_jour;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_proprio', $id_proprio);
    $stmt->execute();

    $calendrier = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $res = "SELECT logement_id,compte_civilite,compte_nom,compte_prenom,logement_adresse,logement_ville,logement_code_postal,devis_date_debut,devis_date_fin FROM alhaiz_breizh._reservation AS r JOIN alhaiz_breizh._logement AS l ON r.reservation_logement_id = l.logement_id JOIN alhaiz_breizh._devis AS d ON r.reservation_devis_id = d.devis_id JOIN alhaiz_breizh._client AS cl ON d.devis_id_compte_client = cl.compte_id WHERE d.devis_statut = 'PPC' AND logement_compte_id_proprio =:id_proprio;";

    $stmt1 = $pdo->prepare($res);
    $stmt1->bindParam(':id_proprio', $id_proprio);
    $stmt1->execute();

    $resa = $stmt1->fetchAll(PDO::FETCH_ASSOC);





} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
/*
foreach($calendrier as $row){
    $time =strtotime($row['calendrier_date_jour']);
    $date = date('Y-M-d',$time);
    
    $datediff = $now - $your_date;

    echo round($datediff / (60 * 60 * 24));


}*/



$evenements = array();
$length = count($calendrier);

// Initialisation de l'événement en cours
$evenement = null;

for ($i = 0; $i < $length; ++$i) {
    $currentEvent = $calendrier[$i];
    $date = strtotime($currentEvent["calendrier_date_jour"]);

    // Si c'est le début d'un nouvel événement ou si l'événement est vide
    if ($evenement === null || (isset($prevDate) && $date > $prevDate + 86400) || $currentEvent['calendrier_logement_id'] !== $evenement['logement_id'] || $currentEvent['calendrier_motif_indisponibilite'] !== $evenement['motif']) {
        if ($evenement !== null) {
            // S'il y avait déjà un événement en cours, l'ajouter à la liste des événements
            $evenements[] = $evenement;
        }

        // Initialiser un nouvel événement
        $evenement = array(
            'date_debut' => $currentEvent["calendrier_date_jour"],
            'date_fin' => $currentEvent["calendrier_date_jour"],
            'logement_id' => $currentEvent["calendrier_logement_id"],
            'lieu' => $currentEvent['logement_adresse'] . ' ' . $currentEvent['logement_ville'] . ' ' . $currentEvent['logement_code_postal'],

            'motif' => $currentEvent['calendrier_motif_indisponibilite']
        );
    } else {
        // Mettre à jour la date de fin de l'événement en cours
        $evenement['date_fin'] = $currentEvent["calendrier_date_jour"];
    }

    // Mémoriser la date précédente pour la comparaison
    $prevDate = $date;
}

// Ajouter le dernier événement à la liste des événements s'il existe toujours après la boucle
if ($evenement !== null) {
    $evenements[] = $evenement;
}

// Afficher les événements
/*
echo "<pre>";
print_r($evenements);
echo "</pre>";

echo "<pre>";
print_r($resa);
echo "</pre>";
*/

function generateCalendar($events, $resa)
{
    // Début de la chaîne de caractères contenant les informations du calendrier
    $calendarData = "BEGIN:VCALENDAR\r\n";
    $calendarData .= "VERSION:2.0\r\n";
    $calendarData .= "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\r\n";

    // Événements

    foreach ($events as $row) {
        $date_debut = new DateTime($row['date_debut']);
        $date_fin = new DateTime($row['date_fin']);

        // Formater les dates au format requis pour iCalendar (avec heure)
        $dtstart = $date_debut->format('Ymd\THis\Z');
        $dtend = $date_fin->format('Ymd\THis\Z');


        $calendarData .= "BEGIN:VEVENT\r\n";
        $calendarData .= "UID:" . uniqid() . "\r\n"; // Identifiant unique de l'événement
        $calendarData .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n"; // Date de création de l'événement (format UTC)
        $calendarData .= "DTSTART:" . $dtstart . "\r\n"; // Date de début de l'événement
        $calendarData .= "DTEND:" . $dtend . "\r\n"; // Date de fin de l'événement
        $calendarData .= "SUMMARY:" . $row['motif'] . "\r\n"; // Titre de l'événement
        //$calendarData .= "DESCRIPTION:" . 'AUCUNE DESCRIPTION' . "\r\n"; // Description de l'événement
        $calendarData .= "LOCATION:" . $row['lieu'] . "\r\n"; // Lieu de l'événement
        $calendarData .= "END:VEVENT\r\n";

    }
    if (isset($resa)) {

        foreach ($resa as $reservation) {
            $date_debut1 = new DateTime($reservation['devis_date_debut']);
            $date_fin1 = new DateTime($reservation['devis_date_fin']);

            // Formater les dates au format requis pour iCalendar (avec heure)
            $dtstart1 = $date_debut1->format('Ymd\THis\Z');
            $dtend1 = $date_fin1->format('Ymd\THis\Z');


            $locataire = $reservation['compte_civilite'] . ' ' . $reservation['compte_nom'] . ' ' . $reservation['compte_prenom'];
            $lieu = $reservation['logement_adresse'] . ' ' . $reservation['logement_ville'] . ' ' . $reservation['logement_code_postal'];
            $calendarData .= "BEGIN:VEVENT\r\n";
            $calendarData .= "UID:" . uniqid() . "\r\n"; // Identifiant unique de l'événement
            $calendarData .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n"; // Date de création de l'événement (format UTC)
            $calendarData .= "DTSTART:" . $dtstart1 . "\r\n"; // Date de début de l'événement
            $calendarData .= "DTEND:" . $dtend1 . "\r\n"; // Date de fin de l'événement
            $calendarData .= "SUMMARY:" . 'Réservation logement' . "\r\n"; // Titre de l'événement
            $calendarData .= "DESCRIPTION:" . $locataire . "\r\n"; // Description de l'événement
            $calendarData .= "LOCATION:" . $lieu . "\r\n"; // Lieu de l'événement
            $calendarData .= "END:VEVENT\r\n";

        }




    }


    // Fin du fichier iCalendar
    $calendarData .= "END:VCALENDAR\r\n";
    $filename = 'abonnement_' . $_GET['key'] . '.ics';
    // Entêtes pour indiquer le type de fichier
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    // Envoi des données du calendrier
    echo $calendarData;

}




// Retourner l'URL d'abonnement au calendrier
//generateEvent($calendarUID,$startDate,$endDate,$summary,$description,$location);

generateCalendar($evenements, $resa);


?>