<?php

function generateEvent($uid, $dtstart, $dtend, $summary, $description, $location)
{
    // Entête pour indiquer le type de fichier
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename=event.ics');

    // Début du fichier iCalendar
    echo "BEGIN:VCALENDAR\r\n";
    echo "VERSION:2.0\r\n";
    echo "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\r\n";

    // Événement
    echo "BEGIN:VEVENT\r\n";
    echo "UID:" . $uid . "\r\n"; // Identifiant unique de l'événement
    echo "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n"; // Date de création de l'événement (format UTC)
    echo "DTSTART:" . $dtstart . "\r\n"; // Date de début de l'événement
    echo "DTEND:" . $dtend . "\r\n"; // Date de fin de l'événement
    echo "SUMMARY:" . $summary . "\r\n"; // Titre de l'événement
    echo "DESCRIPTION:" . $description . "\r\n"; // Description de l'événement
    echo "LOCATION:" . $location . "\r\n"; // Lieu de l'événement
    echo "END:VEVENT\r\n";

    // Fin du fichier iCalendar
    echo "END:VCALENDAR\r\n";
}

// Exemple d'utilisation de la fonction
generateEvent(
    "12345", // UID de l'événement (doit être unique)
    "20240229T080000", // Date et heure de début de l'événement (exemple: 29 Février 2024 à 08:00)
    "20240229T090000", // Date et heure de fin de l'événement (exemple: 29 Février 2024 à 09:00)
    "Réunion importante", // Titre de l'événement
    "Réunion hebdomadaire avec l'équipe", // Description de l'événement
    "Salle de réunion A" // Lieu de l'événement
);

?>