<?php

include('../../libs/connect_params.php');

try {

    print_r($_POST);

    $cles_abo_id = $_POST['cle_id'];
    $val =$_POST['date'];


    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE alhaiz_breizh._abonnement set abonnement_date_fin = :val WHERE abonnement_cle= :cle_id");
    $stmt->bindParam(':cle_id', $cles_abo_id);
    
    $stmt->bindParam(':val', $val);
    $stmt->execute();



    $json_data = json_encode(array('success' => true, 'event' => $val));
    echo $json_data;
} catch (PDOException $e) { 
    $json_data = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_data;
}

?>