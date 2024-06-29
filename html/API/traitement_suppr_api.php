<?php

include('../../libs/connect_params.php');

try {

    print_r($_POST);

    $cles_api_id = $_POST['cle_id'];

    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM alhaiz_breizh._cles_api WHERE cles_api_id = :cle_id;");
    $stmt->bindParam(':cle_id', $cles_api_id);
    $stmt->execute();



    $json_data = json_encode(array('success' => true, 'events' => 'oui'));
    echo $json_data;
} catch (PDOException $e) { 
    $json_data = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_data;
}

?>