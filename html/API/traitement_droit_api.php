<?php

include('../../libs/connect_params.php');

try {


    $cles_api_id = $_POST['cle_id'];
    $cle_droit = $_POST['droit'];
    $cle_val = $_POST['val'];

    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if($cle_val == 'true' && $cle_droit == 'cles_api_consultation_logement'){
        $update = "UPDATE alhaiz_breizh._cles_api set cles_api_consultation_logement = true WHERE cles_api_id = :cle_id";
    }else if($cle_val == 'false' && $cle_droit == 'cles_api_consultation_logement'){
        $update = "UPDATE alhaiz_breizh._cles_api set cles_api_consultation_logement = false WHERE cles_api_id = :cle_id";
    }else if($cle_val == 'true' && $cle_droit == 'cles_api_verif_dispo'){
        $update = "UPDATE alhaiz_breizh._cles_api set cles_api_verif_dispo = true WHERE cles_api_id = :cle_id";
    }else if($cle_val == 'false' && $cle_droit == 'cles_api_verif_dispo'){
        $update = "UPDATE alhaiz_breizh._cles_api set cles_api_verif_dispo = false WHERE cles_api_id = :cle_id";
    }else if($cle_val == 'true' && $cle_droit == 'cles_api_mise_indispo'){
        $update = "UPDATE alhaiz_breizh._cles_api set cles_api_mise_indispo = true WHERE cles_api_id = :cle_id";
    }else if($cle_val == 'false' && $cle_droit == 'cles_api_mise_indispo'){
        $update = "UPDATE alhaiz_breizh._cles_api set cles_api_mise_indispo = false WHERE cles_api_id = :cle_id";
    }else if($cle_val == 'true' && $cle_droit == 'cles_api_apirator'){
        $update = "UPDATE alhaiz_breizh._cles_api set cles_api_apirator = true WHERE cles_api_id = :cle_id";
    }else if($cle_val == 'false' && $cle_droit == 'cles_api_apirator'){
        $update = "UPDATE alhaiz_breizh._cles_api set cles_api_apirator = false WHERE cles_api_id = :cle_id";
    }

    $stmt = $pdo->prepare($update);

    $stmt->bindParam(':cle_id', $cles_api_id);
    
    $stmt->execute();
    

    $json_data = json_encode(array('success' => true, 'events' => 'oui'));
    echo $json_data;
} catch (PDOException $e) { 
    $json_data = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_data;
}

?>