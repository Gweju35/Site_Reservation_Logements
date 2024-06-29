<?php
include('../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les logements du propriétaire
        $sql = 'SELECT avis_id_logement, ROUND(AVG(avis_note)::numeric, 1) AS moyenne_notes FROM alhaiz_breizh._avis GROUP BY avis_id_logement;';
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute();

        $avis_moy = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //print_r($avis_moy[0]);
       
    
    $json_data = json_encode(array('success' => true, 'events' => $avis_moy), JSON_PRETTY_PRINT);
    echo $json_data;
} catch (PDOException $e) {
    // En cas d'erreur, retourner une réponse d'échec
    $json_error = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_error;
}
?>
 