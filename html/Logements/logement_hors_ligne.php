<?php
session_start();
$toto=false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['logement_id']) ) {
        include('../../libs/connect_params.php');

        $logement_id = $_POST['logement_id'];
        try {
            $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql ="UPDATE alhaiz_breizh._logement SET logement_statut_ligne = false where logement_id = :logement_id"; 
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':logement_id', $logement_id);
            $stmt->execute();
            //header("Location: ../Logements/logement.php");
            $json_data = json_encode(array('success' => true));
            echo $json_data;
        } catch (PDOException $e) { 
            $json_data = json_encode(array('success' => false, 'error' => $e->getMessage()));
            echo $json_data;
        }
    } else {
        echo "Ce script doit être appelé via une soumission de formulaire POST.";
    }   

}