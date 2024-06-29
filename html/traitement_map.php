<?php
include('../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les logements du propriétaire
    $sql = "SELECT * FROM alhaiz_breizh._logement WHERE logement_statut_ligne = true";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();


    $logements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = array();
    
    //print_r($logements);
    foreach ($logements as $logement) {
        if(isset($logement['logement_coordonnees_gps'])){
            $coGps= explode(",", $logement['logement_coordonnees_gps']);
            //print_r($coGps);
            
            $event = array(
                'accroche' =>  $logement['logement_accroche'],
                'prix' => $logement['logement_prix_nuit_base'],
                'photo' => $logement['logement_photo'],
                'ville' => $logement['logement_ville'],
                'code_postal' => $logement['logement_code_postal'],
                'coGps' => $coGps,
                'id' => $logement['logement_id']
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
