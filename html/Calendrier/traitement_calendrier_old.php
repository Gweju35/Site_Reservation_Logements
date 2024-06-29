<?php
session_start();
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT r.*, l.*, d.*
    FROM alhaiz_breizh._reservation AS r
    JOIN alhaiz_breizh._logement AS l ON r.reservation_logement_id = l.logement_id
    JOIN alhaiz_breizh._devis AS d ON r.reservation_devis_id = d.devis_id
    WHERE d.devis_statut = 'PPC'
    AND l.logement_compte_id_proprio = :compte_id;";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':compte_id', $_SESSION['user_id']);
    $stmt->execute();

    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = array();
    
    foreach ($reservations as $reservation) {
        $event = array(
            'title' =>  $reservation['logement_description'],
            'start' => $reservation['devis_date_debut'] . 'T16:00:00', // Ajout de l'heure par défaut
            'end' => $reservation['devis_date_fin'] . 'T11:00:00',   // Ajout de l'heure par défaut
        );

        $events[] = $event;
    }
    
    $json_data = json_encode(array('success' => true, 'events' => $events), JSON_PRETTY_PRINT);
    echo $json_data;
} catch (PDOException $e) {
    // En cas d'erreur, retourner une réponse d'échec
    $json_error = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_error;
}
?>
