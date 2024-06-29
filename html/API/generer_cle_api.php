<?php 
session_start();
include('../../libs/connect_params.php');


try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO alhaiz_breizh._cles_api (cles_api_id_compte, cles_api_normal,cles_api_consultation_logement, cles_api_verif_dispo, cles_api_mise_indispo,cles_api_privilege) 
                        VALUES (:id_compte,:cle,:droit_consul,:droit_verif,:droit_modif,:droit_privi)");
    
    $api_key = uniqid('', true);

    
    $stmt->bindParam(':cle',$api_key);
    $stmt->bindParam(':id_compte', $_SESSION['user_id']);
    $stmt->bindValue(':droit_consul',false, PDO::PARAM_BOOL);
    $stmt->bindValue(':droit_modif',false, PDO::PARAM_BOOL);
    $stmt->bindValue(':droit_verif', false, PDO::PARAM_BOOL);
    $stmt->bindValue(':droit_privi', false, PDO::PARAM_BOOL);
    $stmt->execute();

    $sqlSelect="SELECT cles_api_normal
    FROM alhaiz_breizh._cles_api
    WHERE cles_api_id_compte = :id_compte";
   
    
    $stmt1 = $pdo->prepare($sqlSelect);
    $stmt1->bindParam(':id_compte', $_SESSION['user_id']);
    
    $stmt1->execute();
    
    $result = $stmt1->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $api_key = $result['cles_api_normal'];
        
    }

    $json_data = json_encode(array('success' => true, 'key' => $api_key), JSON_PRETTY_PRINT);
    echo $json_data;
} catch (PDOException $e) {
    // En cas d'erreur, retourner une réponse d'échec
    $json_error = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_error;
}




?>