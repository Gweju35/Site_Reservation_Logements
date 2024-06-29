<?php

include('../../libs/connect_params.php');

try {

    print_r($_POST);

    $cles_abo_id = $_POST['cle_id'];
    $cle_droit = $_POST['droit'];
    $cle_val = $_POST['val'];

    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if($cle_val == 'true' && $cle_droit == 'abonnement_reservation'){
        $update = "UPDATE alhaiz_breizh._abonnement set abonnement_reservation = true WHERE abonnement_cle= :cle_id";
    }else if($cle_val == 'false' && $cle_droit == 'abonnement_reservation'){
        $update = "UPDATE alhaiz_breizh._abonnement set abonnement_reservation = false WHERE abonnement_cle = :cle_id";
    }else if($cle_val == 'true' && $cle_droit == 'abonnement_devis'){
        $update = "UPDATE alhaiz_breizh._abonnement set abonnement_devis = true WHERE abonnement_cle = :cle_id";
    }else if($cle_val == 'false' && $cle_droit == 'abonnement_devis'){
        $update = "UPDATE alhaiz_breizh._abonnement set abonnement_devis = false WHERE abonnement_cle = :cle_id";
    }else if($cle_val == 'true' && $cle_droit == 'abonnement_raison_perso'){
        $update = "UPDATE alhaiz_breizh._abonnement set abonnement_raison_perso = true WHERE abonnement_cle = :cle_id";
    }else if($cle_val == 'false' && $cle_droit == 'abonnement_raison_perso'){
        $update = "UPDATE alhaiz_breizh._abonnement set abonnement_raison_perso = false WHERE abonnement_cle = :cle_id";
    }

    $stmt = $pdo->prepare($update);

    $stmt->bindParam(':cle_id', $cles_abo_id);
    
    $stmt->execute();
    

    $json_data = json_encode(array('success' => true, 'events' => 'oui'));
    echo $json_data;
} catch (PDOException $e) { 
    $json_data = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_data;
}

?>